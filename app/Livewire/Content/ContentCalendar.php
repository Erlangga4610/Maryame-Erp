<?php

namespace App\Livewire\Content;

use App\Content\Enums\ContentStatus;
use App\Content\Enums\ContentType;
use App\Content\Models\Adjustment;
use App\Content\Models\AdjustmentLog;
use App\Content\Models\Approval;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
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
    use Traits\WithApprovalPipeline;
    use Traits\WithPublishingReporting;
    use Traits\WithAssetManagement;
    use Traits\WithCapacityPlanning;

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

    public $showBriefModal = false;

    public $briefContent = null;

    public $form = [
        'platform_id' => '',
        'product_id' => '',
        'campaign_id' => '',
        'content_group_id' => '',
        'theme' => '',
        'caption' => '',
        'description' => '',
        'priority' => 'rutin',
        'publish_date' => '',
        'deadline_produksi' => '',
        'pic_copy_id' => '',
        'pic_visual_id' => '',
        'pic_video_id' => '',
        'copy_brief' => '',
        'visual_brief' => '',
        'video_brief' => '',
        'angle' => '',
        'positioning' => '',
        'target_audience' => '',
        'key_message' => '',
        'tone' => '',
        'aspect_ratio' => '',
        'resolution' => '',
        'duration' => '',
        'format_file' => '',
        'hashtag' => '',
        'audio_guidance' => '',
        'originality_instruction' => '',
        'thumbnail_note' => '',
        'est_copy_hours' => '',
        'est_visual_hours' => '',
        'est_video_hours' => '',
    ];

    public $isBriefFinal = false;

    public $editingContentStatus = null;

    public $adjustmentType = 'minor';

    public $adjustmentReason = '';

    public $newContentGroupName = '';

    public $fastTrack = false;

    protected $rules = [
        'form.platform_id' => 'required|exists:platforms,id',
        'form.theme' => 'required|string|max:200',
        'form.priority' => 'required|in:rutin,campaign,spontan',
        'form.publish_date' => 'nullable|date',
        'form.deadline_produksi' => 'nullable|date',
        'form.est_copy_hours' => 'nullable|numeric|min:0|max:999',
        'form.est_visual_hours' => 'nullable|numeric|min:0|max:999',
        'form.est_video_hours' => 'nullable|numeric|min:0|max:999',
    ];

    protected $validationAttributes = [
        'form.platform_id' => 'Platform',
        'form.theme' => 'Tema',
        'form.priority' => 'Priority',
        'form.publish_date' => 'Tanggal Publish',
        'form.deadline_produksi' => 'Deadline Produksi',
        'adjustmentReason' => 'Alasan Adjustment',
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
        $this->capacityWeekStart = now()->startOfWeek();
    }

    public function render()
    {
        $query = Content::with(['platform', 'product', 'campaign', 'contentGroup', 'picCopy', 'picVisual', 'picVideo', 'approvals', 'tiktokQc', 'brief']);

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
        $contentGroups = \App\Content\Models\ContentGroup::orderBy('name')->get();
        $statusOptions = ContentStatus::options();

        $authUser = Auth::user();

        return view('livewire.content.content-calendar', [
            'contents' => $contents,
            'platforms' => $platforms,
            'contentGroups' => $contentGroups,
            'statusOptions' => $statusOptions,
            'canCreate' => $authUser?->isSuperAdmin() || $authUser?->hasRole('CSP') || $authUser?->hasRole('CW'),
            'kanbanColumns' => $kanbanColumns,
            'unscheduledItems' => $unscheduledItems,
            'assignees' => $assignees,
            'contentTypes' => $contentTypes,
            'userRole' => $userRole,
            'isCsp' => $authUser?->isSuperAdmin() || $authUser?->hasRole('CSP'),
            'isSms' => $authUser?->isSuperAdmin() || $authUser?->hasRole('SMS'),
            'isCw' => $authUser?->isSuperAdmin() || $authUser?->hasRole('CW'),
            'isSuperAdmin' => $authUser?->isSuperAdmin(),
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

        $content = Content::with('brief')->findOrFail($contentId);
        $targetStatus = $statusMap[$column] ?? null;

        if ($targetStatus === 'in_production' && ! ($content->brief?->is_final ?? $content->is_brief_final)) {
            flash()->error('Brief harus difinalisasi terlebih dahulu sebelum produksi.');

            return;
        }

        if ($targetStatus) {
            $content->status = $targetStatus;
            $content->save();
        }
    }

    public function openCreateModal()
    {
        $this->reset('form', 'finalAsset', 'thumbnail', 'existingFinalAsset', 'existingThumbnail', 'editingContentStatus', 'adjustmentType', 'adjustmentReason', 'fastTrack');
        $this->resetValidation();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openFastTrackModal()
    {
        $this->reset('form', 'finalAsset', 'thumbnail', 'existingFinalAsset', 'existingThumbnail', 'editingContentStatus', 'adjustmentType', 'adjustmentReason');
        $this->resetValidation();
        $this->modalMode = 'create';
        $this->fastTrack = true;
        $this->form['priority'] = 'spontan';
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $content = Content::with('brief')->findOrFail($id);
        $this->contentId = $id;
        $this->modalMode = 'edit';

        $brief = $content->brief;

        $raw = $content->only([
            'platform_id', 'product_id', 'campaign_id', 'content_group_id', 'theme', 'caption',
            'description', 'pic_copy_id', 'pic_visual_id', 'pic_video_id',
            'est_copy_hours', 'est_visual_hours', 'est_video_hours',
        ]);

        $raw['copy_brief'] = $brief?->copy_brief ?? $content->copy_brief;
        $raw['visual_brief'] = $brief?->visual_brief ?? $content->visual_brief;
        $raw['video_brief'] = $brief?->video_brief ?? $content->video_brief;
        $raw['angle'] = $brief?->angle ?? $content->angle;
        $raw['positioning'] = $brief?->positioning ?? $content->positioning;
        $raw['target_audience'] = $brief?->target_audience ?? $content->target_audience;
        $raw['key_message'] = $brief?->key_message ?? $content->key_message;
        $raw['tone'] = $brief?->tone ?? $content->tone;
        $raw['aspect_ratio'] = $brief?->aspect_ratio ?? $content->aspect_ratio;
        $raw['resolution'] = $brief?->resolution ?? $content->resolution;
        $raw['duration'] = $brief?->duration ?? $content->duration;
        $raw['format_file'] = $brief?->format_file ?? $content->format_file;
        $raw['hashtag'] = $brief?->hashtag ?? $content->hashtag;
        $raw['audio_guidance'] = $brief?->audio_guidance ?? $content->audio_guidance;
        $raw['originality_instruction'] = $brief?->originality_instruction ?? $content->originality_instruction;
        $raw['thumbnail_note'] = $brief?->thumbnail_note ?? $content->thumbnail_note;

        $raw['priority'] = $content->priority?->value ?? 'rutin';
        $raw['publish_date'] = $content->publish_date?->format('Y-m-d') ?? '';
        $raw['deadline_produksi'] = $content->deadline_produksi?->format('Y-m-d') ?? '';
        $this->form = $raw;
        $this->isBriefFinal = $brief?->is_final ?? $content->is_brief_final;
        $this->editingContentStatus = $content->status->value;
        $this->existingFinalAsset = $content->final_asset_link;
        $this->existingThumbnail = $content->thumbnail_link;
        $this->adjustmentType = 'minor';
        $this->adjustmentReason = '';
        $this->resetValidation();

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->modalMode === 'edit' && $this->editingContentStatus !== 'draft' && $this->adjustmentType !== 'minor' && ! $this->adjustmentReason) {
            $this->addError('adjustmentReason', 'Alasan adjustment wajib diisi.');
            return;
        }

        $user = Auth::user();
        $data = collect($this->form)->map(fn ($v) => $v === '' ? null : $v)->all();

        if (($data['content_group_id'] ?? null) === '__create__') {
            if (! $this->newContentGroupName) {
                $this->addError('newContentGroupName', 'Nama group wajib diisi.');
                return;
            }
            $group = \App\Content\Models\ContentGroup::create([
                'name' => $this->newContentGroupName,
                'created_by' => $user->id,
            ]);
            $data['content_group_id'] = $group->id;
        }

        $strategicFields = ['angle', 'positioning', 'target_audience', 'key_message', 'tone'];
        $technicalFields = ['aspect_ratio', 'resolution', 'duration', 'format_file', 'hashtag',
            'audio_guidance', 'originality_instruction', 'thumbnail_note'];

        if ($this->finalAsset) {
            $ext = $this->finalAsset->getClientOriginalExtension();
            $cleanName = \Illuminate\Support\Str::slug(pathinfo($this->finalAsset->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $ext;
            $data['final_asset_link'] = $this->finalAsset->storeAs('assets', $cleanName, 'public');
        }

        if ($this->thumbnail) {
            $ext = $this->thumbnail->getClientOriginalExtension();
            $cleanName = \Illuminate\Support\Str::slug(pathinfo($this->thumbnail->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $ext;
            $data['thumbnail_link'] = $this->thumbnail->storeAs('thumbnails', $cleanName, 'public');
        }

        if ($this->modalMode === 'create') {
            $content = Content::create($data);

            $content->version = 1;
            $content->save();

            $this->saveBrief($content->id);

            ContentVersion::create([
                'content_id' => $content->id,
                'version' => 1,
                'data' => $content->fresh()->toArray(),
                'created_by' => $user->id,
            ]);

            if ($this->fastTrack) {
                $content->status = \App\Content\Enums\ContentStatus::IN_PRODUCTION;
                $content->save();

                Approval::updateOrCreate(
                    ['content_id' => $content->id, 'stage' => 'cw'],
                    ['status' => ApprovalStatus::PENDING->value, 'approver_id' => null, 'notes' => null],
                );

                flash()->success('Fast-Track: konten langsung masuk In Production!');
            } else {
                flash()->success('Konten berhasil dibuat!');
            }
        } else {
            $content = Content::findOrFail($this->contentId);

            if ($user->isSuperAdmin()) {
                // Super Admin can edit everything regardless of brief_final
            } elseif ($content->is_brief_final) {
                foreach ($strategicFields as $f) {
                    unset($data[$f]);
                }
                foreach ($technicalFields as $f) {
                    unset($data[$f]);
                }
                unset($data['copy_brief'], $data['visual_brief'], $data['video_brief']);
            } else {
                if (! $user->hasRole('CSP')) {
                    foreach ($strategicFields as $f) {
                        unset($data[$f]);
                    }
                    unset($data['copy_brief']);
                }
                if (! $user->hasRole('SMS')) {
                    foreach ($technicalFields as $f) {
                        unset($data[$f]);
                    }
                    unset($data['visual_brief'], $data['video_brief']);
                }
            }

            $isAdjustment = $content->status->value !== 'draft';
            $original = $content->fresh()->toArray();

            if ($isAdjustment && $this->adjustmentType === 'major') {
                Adjustment::create([
                    'content_id' => $content->id,
                    'type' => 'major',
                    'reason' => $this->adjustmentReason,
                    'status' => 'pending',
                    'requested_by' => $user->id,
                    'changed_fields' => $data,
                ]);

                flash()->warning('Adjustment major diajukan, menunggu persetujuan CSP.');
            } elseif ($isAdjustment && $this->adjustmentType === 'reactive') {
                $content->update($data);
                $changed = [];

                $trackedFields = ['theme', 'caption', 'description', 'content_type', 'priority',
                    'publish_date', 'deadline_produksi', 'pic_copy_id', 'pic_visual_id', 'pic_video_id',
                    'copy_brief', 'visual_brief', 'video_brief', 'final_asset_link', 'thumbnail_link',
                    'angle', 'positioning', 'target_audience', 'key_message', 'tone',
                    'aspect_ratio', 'resolution', 'duration', 'format_file', 'hashtag',
                    'audio_guidance', 'originality_instruction', 'thumbnail_note',
                    'est_copy_hours', 'est_visual_hours', 'est_video_hours',
                    'is_brief_final'];

                foreach ($trackedFields as $field) {
                    $old = $original[$field] ?? null;
                    $new = $data[$field] ?? $content->$field ?? null;

                    if ($old !== $new) {
                        $changed[] = $field;
                        AdjustmentLog::create([
                            'content_id' => $content->id,
                            'user_id' => $user->id,
                            'field' => $field,
                            'old_value' => is_bool($old) ? ($old ? '1' : '0') : $old,
                            'new_value' => is_bool($new) ? ($new ? '1' : '0') : $new,
                            'adjustment_type' => 'reactive',
                            'adjustment_reason' => $this->adjustmentReason,
                        ]);
                    }
                }

                if ($changed) {
                    $content->version = $content->version + 1;
                    $content->save();

                    ContentVersion::create([
                        'content_id' => $content->id,
                        'version' => $content->version,
                        'data' => $content->fresh()->toArray(),
                        'created_by' => $user->id,
                    ]);
                }

                $this->saveBrief($content->id, $data);

                Adjustment::create([
                    'content_id' => $content->id,
                    'type' => 'reactive',
                    'reason' => $this->adjustmentReason,
                    'status' => 'pending',
                    'requested_by' => $user->id,
                    'changed_fields' => $data,
                ]);

                flash()->success('Adjustment reactive diterapkan, menunggu konfirmasi CSP.');
            } else {
                $content->update($data);

                $versionIncreased = false;
                $trackedFields = ['theme', 'caption', 'description', 'content_type', 'priority',
                    'publish_date', 'deadline_produksi', 'pic_copy_id', 'pic_visual_id', 'pic_video_id',
                    'copy_brief', 'visual_brief', 'video_brief', 'final_asset_link', 'thumbnail_link',
                    'angle', 'positioning', 'target_audience', 'key_message', 'tone',
                    'aspect_ratio', 'resolution', 'duration', 'format_file', 'hashtag',
                    'audio_guidance', 'originality_instruction', 'thumbnail_note',
                    'is_brief_final'];

                foreach ($trackedFields as $field) {
                    $old = $original[$field] ?? null;
                    $new = $data[$field] ?? $content->$field ?? null;

                    if ($old !== $new) {
                        AdjustmentLog::create([
                            'content_id' => $content->id,
                            'user_id' => $user->id,
                            'field' => $field,
                            'old_value' => is_bool($old) ? ($old ? '1' : '0') : $old,
                            'new_value' => is_bool($new) ? ($new ? '1' : '0') : $new,
                            'adjustment_type' => $isAdjustment ? $this->adjustmentType : 'draft_edit',
                            'adjustment_reason' => $isAdjustment ? $this->adjustmentReason : null,
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
                        'created_by' => $user->id,
                    ]);
                }

                $this->saveBrief($content->id, $data);

                if ($isAdjustment && $this->adjustmentType === 'minor') {
                    Adjustment::create([
                        'content_id' => $content->id,
                        'type' => 'minor',
                        'reason' => $this->adjustmentReason,
                        'status' => 'approved',
                        'requested_by' => $user->id,
                        'reviewed_by' => $user->id,
                        'reviewed_at' => now(),
                        'changed_fields' => $data,
                    ]);
                }

                flash()->success('Konten berhasil diupdate!');
            }
        }

        $this->showModal = false;
        $this->reset('form', 'finalAsset', 'thumbnail', 'existingFinalAsset', 'existingThumbnail', 'isBriefFinal', 'editingContentStatus', 'adjustmentType', 'adjustmentReason', 'newContentGroupName');
        $this->resetValidation();
    }

    private function saveBrief($contentId, $data = null)
    {
        $briefFields = ['angle', 'positioning', 'target_audience', 'key_message', 'tone',
            'copy_brief', 'aspect_ratio', 'resolution', 'duration', 'format_file',
            'hashtag', 'audio_guidance', 'originality_instruction', 'thumbnail_note',
            'visual_brief', 'video_brief'];

        $source = $data ?? $this->form;
        $briefData = collect($source)->only($briefFields)
            ->map(fn ($v) => $v === '' ? null : $v)
            ->all();

        \App\Content\Models\Brief::updateOrCreate(
            ['content_id' => $contentId],
            $briefData,
        );
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('form', 'editingContentStatus', 'adjustmentType', 'adjustmentReason', 'newContentGroupName', 'fastTrack');
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

        if ((Auth::user()->isSuperAdmin() || Auth::user()->hasRole('CSP') || Auth::user()->hasRole('CW')) && $content->status === ContentStatus::DRAFT) {
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
        $this->briefContent = Content::with(['platform', 'product', 'campaign', 'brief'])->findOrFail($id);
        $this->showBriefModal = true;
    }

    public function closeBriefModal()
    {
        $this->showBriefModal = false;
        $this->briefContent = null;
    }

    public function startProduction($id)
    {
        $content = Content::findOrFail($id);

        if ($content->status->value !== 'draft') {
            flash()->error('Hanya konten dengan status Draft yang bisa mulai produksi.');
            return;
        }

        $content->status = \App\Content\Enums\ContentStatus::IN_PRODUCTION;
        $content->save();
        flash()->success('Konten masuk ke tahap produksi!');
    }

    public function submitForApproval($id)
    {
        $content = Content::findOrFail($id);

        if ($content->status->value !== 'in_production') {
            flash()->error('Konten harus dalam status In Production sebelum disubmit.');
            return;
        }

        $user = Auth::user();
        $isPic = $user->isSuperAdmin()
            || $user->id === $content->pic_copy_id
            || $user->id === $content->pic_visual_id
            || $user->id === $content->pic_video_id;

        if (! $isPic) {
            flash()->error('Hanya PIC konten yang bisa submit untuk review.');
            return;
        }

        if (! $content->final_asset_link && ! $content->thumbnail_link) {
            flash()->error('Minimal 1 asset harus diupload sebelum submit.');
            return;
        }

        $content->status = ContentStatus::READY_REVIEW;
        $content->save();

        Approval::updateOrCreate(
            ['content_id' => $content->id, 'stage' => 'cw'],
            ['status' => ApprovalStatus::PENDING->value, 'approver_id' => null, 'notes' => null],
        );

        flash()->success('Konten dikirim untuk review copy (menunggu CW)!');
    }

    public function finalizeBrief($id)
    {
        if (! Auth::user()->isSuperAdmin() && ! Auth::user()->hasRole('CSP')) {
            flash()->error('Hanya CSP yang bisa finalisasi brief.');
            return;
        }

        $content = Content::with('brief')->findOrFail($id);
        $content->is_brief_final = true;
        $content->brief_finalized_at = now();
        $content->brief_finalized_by = Auth::id();
        $content->save();

        if ($content->brief) {
            $content->brief->update([
                'is_final' => true,
                'finalized_at' => now(),
                'finalized_by' => Auth::id(),
            ]);
        }

        flash()->success('Brief berhasil difinalisasi! Konten siap produksi.');
    }

    public function resetFilters()
    {
        $this->reset(['selectedPlatform', 'selectedStatus', 'selectedPriority', 'search']);
    }



}
