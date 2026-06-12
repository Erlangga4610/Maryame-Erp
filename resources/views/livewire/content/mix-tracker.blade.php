<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Mix Tracker TikTok</flux:heading>
        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <span>KK Soft Selling limit:</span>
            <flux:input type="number" wire:model.live="softSellingLimit" class="w-16 text-center" min="1" max="10" />
            <span>/ minggu</span>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-800">
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Total TKM (12 minggu)</p>
            <p class="text-2xl font-bold text-zinc-800 dark:text-white">{{ $overallTotal }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-800">
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Total KK</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $overallKk }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-800">
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">KK Ratio</p>
            <p class="text-2xl font-bold {{ $overallPct >= 50 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $overallPct }}%</p>
        </div>
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-800">
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Minggu dengan warning</p>
            <p class="text-2xl font-bold {{ $weeklyData->where('warning', true)->count() > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-800 dark:text-white' }}">{{ $weeklyData->where('warning', true)->count() }}</p>
        </div>
    </div>

    {{-- Weekly table --}}
    <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-800/50 text-left">
                    <th class="px-4 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Minggu</th>
                    <th class="px-4 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300 text-center">KK Interaktif</th>
                    <th class="px-4 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300 text-center">KK Soft Selling</th>
                    <th class="px-4 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300 text-center">Non-KK</th>
                    <th class="px-4 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300 text-center">Total</th>
                    <th class="px-4 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300 text-center">KK%</th>
                    <th class="px-4 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300 text-center">Warning</th>
                    <th class="px-4 py-2.5 font-semibold text-zinc-600 dark:text-zinc-300">Distribusi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($weeklyData as $week)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-2.5 font-mono text-xs text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $week['label'] }}
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="font-semibold text-purple-600 dark:text-purple-400">{{ $week['kk_interaktif'] }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="font-semibold {{ $week['warning'] ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $week['kk_soft_selling'] }}
                                @if($week['warning'])
                                    <span class="inline-block ml-1 text-xs">⚠️</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="font-semibold text-zinc-500 dark:text-zinc-400">{{ $week['non_kk'] }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-center font-semibold text-zinc-800 dark:text-white">
                            {{ $week['total'] }}
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="{{ $week['kk_pct'] >= 50 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }} font-semibold">
                                {{ $week['kk_pct'] }}%
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            @if($week['warning'])
                                <span class="inline-flex items-center gap-1 text-xs bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 px-2 py-0.5 rounded-full whitespace-nowrap">
                                    Soft Selling > {{ $softSellingLimit }}
                                </span>
                            @else
                                <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 min-w-[140px]">
                            @php
                                $kkInter = $week['kk_interaktif'];
                                $kkSoft = $week['kk_soft_selling'];
                                $non = $week['non_kk'];
                                $total = max($week['total'], 1);
                                $interW = round($kkInter / $total * 100);
                                $softW = round($kkSoft / $total * 100);
                                $nonW = 100 - $interW - $softW;
                            @endphp
                            <div class="flex h-5 rounded overflow-hidden text-[10px] font-medium text-white">
                                @if($kkInter > 0)
                                    <div class="bg-purple-500 flex items-center justify-center" style="width: {{ $interW }}%">{{ $kkInter }}</div>
                                @endif
                                @if($kkSoft > 0)
                                    <div class="bg-amber-400 flex items-center justify-center" style="width: {{ $softW }}%">{{ $kkSoft }}</div>
                                @endif
                                @if($non > 0)
                                    <div class="bg-zinc-300 dark:bg-zinc-500 flex items-center justify-center" style="width: {{ $nonW }}%">{{ $non }}</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-zinc-400 dark:text-zinc-500">
                            Belum ada data TikTok dengan QC tersubmit dalam 12 minggu terakhir.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Legend --}}
    <div class="flex items-center gap-6 text-xs text-zinc-500 dark:text-zinc-400">
        <div class="flex items-center gap-1.5">
            <span class="size-3 rounded bg-purple-500"></span>
            <span>KK Interaktif</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="size-3 rounded bg-amber-400"></span>
            <span>KK Soft Selling</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="size-3 rounded bg-zinc-300 dark:bg-zinc-500"></span>
            <span>Non-KK</span>
        </div>
        <div class="flex items-center gap-1.5 ml-4">
            <span class="size-3 rounded bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700"></span>
            <span>Warning (Soft Selling > {{ $softSellingLimit }}/minggu)</span>
        </div>
    </div>
</div>
