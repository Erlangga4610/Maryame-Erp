<?php

namespace App\Livewire\PublishingReporting;

use App\Content\Models\Adjustment;
use App\Content\Models\AdjustmentLog;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdjustmentManager extends Component
{
    public $adjustmentId;

    public function mount($adjustmentId)
    {
        $this->adjustmentId = $adjustmentId;
    }

    public function approve()
    {
        $adjustment = Adjustment::findOrFail($this->adjustmentId);

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
        $this->dispatch('adjustment-completed', adjustmentId: $this->adjustmentId);
    }

    public function reject()
    {
        $adjustment = Adjustment::findOrFail($this->adjustmentId);

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
        $this->dispatch('adjustment-completed', adjustmentId: $this->adjustmentId);
    }

    public function render()
    {
        $adjustment = Adjustment::with(['content', 'requester'])->findOrFail($this->adjustmentId);

        return view('livewire.publishing-reporting.adjustment-manager', [
            'adjustment' => $adjustment,
        ])->layout('layouts.admin', ['title' => 'Adjustment #'.$adjustment->id]);
    }
}
