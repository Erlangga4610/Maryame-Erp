<?php

namespace App\Livewire\PublishingReporting;

use App\Content\Models\Content;
use App\Content\Models\PostPublishChecklist;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublishingManager extends Component
{
    public $contentId;

    public $scheduleDate = '';

    public $scheduleTime = '';

    public $publishLiveUrl = '';

    public $checklist = [];

    public $showScheduleModal = false;

    public $showPublishModal = false;

    public $showChecklistModal = false;

    public function mount($contentId)
    {
        $this->contentId = $contentId;
    }

    public function openScheduleModal()
    {
        $content = Content::find($this->contentId);
        $this->scheduleDate = $content?->publish_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->scheduleTime = $content?->publish_time?->format('H:i') ?? '08:00';
        $this->showScheduleModal = true;
    }

    public function closeScheduleModal()
    {
        $this->showScheduleModal = false;
    }

    public function confirmSchedule()
    {
        $this->validate([
            'scheduleDate' => 'required|date|after_or_equal:today',
            'scheduleTime' => 'required|date_format:H:i',
        ]);

        $content = Content::findOrFail($this->contentId);
        $content->update([
            'publish_date' => $this->scheduleDate,
            'publish_time' => $this->scheduleTime,
            'status' => 'scheduled',
        ]);

        flash()->success('Konten berhasil dijadwalkan!');
        $this->closeScheduleModal();
        $this->dispatch('schedule-completed', contentId: $this->contentId);
    }

    public function openPublishModal()
    {
        $content = Content::find($this->contentId);
        $this->publishLiveUrl = $content?->live_url ?? '';
        $this->showPublishModal = true;
    }

    public function closePublishModal()
    {
        $this->showPublishModal = false;
        $this->publishLiveUrl = '';
    }

    public function confirmPublish()
    {
        $this->validate([
            'publishLiveUrl' => 'required|url|max:500',
        ]);

        $content = Content::findOrFail($this->contentId);
        $content->update([
            'live_url' => $this->publishLiveUrl,
            'status' => 'published',
        ]);

        $this->closePublishModal();
        flash()->success('Konten berhasil dipublikasikan!');
        $this->dispatch('publish-completed', contentId: $this->contentId);
    }

    public function openChecklistModal()
    {
        $this->resetChecklistForm();
        $existing = PostPublishChecklist::where('content_id', $this->contentId)->first();
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

        PostPublishChecklist::updateOrCreate(
            ['content_id' => $this->contentId],
            $data,
        );

        $allChecked = collect($this->checklist)->except('notes')->every(fn ($v) => $v === true);
        if ($allChecked) {
            flash()->success('Semua checklist terpenuhi!');
        } else {
            flash()->warning('Ada item yang belum tercentang.');
        }

        $this->closeChecklistModal();
        $this->dispatch('checklist-completed', contentId: $this->contentId);
    }

    public function render()
    {
        $content = Content::with('platform')->findOrFail($this->contentId);
        $postPublishChecklist = PostPublishChecklist::where('content_id', $this->contentId)->first();

        return view('livewire.publishing-reporting.publishing-manager', [
            'content' => $content,
            'checklist' => $postPublishChecklist,
        ])->layout('layouts.admin', ['title' => 'Publishing #'.$content->content_code]);
    }
}
