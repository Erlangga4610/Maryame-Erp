<?php

namespace App\Livewire\Content;

use App\Content\Models\Content;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyTasks extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public function startProduction($id)
    {
        $content = Content::findOrFail($id);

        if ($content->status->value !== 'draft') {
            flash()->error('Hanya konten Draft yang bisa mulai produksi.');
            return;
        }

        $content->update(['status' => 'in_production']);
        flash()->success('Konten masuk ke tahap produksi!');
    }

    public function submitForReview($id)
    {
        $content = Content::findOrFail($id);

        if ($content->status->value !== 'in_production') {
            flash()->error('Konten harus In Production.');
            return;
        }

        if (! $content->final_asset_link && ! $content->thumbnail_link) {
            flash()->error('Minimal 1 asset harus diupload sebelum submit.');
            return;
        }

        $content->update(['status' => 'ready_review']);
        flash()->success('Konten siap direview!');
    }

    public function render()
    {
        $user = Auth::user();

        $query = Content::with(['platform', 'picCopy', 'picVisual', 'picVideo'])
            ->where(function ($q) use ($user) {
                $q->where('pic_copy_id', $user->id)
                    ->orWhere('pic_visual_id', $user->id)
                    ->orWhere('pic_video_id', $user->id);
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('content_code', 'like', '%' . $this->search . '%')
                    ->orWhere('theme', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $tasks = $query->orderByRaw("CASE status
                WHEN 'draft' THEN 1 WHEN 'in_production' THEN 2
                WHEN 'ready_review' THEN 3 WHEN 'approved' THEN 4
                WHEN 'scheduled' THEN 5 WHEN 'published' THEN 6
                ELSE 7 END")
            ->orderBy('deadline_produksi', 'asc')
            ->paginate(20);

        $statusCounts = Content::where(function ($q) use ($user) {
            $q->where('pic_copy_id', $user->id)
                ->orWhere('pic_visual_id', $user->id)
                ->orWhere('pic_video_id', $user->id);
        })
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('livewire.content.my-tasks', [
            'tasks' => $tasks,
            'statusCounts' => $statusCounts,
        ])->layout('layouts.admin', ['title' => 'My Tasks']);
    }
}
