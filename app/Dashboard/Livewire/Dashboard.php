<?php

namespace App\Dashboard\Livewire;

use App\Content\Enums\ContentStatus;
use App\Content\Models\Content;
use App\Models\Platform;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $total = Content::count();
        $stats = collect(ContentStatus::cases())->mapWithKeys(fn ($s) => [
            $s->value => Content::where('status', $s->value)->count(),
        ]);

        $byPlatform = Platform::withCount('contents')->whereHas('contents')->get();

        $upcoming = Content::with(['platform'])
            ->whereNotNull('publish_date')
            ->whereIn('status', ['approved', 'scheduled'])
            ->orderBy('publish_date')
            ->limit(6)
            ->get();

        $recent = Content::with(['platform'])
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard.dashboard', [
            'total' => $total,
            'stats' => $stats,
            'byPlatform' => $byPlatform,
            'upcoming' => $upcoming,
            'recent' => $recent,
            'statuses' => ContentStatus::cases(),
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
