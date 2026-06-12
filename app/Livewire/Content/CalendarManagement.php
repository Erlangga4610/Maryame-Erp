<?php

namespace App\Livewire\Content;

use App\Content\Enums\CalendarEntryStatus;
use App\Content\Models\Calendar;
use App\Content\Models\CalendarEntry;
use App\Content\Models\Content;
use App\Models\Platform;
use App\Models\User;
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
