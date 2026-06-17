<?php

namespace App\Livewire\Dashboard;

use App\Content\Enums\ContentStatus;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
use App\Models\Campaign;
use App\Models\Platform;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $selectedContentId = null;

    public function mount()
    {
        $latest = Content::latest()->first();
        $this->selectedContentId = $latest?->id;
    }

    public function selectContent($id)
    {
        $this->selectedContentId = $id;
    }

    public function render()
    {
        $total = Content::count();

        $stats = collect(ContentStatus::cases())->map(fn ($s) => [
            'status' => $s,
            'count' => Content::where('status', $s->value)->count(),
        ])->map(fn ($item) => [
            ...$item,
            'percentage' => $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0,
        ]);

        $campaigns = Campaign::where('is_active', true)
            ->orWhere('end_date', '>=', now())
            ->latest()
            ->limit(5)
            ->get();

        $now = now()->startOfWeek();
        $weekDates = collect(range(0, 6))->map(fn ($d) => $now->copy()->addDays($d));

        $weekContent = Content::with('platform')
            ->whereBetween('publish_date', [$weekDates->first(), $weekDates->last()])
            ->whereIn('status', ['approved', 'scheduled', 'published'])
            ->orderBy('publish_date')
            ->get()
            ->groupBy(fn ($item) => $item->publish_date?->format('Y-m-d'));

        $user = Auth::user();

        $myTasks = Content::with(['platform'])
            ->whereIn('status', ['draft', 'in_production', 'ready_review'])
            ->where(function ($q) use ($user) {
                $q->where('pic_copy_id', $user->id)
                  ->orWhere('pic_visual_id', $user->id)
                  ->orWhere('pic_video_id', $user->id);
            })
            ->orderBy('deadline_produksi')
            ->limit(8)
            ->get();

        $lateCount = Content::whereIn('status', ['draft', 'in_production', 'ready_review', 'approved'])
            ->whereNotNull('deadline_produksi')
            ->where('deadline_produksi', '<', now())
            ->count();

        $kanbanData = collect(ContentStatus::cases())->mapWithKeys(fn ($s) => [
            $s->value => Content::with(['platform', 'picCopy', 'picVisual', 'picVideo'])
                ->where('status', $s->value)
                ->latest()
                ->limit(5)
                ->get(),
        ]);

        $selectedContent = null;
        if ($this->selectedContentId) {
            $selectedContent = Content::with([
                'platform', 'product', 'campaign', 'picCopy', 'picVisual', 'picVideo',
                'approvals.approver', 'brief',
            ])->find($this->selectedContentId);
        }

        $roleCapacity = collect([
            'GVD' => User::role('GVD')->first(),
            'VG' => User::role('VG')->first(),
            'CW' => User::role('CW')->first(),
            'SMS' => User::role('SMS')->first(),
        ])->mapWithKeys(function ($user, $role) {
            if (! $user) return [$role => ['total' => 0, 'active' => 0, 'percentage' => 0]];
            $total = Content::where(function ($q) use ($user) {
                $q->where('pic_copy_id', $user->id)
                  ->orWhere('pic_visual_id', $user->id)
                  ->orWhere('pic_video_id', $user->id);
            })->count();
            $active = Content::whereIn('status', ['draft', 'in_production', 'ready_review'])
                ->where(function ($q) use ($user) {
                    $q->where('pic_copy_id', $user->id)
                      ->orWhere('pic_visual_id', $user->id)
                      ->orWhere('pic_video_id', $user->id);
                })->count();
            $max = max($total, 5);
            return [$role => [
                'total' => $total,
                'active' => $active,
                'percentage' => min(round(($active / $max) * 100), 100),
            ]];
        });

        $assetProducts = Product::where('is_active', true)->limit(6)->get();

        $recentVersions = ContentVersion::with(['content', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        $platforms = Platform::all();

        $allContent = Content::with(['platform'])
            ->whereIn('status', ['approved', 'scheduled', 'published', 'in_production', 'ready_review'])
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.dashboard.dashboard', [
            'total' => $total,
            'stats' => $stats,
            'campaigns' => $campaigns,
            'weekDates' => $weekDates,
            'weekContent' => $weekContent,
            'myTasks' => $myTasks,
            'lateCount' => $lateCount,
            'kanbanData' => $kanbanData,
            'selectedContent' => $selectedContent,
            'roleCapacity' => $roleCapacity,
            'assetProducts' => $assetProducts,
            'recentVersions' => $recentVersions,
            'platforms' => $platforms,
            'allContent' => $allContent,
            'user' => $user,
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
