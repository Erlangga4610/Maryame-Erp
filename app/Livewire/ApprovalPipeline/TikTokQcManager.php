<?php

namespace App\Livewire\ApprovalPipeline;

use App\Content\Models\Content;
use App\Content\Models\TiktokQc;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TikTokQcManager extends Component
{
    public $contentId;

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

    public function mount($contentId)
    {
        $this->contentId = $contentId;
        $this->loadExisting();
    }

    public function loadExisting()
    {
        $existing = TiktokQc::where('content_id', $this->contentId)->first();
        if ($existing) {
            $this->qc = $existing->only([
                'k1_audio_original', 'k2_demo_penggunaan', 'k3_produk_visible',
                'k4_manfaat_verbal', 'k5_tambahan', 'k6_tambahan',
                'k5_label', 'k6_label', 'has_shopping_cart', 'notes',
            ]);
        }
    }

    public function getSuggestedSubtypeProperty(): ?string
    {
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

    public function save()
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

        TiktokQc::updateOrCreate(
            ['content_id' => $this->contentId],
            $data,
        );

        $content = Content::with('platform')->find($this->contentId);
        if ($content && $content->platform->code === 'TKM') {
            $content->update(['tiktok_subtype' => $this->suggested_subtype]);
        }

        if ($allPass) {
            flash()->success('QC TikTok selesai — semua checklist terpenuhi!');
        } else {
            flash()->warning('QC TikTok perlu perbaikan — ada item yang belum terpenuhi.');
        }

        $this->dispatch('qc-saved', contentId: $this->contentId);
    }

    public function render()
    {
        $content = Content::with('platform')->findOrFail($this->contentId);

        return view('livewire.approval-pipeline.tiktok-qc-manager', [
            'content' => $content,
        ])->layout('layouts.admin', ['title' => 'QC TikTok #'.$content->content_code]);
    }
}
