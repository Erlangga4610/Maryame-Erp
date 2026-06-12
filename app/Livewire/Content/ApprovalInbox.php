<?php

namespace App\Livewire\Content;

use App\Content\Models\Adjustment;
use App\Content\Models\AdjustmentLog;
use App\Content\Models\Approval;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
use App\Enums\ApprovalStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ApprovalInbox extends Component
{
    public $filterStage = '';

    public $showApproveModal = false;

    public $approveContentId = null;

    public $approveStage = null;

    public $approveNotes = '';

    public function render()
    {
        $user = Auth::user();
        $userRole = $user?->roles->first()?->name;

        $stageMap = [
            'CW' => 'cw',
            'CSP' => 'csp',
            'SMS' => 'sms',
            'RnD' => 'rnd',
            'Legal' => 'legal',
        ];

        $stage = $user?->isSuperAdmin() ? ($this->filterStage ?: null) : ($stageMap[$userRole] ?? null);

        $query = Content::with(['platform', 'picCopy', 'approvals'])
            ->where('status', 'ready_review');

        if ($stage) {
            $query->whereHas('approvals', function ($q) use ($stage) {
                $q->where('stage', $stage)
                    ->where('status', 'pending');
            });
        } else {
            $query->whereHas('approvals', function ($q) {
                $q->where('status', 'pending');
            });
        }

        $contents = $query->orderBy('created_at', 'desc')->get();

        // Pending adjustments — MC/BM (major), CSP (reactive), atau Super Admin (all)
        $adjustments = collect();
        if ($user?->isSuperAdmin() || $user?->hasRole('MC_BM') || $user?->hasRole('CSP')) {
            $adjustments = \App\Content\Models\Adjustment::with(['content', 'requester'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.content.approval-inbox', [
            'contents' => $contents,
            'adjustments' => $adjustments,
            'userRole' => $userRole,
        ])->layout('layouts.admin', ['title' => 'Approval Inbox']);
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

        $allApproved = Approval::where('content_id', $this->approveContentId)
            ->where('status', '!=', ApprovalStatus::APPROVED->value)
            ->doesntExist();

        if ($allApproved) {
            $content = Content::findOrFail($this->approveContentId);
            $content->status = 'approved';
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
        $content->status = 'in_production';
        $content->save();

        flash()->success('Revisi diminta. Konten dikembalikan ke In Production.');
        $this->cancelApprove();
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
}
