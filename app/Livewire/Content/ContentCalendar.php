<?php

namespace App\Livewire\Content;

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

    public $showScheduleModal = false;

    public $scheduleContentId = null;

    public $scheduleDate = '';

    public $scheduleTime = '';

    public $showPublishModal = false;

    public $publishContentId = null;

    public $publishLiveUrl = '';

    public $showChecklistModal = false;

    public $checklistContentId = null;

    public $checklist = [];

    public $qc = [
        'k1_audio_original' => null,
        'k2_demo_penggunaan' => null,
        'k3_produk_visible' => null,
        'k4_manfaat_verbal' => null,
        'k5_tambahan' => null,
        'k6_tambahan' => null,
        'k5_label' => '',
        'k6_label' => '',
        'has_shopping_cart' => false,
        'notes' => '',
    ];

    public $capacityWeekStart = null;

    public $showCapacityEditModal = false;

    public $capacityEditUserId = null;

    public $capacityEditUserName = '';

    public $capacityEditMaxHours = 40;

    public $capacityEditContents = [];

    public $capacityResolutionNotes = '';

    public $capacityResolutionStep = 0;

    public $confirmByUserId = null;

    public function capacityGoToToday()
    {
        $this->capacityWeekStart = now()->startOfWeek();
    }

    public function capacityPreviousWeek()
    {
        $this->capacityWeekStart = $this->capacityWeekStart->copy()->subWeek();
    }

    public function capacityNextWeek()
    {
        $this->capacityWeekStart = $this->capacityWeekStart->copy()->addWeek();
    }

    public function getCapacityWeekEndProperty()
    {
        return $this->capacityWeekStart->copy()->endOfWeek();
    }

    public function getCapacityDataProperty()
    {
        $start = $this->capacityWeekStart;
        $end = $start->copy()->endOfWeek();

        $contents = Content::with(['picCopy', 'picVisual', 'picVideo'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('deadline_produksi', [$start, $end])
                    ->orWhereBetween('publish_date', [$start, $end]);
            })
            ->get();

        $users = [];

        foreach ($contents as $c) {
            if ($c->est_copy_hours && $c->pic_copy_id) {
                $users[$c->pic_copy_id]['name'] = $c->picCopy->name;
                $users[$c->pic_copy_id]['role'] = 'Copy';
                $users[$c->pic_copy_id]['total'] = ($users[$c->pic_copy_id]['total'] ?? 0) + (float) $c->est_copy_hours;
                $users[$c->pic_copy_id]['contents'][] = [
                    'id' => $c->id,
                    'code' => $c->content_code,
                    'theme' => $c->theme,
                    'hours' => (float) $c->est_copy_hours,
                ];
            }
            if ($c->est_visual_hours && $c->pic_visual_id) {
                $users[$c->pic_visual_id]['name'] = $c->picVisual->name;
                $users[$c->pic_visual_id]['role'] = 'Visual';
                $users[$c->pic_visual_id]['total'] = ($users[$c->pic_visual_id]['total'] ?? 0) + (float) $c->est_visual_hours;
                $users[$c->pic_visual_id]['contents'][] = [
                    'id' => $c->id,
                    'code' => $c->content_code,
                    'theme' => $c->theme,
                    'hours' => (float) $c->est_visual_hours,
                ];
            }
            if ($c->est_video_hours && $c->pic_video_id) {
                $users[$c->pic_video_id]['name'] = $c->picVideo->name;
                $users[$c->pic_video_id]['role'] = 'Video';
                $users[$c->pic_video_id]['total'] = ($users[$c->pic_video_id]['total'] ?? 0) + (float) $c->est_video_hours;
                $users[$c->pic_video_id]['contents'][] = [
                    'id' => $c->id,
                    'code' => $c->content_code,
                    'theme' => $c->theme,
                    'hours' => (float) $c->est_video_hours,
                ];
            }
        }

        foreach ($users as $id => &$row) {
            $setting = \App\Models\UserCapacitySetting::where('user_id', $id)
                ->where('effective_from', '<=', $start)
                ->orderBy('effective_from', 'desc')
                ->with('confirmer')
                ->first();

            $row['max'] = $setting ? (float) $setting->max_hours : 40;
            $row['total'] = $row['total'] ?? 0;
            $row['contents'] = $row['contents'] ?? [];
            $row['resolution_step'] = $setting?->resolution_step ?? 0;
            $row['resolution_notes'] = $setting?->resolution_notes;
            $row['confirmed_by'] = $setting?->confirmer?->name;
            $row['confirmed_at'] = $setting?->confirmed_at;
            $row['setting_id'] = $setting?->id;
        }

        return $users;
    }

    public function openCapacityEdit($userId)
    {
        $this->capacityEditUserId = $userId;
        $user = \App\Models\User::find($userId);
        $this->capacityEditUserName = $user?->name ?? 'User';

        $setting = \App\Models\UserCapacitySetting::where('user_id', $userId)
            ->where('effective_from', '<=', $this->capacityWeekStart)
            ->orderBy('effective_from', 'desc')
            ->first();

        $this->capacityEditMaxHours = $setting ? (float) $setting->max_hours : 40;

        $this->capacityEditContents = collect($this->capacity_data)
            ->get($userId, [])['contents'] ?? [];

        $this->showCapacityEditModal = true;
    }

    public function closeCapacityEditModal()
    {
        $this->showCapacityEditModal = false;
        $this->capacityEditUserId = null;
    }

    public function saveCapacitySettings()
    {
        $this->validate([
            'capacityEditMaxHours' => 'required|numeric|min:1|max:168',
        ]);

        \App\Models\UserCapacitySetting::updateOrCreate(
            [
                'user_id' => $this->capacityEditUserId,
                'effective_from' => $this->capacityWeekStart,
            ],
            [
                'max_hours' => $this->capacityEditMaxHours,
            ],
        );

        flash()->success('Kapasitas berhasil disimpan.');
        $this->closeCapacityEditModal();
    }

    public function saveCapacityResolution($userId)
    {
        $this->validate([
            'capacityResolutionStep' => 'required|integer|min:1|max:5',
            'capacityResolutionNotes' => 'required|string|max:1000',
        ]);

        \App\Models\UserCapacitySetting::updateOrCreate(
            [
                'user_id' => $userId,
                'effective_from' => $this->capacityWeekStart,
            ],
            [
                'resolution_step' => $this->capacityResolutionStep,
                'resolution_notes' => $this->capacityResolutionNotes,
            ],
        );

        $this->reset('capacityResolutionNotes', 'capacityResolutionStep');
        flash()->success('Tindakan kapasitas berhasil dicatat.');
    }

    public function confirmCapacity($userId)
    {
        \App\Models\UserCapacitySetting::updateOrCreate(
            [
                'user_id' => $userId,
                'effective_from' => $this->capacityWeekStart,
            ],
            [
                'confirmed_by' => Auth::id(),
                'confirmed_at' => now(),
            ],
        );

        flash()->success('Kapasitas sudah dikonfirmasi.');
    }

    public function getQcSuggestedSubtypeProperty(): ?string
    {
        if (! $this->qcContentId) {
            return null;
        }

        $values = collect($this->qc)->only([
            'k1_audio_original', 'k2_demo_penggunaan', 'k3_produk_visible',
            'k4_manfaat_verbal', 'k5_tambahan', 'k6_tambahan',
        ]);

        if ($values->contains(null)) {
            return null;
        }

        $allPass = $values->every(fn ($v) => $v === 'pass');
        $hasCart = $this->qc['has_shopping_cart'] ?? false;

        if ($hasCart && $allPass) {
            return 'KK Interaktif';
        }

        if ($hasCart) {
            return 'KK Soft Selling';
        }

        return 'Non-KK';
    }

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
        'content_group_id' => '',
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

    protected $rules = [
        'form.platform_id' => 'required|exists:platforms,id',
        'form.theme' => 'required|string|max:200',
        'form.priority' => 'required|in:high,medium,low',
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

        $content = Content::findOrFail($contentId);
        $targetStatus = $statusMap[$column] ?? null;

        if ($targetStatus === 'in_production' && ! $content->is_brief_final) {
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
        $this->reset('form', 'finalAsset', 'thumbnail', 'existingFinalAsset', 'existingThumbnail', 'editingContentStatus', 'adjustmentType', 'adjustmentReason');
        $this->resetValidation();
        $this->modalMode = 'create';
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

        $raw['priority'] = $content->priority?->value ?? 'medium';
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
            $this->addError('adjustmentReason', 'Alasan adjustment wajib diisi untuk tipe Major atau Reactive.');
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

            flash()->success('Konten berhasil dibuat!');
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
                            'adjustment_type' => $isAdjustment ? $this->adjustmentType : 'minor',
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

                flash()->success('Konten berhasil diupdate!');
            }
        }

        $this->showModal = false;
        $this->reset('form', 'finalAsset', 'thumbnail', 'existingFinalAsset', 'existingThumbnail', 'isBriefFinal', 'editingContentStatus', 'adjustmentType', 'adjustmentReason', 'newContentGroupName');
        $this->resetValidation();
    }

    private function saveBrief($contentId)
    {
        $briefFields = ['angle', 'positioning', 'target_audience', 'key_message', 'tone',
            'copy_brief', 'aspect_ratio', 'resolution', 'duration', 'format_file',
            'hashtag', 'audio_guidance', 'originality_instruction', 'thumbnail_note',
            'visual_brief', 'video_brief'];

        $briefData = collect($this->form)->only($briefFields)
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
        $this->reset('form', 'editingContentStatus', 'adjustmentType', 'adjustmentReason', 'newContentGroupName');
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

    public function openQcModal($id)
    {
        $this->qcContentId = $id;
        $this->resetQcForm();

        $existing = TiktokQc::where('content_id', $id)->first();
        if ($existing) {
            $this->qc = $existing->only([
                'k1_audio_original', 'k2_demo_penggunaan', 'k3_produk_visible',
                'k4_manfaat_verbal', 'k5_tambahan', 'k6_tambahan',
                'k5_label', 'k6_label', 'has_shopping_cart', 'notes',
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
            'k1_audio_original' => null,
            'k2_demo_penggunaan' => null,
            'k3_produk_visible' => null,
            'k4_manfaat_verbal' => null,
            'k5_tambahan' => null,
            'k6_tambahan' => null,
            'k5_label' => '',
            'k6_label' => '',
            'has_shopping_cart' => false,
            'notes' => '',
        ];
    }

    public function saveQc()
    {
        $this->validate([
            'qc.notes' => 'nullable|string|max:1000',
            'qc.k1_audio_original' => 'required|in:pass,fail,na',
            'qc.k2_demo_penggunaan' => 'required|in:pass,fail,na',
            'qc.k3_produk_visible' => 'required|in:pass,fail,na',
            'qc.k4_manfaat_verbal' => 'required|in:pass,fail,na',
            'qc.k5_tambahan' => 'nullable|in:pass,fail,na',
            'qc.k6_tambahan' => 'nullable|in:pass,fail,na',
        ]);

        $data = collect($this->qc)->map(fn ($v) => $v === '' ? null : $v)->all();
        $data['checked_by'] = Auth::id();
        $data['checked_at'] = now();

        $criteriaKeys = ['k1_audio_original', 'k2_demo_penggunaan', 'k3_produk_visible',
            'k4_manfaat_verbal', 'k5_tambahan', 'k6_tambahan'];

        $allPass = collect($data)->only($criteriaKeys)->every(fn ($v) => $v === 'pass');
        $data['status'] = $allPass ? 'passed' : 'need_revision';

        $qcRecord = TiktokQc::updateOrCreate(
            ['content_id' => $this->qcContentId],
            $data,
        );

        if ($qcRecord->allPass()) {
            $content = Content::with('platform')->find($this->qcContentId);
            if ($content && $content->platform->code === 'TKM') {
                $content->update(['tiktok_subtype' => $qcRecord->suggestedSubtype()]);
            }
        }

        if ($allPass) {
            flash()->success('QC TikTok selesai — semua checklist terpenuhi!');
        } else {
            flash()->warning('QC TikTok perlu perbaikan — ada item yang belum terpenuhi.');
        }

        $this->closeQcModal();
    }

    public function openScheduleModal($id)
    {
        $this->scheduleContentId = $id;
        $content = Content::find($id);
        $this->scheduleDate = $content?->publish_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->scheduleTime = $content?->publish_time?->format('H:i') ?? '08:00';
        $this->showScheduleModal = true;
    }

    public function closeScheduleModal()
    {
        $this->showScheduleModal = false;
        $this->scheduleContentId = null;
    }

    public function confirmSchedule()
    {
        $this->validate([
            'scheduleDate' => 'required|date|after_or_equal:today',
            'scheduleTime' => 'required|date_format:H:i',
        ]);

        $content = Content::findOrFail($this->scheduleContentId);
        $content->update([
            'publish_date' => $this->scheduleDate,
            'publish_time' => $this->scheduleTime,
            'status' => 'scheduled',
        ]);

        flash()->success('Konten berhasil dijadwalkan!');
        $this->closeScheduleModal();
    }

    public function openPublishModal($id)
    {
        $this->publishContentId = $id;
        $content = Content::find($id);
        $this->publishLiveUrl = $content?->live_url ?? '';
        $this->showPublishModal = true;
    }

    public function closePublishModal()
    {
        $this->showPublishModal = false;
        $this->publishContentId = null;
        $this->publishLiveUrl = '';
    }

    public function confirmPublish()
    {
        $this->validate([
            'publishLiveUrl' => 'required|url|max:500',
        ]);

        $content = Content::findOrFail($this->publishContentId);
        $content->update([
            'live_url' => $this->publishLiveUrl,
            'status' => 'published',
        ]);

        $this->closePublishModal();
        flash()->success('Konten berhasil dipublikasikan!');
    }

    public function openChecklistModal($id)
    {
        $this->checklistContentId = $id;
        $this->resetChecklistForm();

        $existing = \App\Content\Models\PostPublishChecklist::where('content_id', $id)->first();
        if ($existing) {
            $this->checklist = $existing->only([
                'link_works', 'thumbnail_visible', 'caption_accurate',
                'hashtags_included', 'cta_functional', 'product_tagged',
                'no_typo', 'audio_sync', 'notes',
            ]);
        }

        $this->showChecklistModal = true;
    }

    public function closeChecklistModal()
    {
        $this->showChecklistModal = false;
        $this->checklistContentId = null;
        $this->resetChecklistForm();
    }

    public function resetChecklistForm()
    {
        $this->checklist = [
            'link_works' => false,
            'thumbnail_visible' => false,
            'caption_accurate' => false,
            'hashtags_included' => false,
            'cta_functional' => false,
            'product_tagged' => false,
            'no_typo' => false,
            'audio_sync' => false,
            'notes' => '',
        ];
    }

    public function saveChecklist()
    {
        $data = collect($this->checklist)->map(fn ($v) => $v === '' ? null : $v)->all();
        $data['checked_by'] = Auth::id();
        $data['checked_at'] = now();

        \App\Content\Models\PostPublishChecklist::updateOrCreate(
            ['content_id' => $this->checklistContentId],
            $data,
        );

        $allChecked = collect($this->checklist)->except('notes')->every(fn ($v) => $v === true);
        if ($allChecked) {
            flash()->success('Semua checklist terpenuhi!');
        } else {
            flash()->warning('Ada item yang belum tercentang.');
        }

        $this->closeChecklistModal();
    }

    public function startProduction($id)
    {
        $content = Content::findOrFail($id);

        if ($content->status->value !== 'draft') {
            flash()->error('Hanya konten dengan status Draft yang bisa mulai produksi.');
            return;
        }

        $content->update(['status' => 'in_production']);
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

        $content = Content::findOrFail($this->approveContentId);

        $nextStage = match ($this->approveStage) {
            'cw' => 'csp',
            'csp' => 'sms',
            'sms' => $content->has_claim ? 'rnd' : ($content->is_sensitive ? 'legal' : null),
            'rnd' => $content->is_sensitive ? 'legal' : null,
            'legal' => null,
            default => null,
        };

        if ($nextStage) {
            Approval::updateOrCreate(
                ['content_id' => $content->id, 'stage' => $nextStage],
                ['status' => ApprovalStatus::PENDING->value, 'approver_id' => null, 'notes' => null],
            );
        } else {
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

    public function approveAdjustment($adjustmentId)
    {
        $adjustment = Adjustment::findOrFail($adjustmentId);

        $allowed = Auth::user()->isSuperAdmin()
            || Auth::user()->hasRole('MC_BM')
            || ($adjustment->type === 'reactive' && Auth::user()->hasRole('CSP'));

        if (! $allowed) {
            flash()->error('Hanya MC/BM yang bisa menyetujui adjustment.');
            return;
        }

        $content = $adjustment->content;

        if ($adjustment->type === 'major' && $adjustment->changed_fields) {
            $original = $content->fresh()->toArray();
            $content->update($adjustment->changed_fields);
            $content->version = $content->version + 1;
            $content->save();

            ContentVersion::create([
                'content_id' => $content->id,
                'version' => $content->version,
                'data' => $content->fresh()->toArray(),
                'created_by' => Auth::id(),
            ]);

            foreach ($adjustment->changed_fields as $field => $new) {
                $old = $original[$field] ?? null;
                if ($old !== $new && ! in_array($field, ['created_at', 'updated_at', 'id', 'version'])) {
                    AdjustmentLog::create([
                        'content_id' => $content->id,
                        'user_id' => Auth::id(),
                        'field' => $field,
                        'old_value' => is_bool($old) ? ($old ? '1' : '0') : (string) $old,
                        'new_value' => is_bool($new) ? ($new ? '1' : '0') : (string) $new,
                        'adjustment_type' => 'major',
                        'adjustment_reason' => $adjustment->reason,
                    ]);
                }
            }
        }

        $adjustment->status = 'approved';
        $adjustment->reviewed_by = Auth::id();
        $adjustment->reviewed_at = now();
        $adjustment->save();

        flash()->success('Adjustment '.$adjustment->type.' disetujui.');
    }

    public function rejectAdjustment($adjustmentId)
    {
        $adjustment = Adjustment::findOrFail($adjustmentId);

        $allowed = Auth::user()->isSuperAdmin()
            || Auth::user()->hasRole('MC_BM')
            || ($adjustment->type === 'reactive' && Auth::user()->hasRole('CSP'));

        if (! $allowed) {
            flash()->error('Hanya MC/BM yang bisa menolak adjustment.');
            return;
        }

        $adjustment->status = 'rejected';
        $adjustment->reviewed_by = Auth::id();
        $adjustment->reviewed_at = now();
        $adjustment->save();

        flash()->success('Adjustment '.$adjustment->type.' ditolak.');
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

    public function archiveVersion($versionId)
    {
        $version = ContentVersion::findOrFail($versionId);

        $version->update([
            'is_archived' => true,
            'archived_by' => Auth::id(),
            'archived_at' => now(),
        ]);

        flash()->success('Versi berhasil diarsipkan.');
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
