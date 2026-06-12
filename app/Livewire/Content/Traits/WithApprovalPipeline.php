<?php

namespace App\Livewire\Content\Traits;

use App\Content\Models\Approval;
use App\Content\Models\Content;
use App\Content\Models\TiktokQc;
use App\Enums\ApprovalStatus;
use App\Enums\ContentStatus;
use Illuminate\Support\Facades\Auth;

trait WithApprovalPipeline
{
    public $showApproveModal = false;

    public $approveContentId = null;

    public $approveStage = null;

    public $approveNotes = '';

    public $showQcModal = false;

    public $qcContentId = null;

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

    public function openQcModal($id)
    {
        $content = Content::with('platform')->find($id);

        if (! $content || $content->platform?->code !== 'TKM') {
            flash()->error('QC TikTok hanya tersedia untuk konten platform TikTok.');
            return;
        }

        if (! in_array($content->status->value, ['in_production', 'ready_review', 'approved'])) {
            flash()->error('QC hanya bisa dilakukan pada konten berstatus In Production, Ready for Review, atau Approved.');
            return;
        }

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
}
