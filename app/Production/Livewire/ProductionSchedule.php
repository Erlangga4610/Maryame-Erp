<?php

namespace App\Production\Livewire;

use App\Content\Enums\ContentStatus;
use App\Content\Models\Content;
use App\Models\Platform;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductionSchedule extends Component
{
    public $weekStart = null;

    public $weekEnd = null;

    public $selectedPlatform = '';

    public $selectedStatus = '';

    public $search = '';

    public $showBlockReasonModal = false;

    public $blockReasonContentId = null;

    public $blockReasonInput = '';

    public function mount()
    {
        $this->goToCurrentWeek();
    }

    public function goToCurrentWeek()
    {
        $now = Carbon::now();
        $this->weekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $this->weekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
    }

    public function previousWeek()
    {
        $start = Carbon::parse($this->weekStart)->subWeek();
        $this->weekStart = $start->format('Y-m-d');
        $this->weekEnd = $start->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
    }

    public function nextWeek()
    {
        $start = Carbon::parse($this->weekStart)->addWeek();
        $this->weekStart = $start->format('Y-m-d');
        $this->weekEnd = $start->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
    }

    public function toggleBriefed($id)
    {
        $content = Content::findOrFail($id);
        $content->is_briefed = ! $content->is_briefed;
        $content->briefed_by = $content->is_briefed ? Auth::id() : null;
        $content->briefed_at = $content->is_briefed ? now() : null;
        $content->save();

        flash()->success($content->is_briefed ? 'Konten sudah dibrief ke produksi.' : 'Status brief dibatalkan.');
    }

    public function toggleBlocked($id)
    {
        $content = Content::findOrFail($id);
        $content->is_blocked = ! $content->is_blocked;
        $content->blocked_by = $content->is_blocked ? Auth::id() : null;
        $content->blocked_at = $content->is_blocked ? now() : null;
        $content->save();

        flash()->success($content->is_blocked ? 'Konten diblokir.' : 'Blokir dibatalkan.');
    }

    public function openBlockReason($id)
    {
        $content = Content::findOrFail($id);
        $this->blockReasonContentId = $id;
        $this->blockReasonInput = $content->blocked_reason ?? '';
        $this->showBlockReasonModal = true;
    }

    public function saveBlockReason()
    {
        if (! $this->blockReasonContentId) {
            return;
        }

        $content = Content::findOrFail($this->blockReasonContentId);
        $content->blocked_reason = $this->blockReasonInput ?: null;
        $content->save();

        $this->blockReasonContentId = null;
        $this->blockReasonInput = '';
        $this->showBlockReasonModal = false;

        flash()->success('Alasan blokir diperbarui.');
    }

    public function render()
    {
        $weekStart = Carbon::parse($this->weekStart);
        $weekEnd = Carbon::parse($this->weekEnd);

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $days[] = [
                'date' => $date,
                'label' => $date->isoFormat('dddd'),
                'day' => $date->format('d'),
                'month' => $date->format('M'),
                'isToday' => $date->isToday(),
                'isPast' => $date->isPast() && ! $date->isToday(),
            ];
        }

        $query = Content::with(['platform', 'picCopy', 'picVisual', 'picVideo']);

        $query->where(function ($q) use ($weekStart, $weekEnd) {
            $q->whereBetween('deadline_produksi', [$weekStart, $weekEnd])
                ->orWhereNull('deadline_produksi');
        });

        if ($this->selectedPlatform) {
            $query->where('platform_id', $this->selectedPlatform);
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('content_code', 'like', '%'.$this->search.'%')
                    ->orWhere('theme', 'like', '%'.$this->search.'%');
            });
        }

        $contents = $query->orderBy('priority', 'desc')
            ->orderBy('deadline_produksi', 'asc')
            ->get();

        $scheduled = $contents->filter(fn ($c) => $c->deadline_produksi);
        $unscheduled = $contents->filter(fn ($c) => ! $c->deadline_produksi);

        $groupedByDay = collect($days)->mapWithKeys(function ($day) use ($scheduled) {
            $dateStr = $day['date']->format('Y-m-d');
            return [$dateStr => $scheduled->filter(fn ($c) => $c->deadline_produksi?->format('Y-m-d') === $dateStr)];
        });

        $platforms = Platform::where('is_active', true)->get();
        $statusOptions = ContentStatus::options();

        return view('production.production-schedule', [
            'days' => $days,
            'groupedByDay' => $groupedByDay,
            'unscheduled' => $unscheduled,
            'weekLabelStart' => $weekStart,
            'weekLabelEnd' => $weekEnd,
            'platforms' => $platforms,
            'statusOptions' => $statusOptions,
        ])->layout('layouts.admin', ['title' => 'Production Schedule']);
    }
}
