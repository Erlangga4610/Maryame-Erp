<?php

namespace App\Content\Livewire;

use App\Content\Enums\ContentStatus;
use App\Content\Enums\ContentType;
use App\Content\Models\Content;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ContentCalendar extends Component
{
    use WithPagination;

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
    ];

    protected $rules = [
        'form.platform_id' => 'required|exists:platforms,id',
        'form.theme' => 'required|string|max:200',
        'form.priority' => 'required|in:high,medium,low',
        'form.publish_date' => 'nullable|date',
        'form.deadline_produksi' => 'nullable|date',
    ];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function render()
    {
        $query = Content::with(['platform', 'product', 'campaign', 'picCopy', 'picVisual', 'picVideo']);

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
        ]);
        $raw['priority'] = $content->priority?->value ?? 'medium';
        $raw['publish_date'] = $content->publish_date?->format('Y-m-d') ?? '';
        $raw['deadline_produksi'] = $content->deadline_produksi?->format('Y-m-d') ?? '';
        $this->form = $raw;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = collect($this->form)->map(fn ($v) => $v === '' ? null : $v)->all();

        if ($this->modalMode === 'create') {
            Content::create($data);
            flash()->success('Konten berhasil dibuat!');
        } else {
            $content = Content::findOrFail($this->contentId);
            $content->update($data);
            flash()->success('Konten berhasil diupdate!');
        }

        $this->showModal = false;
        $this->reset('form');
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

    public function resetFilters()
    {
        $this->reset(['selectedPlatform', 'selectedStatus', 'selectedPriority', 'search']);
    }
}
