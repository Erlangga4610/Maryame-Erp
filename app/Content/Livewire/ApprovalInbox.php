<?php

namespace App\Content\Livewire;

use App\Content\Models\Approval;
use App\Content\Models\Content;
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

        $query = Content::with(['platform', 'picCopy', 'approvals'])
            ->where('status', 'ready_review');

        if (in_array($userRole, ['MC_BM', 'Legal'])) {
            $stage = $userRole === 'MC_BM' ? 'mc_bm' : 'legal';
            $query->whereHas('approvals', function ($q) use ($stage) {
                $q->where('stage', $this->filterStage ?: $stage)
                    ->where('status', 'pending');
            });
        } else {
            $query->whereHas('approvals', function ($q) {
                $q->where('status', 'pending');
            });
        }

        $contents = $query->orderBy('created_at', 'desc')->get();

        return view('content.approval-inbox', [
            'contents' => $contents,
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
}
