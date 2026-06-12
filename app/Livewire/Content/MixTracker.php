<?php

namespace App\Livewire\Content;

use App\Content\Enums\TiktokSubtype;
use App\Content\Models\Content;
use App\Models\Platform;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MixTracker extends Component
{
    public $softSellingLimit = 2;

    public $weeksBack = 12;

    public function render()
    {
        $tkmPlatform = Platform::where('code', 'TKM')->first();
        $weeklyData = collect();

        if ($tkmPlatform) {
            $rows = Content::select(
                'tiktok_subtype',
                DB::raw("to_char(publish_date, 'IYYY') as iso_year"),
                DB::raw("to_char(publish_date, 'IW') as iso_week"),
                DB::raw("to_char(publish_date, 'IYYY-\"W\"IW') as iso_week_label"),
                DB::raw('COUNT(*) as total')
            )
                ->where('platform_id', $tkmPlatform->id)
                ->whereNotNull('tiktok_subtype')
                ->whereNotNull('publish_date')
                ->where('publish_date', '>=', now()->subWeeks($this->weeksBack)->startOfWeek())
                ->groupBy('tiktok_subtype', 'iso_year', 'iso_week', 'iso_week_label')
                ->orderBy('iso_year', 'desc')
                ->orderBy('iso_week', 'desc')
                ->get();

            $grouped = $rows->groupBy('iso_week_label');

            $weeks = collect();
            foreach ($grouped as $weekLabel => $items) {
                $kkInteraktif = $items->where('tiktok_subtype', 'kk_interaktif')->sum('total');
                $kkSoftSelling = $items->where('tiktok_subtype', 'kk_soft_selling')->sum('total');
                $nonKk = $items->where('tiktok_subtype', 'non_kk')->sum('total');
                $total = $items->sum('total');

                $weeks->push([
                    'label' => $weekLabel,
                    'kk_interaktif' => $kkInteraktif,
                    'kk_soft_selling' => $kkSoftSelling,
                    'non_kk' => $nonKk,
                    'total' => $total,
                    'kk_total' => $kkInteraktif + $kkSoftSelling,
                    'kk_pct' => $total > 0 ? round(($kkInteraktif + $kkSoftSelling) / $total * 100) : 0,
                    'warning' => $kkSoftSelling > $this->softSellingLimit,
                    'warning_type' => $kkSoftSelling > $this->softSellingLimit ? 'soft_selling' : null,
                ]);
            }

            $weeklyData = $weeks->sortByDesc('label')->values();
        }

        $maxWeekly = $weeklyData->max('total') ?? 1;
        $overallTotal = $weeklyData->sum('total');
        $overallKk = $weeklyData->sum('kk_total');
        $overallPct = $overallTotal > 0 ? round($overallKk / $overallTotal * 100) : 0;

        return view('livewire.content.mix-tracker', [
            'weeklyData' => $weeklyData,
            'maxWeekly' => $maxWeekly,
            'overallTotal' => $overallTotal,
            'overallKk' => $overallKk,
            'overallPct' => $overallPct,
        ])->layout('layouts.admin', ['title' => 'Mix Tracker TikTok']);
    }
}
