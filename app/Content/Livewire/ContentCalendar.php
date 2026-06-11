<?php

namespace App\Content\Livewire;

use App\Content\Enums\ContentStatus;
use App\Content\Enums\ContentType;
use App\Content\Models\AdjustmentLog;
use App\Content\Models\Approval;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
use App\Content\Models\TiktokQc;
use App\Enums\ApprovalStatus;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ContentCalendar extends Component
{
    use WithFileUploads, WithPagination;

    public $selectedPlatform = '';

    public $selectedStatus = '';

    public $selectedPriority = '';

    public $search = '';

    public $viewMode = 'kanban';

    public $currentMonth = null;

    public $currentYear = null;

    public $showFilters = false;

    public $showModal = false;

    public $showDeleteModal = false;

    public $modalMode = 'create';

    public $contentId = null;

    public $deleteContentId = null;

    public $unscheduledSearch = '';

    public $unscheduledAssignee = '';

    public $unscheduledType = '';

    public $unscheduledStatus = '';

    public $showUnscheduledFilters = false;

    public $showApproveModal = false;

    public $approveContentId = null;

    public $approveStage = null;

    public $approveNotes = '';

    public $showQcModal = false;

    public $qcContentId = null;

    public $qc = [
        'hook_strong' => false,
        'cta_clear' => false,
        'audio_clear' => false,
        'visual_quality' => false,
        'caption_complete' => false,
        'product_visible' => false,
        'duration_appropriate' => false,
        'branding_included' => false,
        'no_sensitive_content' => false,
        'notes' => '',
    ];

    public $showBriefModal = false;

    public $briefContent = null;

    public $showVersionModal = false;

    public $versionContentId = null;

    public $showAdjustmentModal = false;

    public $adjustmentContentId = null;

    public $finalAsset;

    public $thumbnail;

    public $existingFinalAsset = null;

    public $existingThumbnail = null;

    public $form = [
        'platform_id' => '',
        'product_id' => '',
        'campaign_id' => '',
        'theme' => '',
        'caption' => '',
        'description' => '',
        'priority' => 'medium',
        'publish_date' => '',
        'deadline_produksi' => '',
        'pic_copy_id' => '',
        'pic_visual_id' => '',
        'pic_video_id' => '',
        'copy_brief' => '',
        'visual_brief' => '',
        'video_brief' => '',
    ];

    protected $rules = [
        'form.platform_id' => 'required|exists:platforms,id',
        'form.theme' => 'required|string|max:200',
        'form.priority' => 'required|in:high,medium,low',
        'form.publish_date' => 'nullable|date',
        'form.deadline_produksi' => 'nullable|date',
    ];

    protected $validationAttributes = [
        'form.platform_id' => 'Platform',
        'form.theme' => 'Tema',
        'form.priority' => 'Priority',
        'form.publish_date' => 'Tanggal Publish',
        'form.deadline_produksi' => 'Deadline Produksi',
    ];

    public function updatedShowModal($value)
    {
        if (! $value) {
            $this->reset('form');
            $this->resetValidation();
        }
    }

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function render()
    {
        $query = Content::with(['platform', 'product', 'campaign', 'picCopy', 'picVisual', 'picVideo', 'approvals', 'tiktokQc']);

        if ($this->selectedPlatform) {
            $query->where('platform_id', $this->selectedPlatform);
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->selectedPriority) {
            $query->where('priority', $this->selectedPriority);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('content_code', 'like', '%'.$this->search.'%')
                    ->orWhere('theme', 'like', '%'.$this->search.'%')
                    ->orWhere('caption', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->viewMode === 'calendar') {
            $query->whereYear('publish_date', $this->currentYear)
                ->whereMonth('publish_date', $this->currentMonth);
        }

        $allContents = $query->orderBy('publish_date', 'desc')->get();

        $columns = [
            'todo' => ['label' => 'To Do', 'statuses' => ['draft']],
            'in_progress' => ['label' => 'In Progress', 'statuses' => ['in_production']],
            'in_review' => ['label' => 'In Review', 'statuses' => ['ready_review']],
            'done' => ['label' => 'Done', 'statuses' => ['approved', 'scheduled', 'published']],
        ];

        $kanbanColumns = [];
        foreach ($columns as $key => $column) {
            $kanbanColumns[$key] = [
                'label' => $column['label'],
                'items' => $allContents->filter(fn ($c) => in_array($c->status->value, $column['statuses'])),
            ];
        }

        $contents = $this->viewMode !== 'kanban'
            ? $query->paginate(15)
            : $query->paginate(999);

        $userRole = Auth::user()?->roles->first()?->name;

        $unscheduledQuery = Content::with(['platform', 'picCopy'])
            ->whereNull('publish_date');

        if ($this->unscheduledSearch) {
            $unscheduledQuery->where(function ($q) {
                $q->where('content_code', 'like', '%'.$this->unscheduledSearch.'%')
                    ->orWhere('theme', 'like', '%'.$this->unscheduledSearch.'%');
            });
        }

        if ($this->unscheduledAssignee) {
            $unscheduledQuery->where(function ($q) {
                $q->where('pic_copy_id', $this->unscheduledAssignee)
                    ->orWhere('pic_visual_id', $this->unscheduledAssignee)
                    ->orWhere('pic_video_id', $this->unscheduledAssignee);
            });
        }

        if ($this->unscheduledType) {
            $unscheduledQuery->where('content_type', $this->unscheduledType);
        }

        if ($this->unscheduledStatus) {
            $unscheduledQuery->where('status', $this->unscheduledStatus);
        }

        $unscheduledItems = $unscheduledQuery->orderBy('created_at', 'desc')->get();
        $assignees = User::where('is_active', true)->get();
        $contentTypes = ContentType::options();

        $platforms = Platform::where('is_active', true)->get();
        $statusOptions = ContentStatus::options();

        return view('content.content-calendar', [
            'contents' => $contents,
            'platforms' => $platforms,
            'statusOptions' => $statusOptions,
            'canCreate' => Auth::user()?->hasRole('CSP'),
            'kanbanColumns' => $kanbanColumns,
            'unscheduledItems' => $unscheduledItems,
            'assignees' => $assignees,
            'contentTypes' => $contentTypes,
            'userRole' => $userRole,
        ])->layout('layouts.admin', ['title' => 'Content Calendar']);
    }

    public function moveToColumn($contentId, $column)
    {
        $statusMap = [
            'todo' => 'draft',
            'in_progress' => 'in_production',
            'in_review' => 'ready_review',
            'done' => 'approved',
        ];

        $content = Content::findOrFail($contentId);
        $targetStatus = $statusMap[$column] ?? null;

        if ($targetStatus) {
            $content->status = $targetStatus;
            $content->save();
        }
    }

    public function openCreateModal()
    {
        $this->reset('form');
        $this->resetValidation();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $content = Content::findOrFail($id);
        $this->contentId = $id;
        $this->modalMode = 'edit';

        $raw = $content->only([
            'platform_id', 'product_id', 'campaign_id', 'theme', 'caption',
            'description', 'pic_copy_id', 'pic_visual_id', 'pic_video_id',
            'copy_brief', 'visual_brief', 'video_brief',
        ]);
        $raw['priority'] = $content->priority?->value ?? 'medium';
        $raw['publish_date'] = $content->publish_date?->format('Y-m-d') ?? '';
        $raw['deadline_produksi'] = $content->deadline_produksi?->format('Y-m-d') ?? '';
        $this->form = $raw;
        $this->existingFinalAsset = $content->final_asset_link;
        $this->existingThumbnail = $content->thumbnail_link;
        $this->resetValidation();

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = collect($this->form)->map(fn ($v) => $v === '' ? null : $v)->all();

        if ($this->finalAsset) {
            $data['final_asset_link'] = $this->finalAsset->store('assets', 'public');
        }

        if ($this->thumbnail) {
            $data['thumbnail_link'] = $this->thumbnail->store('thumbnails', 'public');
        }

        if ($this->modalMode === 'create') {
            $content = Content::create($data);

            $content->version = 1;
            $content->save();

            ContentVersion::create([
                'content_id' => $content->id,
                'version' => 1,
                'data' => $content->fresh()->toArray(),
                'created_by' => Auth::id(),
            ]);

            flash()->success('Konten berhasil dibuat!');
        } else {
            $content = Content::findOrFail($this->contentId);
            $original = $content->fresh()->toArray();
            $content->update($data);

            if ($this->finalAsset || $this->thumbnail) {
                $content->version = $content->version + 1;
                $content->save();
            }

            $versionIncreased = false;
            $trackedFields = ['theme', 'caption', 'description', 'content_type', 'priority',
                'publish_date', 'deadline_produksi', 'pic_copy_id', 'pic_visual_id', 'pic_video_id',
                'copy_brief', 'visual_brief', 'video_brief', 'final_asset_link', 'thumbnail_link'];

            foreach ($trackedFields as $field) {
                $old = $original[$field] ?? null;
                $new = $data[$field] ?? $content->$field ?? null;

                if ($old !== $new) {
                    AdjustmentLog::create([
                        'content_id' => $content->id,
                        'user_id' => Auth::id(),
                        'field' => $field,
                        'old_value' => is_bool($old) ? ($old ? '1' : '0') : $old,
                        'new_value' => is_bool($new) ? ($new ? '1' : '0') : $new,
                    ]);
                    $versionIncreased = true;
                }
            }

            if ($versionIncreased) {
                $content->version = $content->version + 1;
                $content->save();

                ContentVersion::create([
                    'content_id' => $content->id,
                    'version' => $content->version,
                    'data' => $content->fresh()->toArray(),
                    'created_by' => Auth::id(),
                ]);
            }

            flash()->success('Konten berhasil diupdate!');
        }

        $this->showModal = false;
        $this->reset('form', 'finalAsset', 'thumbnail', 'existingFinalAsset', 'existingThumbnail');
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('form');
        $this->resetValidation();
    }

    public function confirmDelete($id)
    {
        $this->deleteContentId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->deleteContentId = null;
        $this->showDeleteModal = false;
    }

    public function executeDelete()
    {
        $content = Content::findOrFail($this->deleteContentId);

        if (Auth::user()->hasRole('CSP') && $content->status === ContentStatus::DRAFT) {
            $content->delete();
            flash()->success('Konten berhasil dihapus!');
        } else {
            flash()->error('Tidak bisa menghapus konten ini!');
        }

        $this->cancelDelete();
    }

    public function changeStatus($id, $newStatus)
    {
        $content = Content::findOrFail($id);

        $content->status = $newStatus;
        $content->save();

        flash()->success('Status berhasil diubah!');
    }

    public function setPublishDate($id, $date)
    {
        $content = Content::findOrFail($id);
        $content->publish_date = $date ?: null;
        $content->save();

        flash()->success('Publish date berhasil diatur!');
    }

    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
    }

    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
    }

    public function openBriefModal($id)
    {
        $this->briefContent = Content::with(['platform', 'product', 'campaign'])->findOrFail($id);
        $this->showBriefModal = true;
    }

    public function closeBriefModal()
    {
        $this->showBriefModal = false;
        $this->briefContent = null;
    }

    public function openQcModal($id)
    {
        $this->qcContentId = $id;
        $this->resetQcForm();

        $existing = TiktokQc::where('content_id', $id)->first();
        if ($existing) {
            $this->qc = $existing->only([
                'hook_strong', 'cta_clear', 'audio_clear', 'visual_quality',
                'caption_complete', 'product_visible', 'duration_appropriate',
                'branding_included', 'no_sensitive_content', 'notes',
            ]);
        }

        $this->showQcModal = true;
    }

    public function closeQcModal()
    {
        $this->showQcModal = false;
        $this->qcContentId = null;
        $this->resetQcForm();
    }

    public function resetQcForm()
    {
        $this->qc = [
            'hook_strong' => false,
            'cta_clear' => false,
            'audio_clear' => false,
            'visual_quality' => false,
            'caption_complete' => false,
            'product_visible' => false,
            'duration_appropriate' => false,
            'branding_included' => false,
            'no_sensitive_content' => false,
            'notes' => '',
        ];
    }

    public function saveQc()
    {
        $this->validate([
            'qc.notes' => 'nullable|string|max:1000',
        ]);

        $data = collect($this->qc)->map(fn ($v) => $v === '' ? null : $v)->all();
        $data['checked_by'] = Auth::id();
        $data['checked_at'] = now();
        $data['status'] = collect($this->qc)->except('notes')->every(fn ($v) => $v === true) ? 'passed' : 'need_revision';

        TiktokQc::updateOrCreate(
            ['content_id' => $this->qcContentId],
            $data,
        );

        $allPassed = collect($this->qc)->except('notes')->every(fn ($v) => $v === true);

        if ($allPassed) {
            flash()->success('QC TikTok selesai — semua checklist terpenuhi!');
        } else {
            flash()->warning('QC TikTok perlu perbaikan — ada item yang belum terpenuhi.');
        }

        $this->closeQcModal();
    }

    public function submitForApproval($id)
    {
        $content = Content::findOrFail($id);

        $content->status = ContentStatus::READY_REVIEW;
        $content->save();

        foreach (['mc_bm', 'legal'] as $stage) {
            Approval::updateOrCreate(
                ['content_id' => $content->id, 'stage' => $stage],
                ['status' => ApprovalStatus::PENDING->value, 'approver_id' => null, 'notes' => null],
            );
        }

        flash()->success('Konten dikirim untuk approval!');
    }

    public function confirmApprove($id, $stage)
    {
        $this->approveContentId = $id;
        $this->approveStage = $stage;
        $this->approveNotes = '';
        $this->showApproveModal = true;
    }

    public function cancelApprove()
    {
        $this->showApproveModal = false;
        $this->approveContentId = null;
        $this->approveStage = null;
        $this->approveNotes = '';
    }

    public function approveContent()
    {
        $approval = Approval::where('content_id', $this->approveContentId)
            ->where('stage', $this->approveStage)
            ->firstOrFail();

        $approval->status = ApprovalStatus::APPROVED->value;
        $approval->approver_id = Auth::id();
        $approval->notes = $this->approveNotes;
        $approval->save();

        $allApproved = Approval::where('content_id', $this->approveContentId)
            ->where('status', '!=', ApprovalStatus::APPROVED->value)
            ->doesntExist();

        if ($allApproved) {
            $content = Content::findOrFail($this->approveContentId);
            $content->status = ContentStatus::APPROVED;
            $content->save();
        }

        flash()->success('Konten berhasil di-approve!');
        $this->cancelApprove();
    }

    public function reviseContent()
    {
        $approval = Approval::where('content_id', $this->approveContentId)
            ->where('stage', $this->approveStage)
            ->firstOrFail();

        $approval->status = ApprovalStatus::REVISION->value;
        $approval->approver_id = Auth::id();
        $approval->notes = $this->approveNotes;
        $approval->save();

        $content = Content::findOrFail($this->approveContentId);
        $content->status = ContentStatus::IN_PRODUCTION;
        $content->save();

        flash()->success('Revisi diminta. Konten dikembalikan ke In Production.');
        $this->cancelApprove();
    }

    public function resetFilters()
    {
        $this->reset(['selectedPlatform', 'selectedStatus', 'selectedPriority', 'search']);
    }

    public function openVersionModal($id)
    {
        $this->versionContentId = $id;
        $this->showVersionModal = true;
    }

    public function closeVersionModal()
    {
        $this->showVersionModal = false;
        $this->versionContentId = null;
    }

    public function openAdjustmentModal($id)
    {
        $this->adjustmentContentId = $id;
        $this->showAdjustmentModal = true;
    }

    public function closeAdjustmentModal()
    {
        $this->showAdjustmentModal = false;
        $this->adjustmentContentId = null;
    }

    public function getVersionHistoryProperty()
    {
        if (! $this->versionContentId) {
            return collect();
        }

        return ContentVersion::with('creator')
            ->where('content_id', $this->versionContentId)
            ->orderBy('version', 'desc')
            ->get();
    }

    public function getAdjustmentLogsProperty()
    {
        if (! $this->adjustmentContentId) {
            return collect();
        }

        return AdjustmentLog::with('user')
            ->where('content_id', $this->adjustmentContentId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
