<?php

namespace App\Livewire\Content;

use App\Content\Enums\CalendarEntryStatus;
use App\Content\Models\Calendar;
use App\Content\Models\CalendarEntry;
use App\Content\Models\Content;
use App\Models\Platform;
use App\Models\User;
use App\Models\UserCapacitySetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CalendarManagement extends Component
{
    public $currentMonth;

    public $currentYear;

    public $calendar;

    public $searchContent = '';

    public $selectedPlatform = '';

    public $searchResults = [];

    public $showAddPanel = false;

    public $selectedDate = null;

    protected $queryString = ['currentMonth', 'currentYear'];

    public function mount()
    {
        $this->currentMonth = (int) $this->currentMonth ?: now()->month;
        $this->currentYear = (int) $this->currentYear ?: now()->year;
        $this->loadCalendar();
    }

    public function loadCalendar()
    {
        $this->calendar = Calendar::firstOrCreate(
            ['month' => $this->currentMonth, 'year' => $this->currentYear],
            [
                'label' => Carbon::create($this->currentYear, $this->currentMonth)->format('F Y'),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ],
        );

        $this->calendar->load('entries.content.platform', 'entries.content.picCopy');
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadCalendar();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadCalendar();
    }

    public function goToToday()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->loadCalendar();
    }

    public function isCapacityReady(): bool
    {
        $monthStart = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfWeek();
        $monthEnd = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth()->endOfWeek();

        $contents = \App\Content\Models\Content::where(function ($q) use ($monthStart, $monthEnd) {
            $q->whereBetween('deadline_produksi', [$monthStart, $monthEnd])
                ->orWhereBetween('publish_date', [$monthStart, $monthEnd]);
        })->get();

        $weekStarts = collect();
        $current = $monthStart->copy();
        while ($current <= $monthEnd) {
            $weekStarts->push($current->copy());
            $current->addWeek();
        }

        foreach ($weekStarts as $weekStart) {
            $weekEnd = $weekStart->copy()->endOfWeek();
            $weekContents = $contents->filter(fn ($c) =>
                ($c->deadline_produksi && $c->deadline_produksi->between($weekStart, $weekEnd))
                || ($c->publish_date && $c->publish_date->between($weekStart, $weekEnd))
            );

            $userTotals = [];
            foreach ($weekContents as $c) {
                if ($c->est_copy_hours && $c->pic_copy_id) {
                    $userTotals[$c->pic_copy_id] = ($userTotals[$c->pic_copy_id] ?? 0) + (float) $c->est_copy_hours;
                }
                if ($c->est_visual_hours && $c->pic_visual_id) {
                    $userTotals[$c->pic_visual_id] = ($userTotals[$c->pic_visual_id] ?? 0) + (float) $c->est_visual_hours;
                }
                if ($c->est_video_hours && $c->pic_video_id) {
                    $userTotals[$c->pic_video_id] = ($userTotals[$c->pic_video_id] ?? 0) + (float) $c->est_video_hours;
                }
            }

            foreach ($userTotals as $userId => $total) {
                $setting = UserCapacitySetting::where('user_id', $userId)
                    ->where('effective_from', '<=', $weekStart)
                    ->orderBy('effective_from', 'desc')
                    ->first();

                $max = $setting ? (float) $setting->max_hours : 40;

                if ($total > $max && ! $setting?->confirmed_by) {
                    return false;
                }
            }
        }

        return true;
    }

    public function submitForReview()
    {
        if (! Auth::user()->isSuperAdmin() && ! Auth::user()->hasRole('CSP')) {
            flash()->error('Hanya CSP yang bisa submit kalender untuk review.');
            return;
        }

        if ($this->calendar->status !== 'draft') {
            flash()->error('Kalender harus berstatus Draft.');
            return;
        }

        if (! $this->isCapacityReady()) {
            flash()->error('Kapasitas produksi belum OK. Selesaikan tindakan Over Capacity di tab Capacity terlebih dahulu.');
            return;
        }

        $this->calendar->update(['status' => 'in_review']);
        flash()->success('Kalender dikirim ke review (menunggu MC/BM).');
        $this->loadCalendar();
    }

    public function approveCalendar()
    {
        if (! Auth::user()->isSuperAdmin() && ! Auth::user()->hasRole('MC_BM')) {
            flash()->error('Hanya MC/BM yang bisa menyetujui kalender.');
            return;
        }

        if ($this->calendar->status !== 'in_review') {
            flash()->error('Kalender harus berstatus In Review.');
            return;
        }

        $this->calendar->update(['status' => 'approved']);
        flash()->success('Kalender disetujui.');
        $this->loadCalendar();
    }

    public function distributeCalendar()
    {
        if (! Auth::user()->isSuperAdmin() && ! Auth::user()->hasRole('CSP')) {
            flash()->error('Hanya CSP yang bisa mendistribusikan kalender.');
            return;
        }

        if ($this->calendar->status !== 'approved') {
            flash()->error('Kalender harus berstatus Approved.');
            return;
        }

        $this->calendar->update(['status' => 'distributed']);
        flash()->success('Kalender didistribusikan ke tim.');
        $this->loadCalendar();
    }

    public function archiveCalendar()
    {
        if (! Auth::user()->isSuperAdmin() && ! Auth::user()->hasRole('CSP')) {
            flash()->error('Hanya CSP yang bisa mengarsipkan kalender.');
            return;
        }

        if ($this->calendar->status !== 'distributed') {
            flash()->error('Kalender harus berstatus Distributed.');
            return;
        }

        $this->calendar->update(['status' => 'archived']);
        flash()->success('Kalender diarsipkan.');
        $this->loadCalendar();
    }

    public function openAddPanel($date)
    {
        $this->selectedDate = $date;
        $this->showAddPanel = true;
        $this->searchContent = '';
        $this->selectedPlatform = '';
        $this->searchContent();
    }

    public function closeAddPanel()
    {
        $this->showAddPanel = false;
        $this->selectedDate = null;
        $this->searchContent = '';
        $this->searchResults = [];
    }

    public function updatedSearchContent()
    {
        $this->searchContent();
    }

    public function updatedSelectedPlatform()
    {
        $this->searchContent();
    }

    public function searchContent()
    {
        $query = Content::with('platform');

        if ($this->searchContent) {
            $query->where(function ($q) {
                $q->where('theme', 'like', '%' . $this->searchContent . '%')
                    ->orWhere('content_code', 'like', '%' . $this->searchContent . '%');
            });
        }

        if ($this->selectedPlatform) {
            $query->where('platform_id', $this->selectedPlatform);
        }

        $this->searchResults = $query->whereNotIn('id', $this->calendar->entries->pluck('content_id'))
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function addContent($contentId)
    {
        $existing = CalendarEntry::where('calendar_id', $this->calendar->id)
            ->where('content_id', $contentId)
            ->exists();

        if ($existing) {
            flash()->warning('Konten sudah ada di kalender ini.');
            return;
        }

        CalendarEntry::create([
            'calendar_id' => $this->calendar->id,
            'content_id' => $contentId,
            'scheduled_date' => $this->selectedDate,
            'status' => CalendarEntryStatus::DRAFT,
            'sort_order' => CalendarEntry::where('calendar_id', $this->calendar->id)
                ->where('scheduled_date', $this->selectedDate)
                ->count(),
            'created_by' => Auth::id(),
        ]);

        flash()->success('Konten ditambahkan ke kalender.');
        $this->closeAddPanel();
        $this->loadCalendar();
    }

    public function removeEntry($entryId)
    {
        CalendarEntry::findOrFail($entryId)->delete();
        flash()->success('Entry dihapus dari kalender.');
        $this->loadCalendar();
    }

    public function updateStatus($entryId, $status)
    {
        $entry = CalendarEntry::findOrFail($entryId);
        $entry->update(['status' => $status]);
        flash()->success('Status diupdate ke ' . CalendarEntryStatus::from($status)->label());
        $this->loadCalendar();
    }

    public function moveEntry($entryId, $date)
    {
        CalendarEntry::findOrFail($entryId)->update(['scheduled_date' => $date]);
        $this->loadCalendar();
    }

    public function render()
    {
        $days = [];
        $firstDay = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        $start = $firstDay->copy()->startOfWeek();
        $end = $lastDay->copy()->endOfWeek();

        $current = $start->copy();
        while ($current <= $end) {
            $days[] = $current->copy();
            $current->addDay();
        }

        $entriesByDate = $this->calendar->entries
            ->groupBy(fn ($e) => $e->scheduled_date->format('Y-m-d'));

        $platforms = Platform::orderBy('name')->get();

        $statusOptions = CalendarEntryStatus::options();

        $entryStatuses = CalendarEntryStatus::cases();

        return view('livewire.content.calendar-management', [
            'days' => $days,
            'entriesByDate' => $entriesByDate,
            'platforms' => $platforms,
            'statusOptions' => $statusOptions,
            'entryStatuses' => $entryStatuses,
        ])->layout('layouts.admin', ['title' => 'Calendar - ' . Carbon::create($this->currentYear, $this->currentMonth)->format('F Y')]);
    }
}
