<?php

namespace App\Livewire\ApprovalPipeline;

use App\Content\Models\Approval;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
use App\Enums\ApprovalStatus;
use App\Enums\ContentStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ApprovalWorkflow extends Component
{
    public $contentId;

    public $stage;

    public $showApproveModal = false;

    public $approveNotes = '';

    public function mount($contentId, $stage)
    {
        $this->contentId = $contentId;
        $this->stage = $stage;
    }

    public function confirmApprove()
    {
        $this->showApproveModal = true;
    }

    public function cancelApprove()
    {
        $this->showApproveModal = false;
        $this->approveNotes = '';
    }

    public function approveContent()
    {
        $approval = Approval::where('content_id', $this->contentId)
            ->where('stage', $this->stage)
            ->firstOrFail();

        $approval->status = ApprovalStatus::APPROVED->value;
        $approval->approver_id = Auth::id();
        $approval->notes = $this->approveNotes;
        $approval->save();

        $content = Content::findOrFail($this->contentId);

        $nextStage = match ($this->stage) {
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
        $this->dispatch('approval-completed', contentId: $this->contentId);
    }

    public function reviseContent()
    {
        $approval = Approval::where('content_id', $this->contentId)
            ->where('stage', $this->stage)
            ->firstOrFail();

        $approval->status = ApprovalStatus::REVISION->value;
        $approval->approver_id = Auth::id();
        $approval->notes = $this->approveNotes;
        $approval->save();

        $content = Content::findOrFail($this->contentId);
        $content->status = ContentStatus::IN_PRODUCTION;
        $content->save();

        flash()->success('Revisi diminta. Konten dikembalikan ke In Production.');
        $this->cancelApprove();
        $this->dispatch('approval-completed', contentId: $this->contentId);
    }

    public function render()
    {
        $approval = Approval::where('content_id', $this->contentId)
            ->where('stage', $this->stage)
            ->firstOrFail();

        $content = Content::with('platform')->findOrFail($this->contentId);

        return view('livewire.approval-pipeline.approval-workflow', [
            'approval' => $approval,
            'content' => $content,
        ])->layout('layouts.admin', ['title' => 'Approval #'.$content->content_code]);
    }
}
