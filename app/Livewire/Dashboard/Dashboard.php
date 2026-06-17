<?php

namespace App\Livewire\Dashboard;

use App\Content\Enums\ContentStatus;
use App\Content\Models\Asset;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
use App\Models\Campaign;
use App\Models\Platform;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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

    public function toggleTask($contentId)
    {
        $completed = Session::get('dashboard_completed_tasks', []);
        if (in_array($contentId, $completed)) {
            $completed = array_values(array_filter($completed, fn ($id) => $id != $contentId));
        } else {
            $completed[] = $contentId;
        }
        Session::put('dashboard_completed_tasks', $completed);
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

        $completedTasks = Session::get('dashboard_completed_tasks', []);

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
        $assets = collect();
        if ($this->selectedContentId) {
            $selectedContent = Content::with([
                'platform', 'product', 'campaign', 'picCopy', 'picVisual', 'picVideo',
                'approvals.approver', 'brief', 'assets.uploader',
            ])->find($this->selectedContentId);

            if ($selectedContent) {
                $assets = $selectedContent->assets;
            }
        }

        // role → PIC field mapping
        $roleFieldMap = [
            'CW' => 'pic_copy_id',
            'GVD' => 'pic_visual_id',
            'VG' => 'pic_video_id',
            'SMS' => 'pic_copy_id', // SMS uses pic_copy workload
        ];

        // est hours per role field
        $roleHoursMap = [
            'CW' => 'est_copy_hours',
            'GVD' => 'est_visual_hours',
            'VG' => 'est_video_hours',
            'SMS' => 'est_copy_hours',
        ];

        $weekHours = 40;

        $roleCapacity = collect($roleFieldMap)->mapWithKeys(function ($field, $role) use ($roleHoursMap, $weekHours) {
            $user = User::role($role)->first();
            if (! $user) return [$role => ['total' => 0, 'used' => 0, 'percentage' => 0]];

            $hoursCol = $roleHoursMap[$role];

            $used = Content::where($field, $user->id)
                ->whereIn('status', ['draft', 'in_production', 'ready_review', 'approved'])
                ->sum($hoursCol);

            return [$role => [
                'total' => $weekHours,
                'used' => round($used, 1),
                'percentage' => $weekHours > 0 ? min(round(($used / $weekHours) * 100), 100) : 0,
            ]];
        });

        $assetProducts = Product::where('is_active', true)->limit(6)->get();

        $recentVersions = ContentVersion::with(['content', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        $platforms = Platform::all();

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
            'selectedContentId' => $this->selectedContentId,
            'assets' => $assets,
            'roleCapacity' => $roleCapacity,
            'assetProducts' => $assetProducts,
            'recentVersions' => $recentVersions,
            'platforms' => $platforms,
            'user' => $user,
            'completedTasks' => $completedTasks,
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
