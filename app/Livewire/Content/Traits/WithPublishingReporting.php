<?php

namespace App\Livewire\Content\Traits;

use App\Content\Models\Adjustment;
use App\Content\Models\AdjustmentLog;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
use App\Content\Models\PostPublishChecklist;
use Illuminate\Support\Facades\Auth;

trait WithPublishingReporting
{
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

    public $showAdjustmentModal = false;

    public $adjustmentContentId = null;

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

        $existing = PostPublishChecklist::where('content_id', $id)->first();
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

        PostPublishChecklist::updateOrCreate(
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
