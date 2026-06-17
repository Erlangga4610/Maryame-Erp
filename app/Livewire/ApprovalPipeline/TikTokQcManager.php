<?php

namespace App\Livewire\ApprovalPipeline;

use App\Content\Models\Adjustment;
use App\Content\Models\AdjustmentLog;
use App\Content\Models\Content;
use App\Content\Models\QcCriteriaResult;
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
        $existing = TiktokQc::with('criteriaResults')->where('content_id', $this->contentId)->first();
        if ($existing) {
            $this->qc = $existing->only([
                'k1_audio_original', 'k2_demo_penggunaan', 'k3_produk_visible',
                'k4_manfaat_verbal', 'k5_tambahan', 'k6_tambahan',
                'k5_label', 'k6_label', 'has_shopping_cart', 'notes',
            ]);

            if ($existing->criteriaResults->isNotEmpty()) {
                $map = [
                    1 => 'k1_audio_original',
                    2 => 'k2_demo_penggunaan',
                    3 => 'k3_produk_visible',
                    4 => 'k4_manfaat_verbal',
                    5 => 'k5_tambahan',
                    6 => 'k6_tambahan',
                ];
                foreach ($existing->criteriaResults as $r) {
                    $field = $map[$r->criteria_no] ?? null;
                    if ($field) {
                        $this->qc[$field] = $r->result;
                        if ($r->criteria_no === 5) {
                            $this->qc['k5_label'] = $r->note;
                        } elseif ($r->criteria_no === 6) {
                            $this->qc['k6_label'] = $r->note;
                        }
                    }
                }
            }
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

    public function downgradeToNonKk()
    {
        $content = Content::findOrFail($this->contentId);

        $oldSubtype = $content->tiktok_subtype?->value;
        $content->tiktok_subtype = \App\Content\Enums\TiktokSubtype::NON_KK;
        $content->version = $content->version + 1;
        $content->save();

        AdjustmentLog::create([
            'content_id' => $content->id,
            'user_id' => Auth::id(),
            'field' => 'tiktok_subtype',
            'old_value' => $oldSubtype ?? '-',
            'new_value' => 'non_kk',
            'adjustment_type' => 'minor',
            'adjustment_reason' => 'Turun ke Non-KK — QC tidak feasible',
        ]);

        Adjustment::create([
            'content_id' => $content->id,
            'type' => 'minor',
            'reason' => 'Turun ke Non-KK — QC tidak feasible',
            'status' => 'approved',
            'requested_by' => Auth::id(),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'changed_fields' => ['tiktok_subtype' => 'non_kk'],
        ]);

        flash()->success('Konten diturunkan ke Non-KK.');
        $this->dispatch('qc-saved', contentId: $this->contentId);
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

        $qcRecord = TiktokQc::updateOrCreate(
            ['content_id' => $this->contentId],
            $data,
        );

        $criteriaKeys = [
            1 => 'k1_audio_original',
            2 => 'k2_demo_penggunaan',
            3 => 'k3_produk_visible',
            4 => 'k4_manfaat_verbal',
            5 => 'k5_tambahan',
            6 => 'k6_tambahan',
        ];

        $criteriaLabels = [
            5 => 'k5_label',
            6 => 'k6_label',
        ];

        foreach ($criteriaKeys as $no => $key) {
            $value = $data[$key] ?? null;
            if ($value !== null) {
                QcCriteriaResult::updateOrCreate(
                    ['qc_check_id' => $qcRecord->id, 'criteria_no' => $no],
                    [
                        'result' => $value,
                        'note' => isset($criteriaLabels[$no]) ? ($this->qc[$criteriaLabels[$no]] ?? null) : null,
                    ],
                );
            }
        }

        $qcRecord->load('criteriaResults');

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
