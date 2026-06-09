<div class="space-y-6">
    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
        @foreach($statuses as $status)
            <flux:card class="p-4" x-data>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $status->label() }}</p>
                <p class="text-2xl font-bold mt-1 {{ match($status->color()) {
                    'gray' => 'text-zinc-600 dark:text-zinc-300',
                    'blue' => 'text-blue-600 dark:text-blue-400',
                    'amber' => 'text-amber-600 dark:text-amber-400',
                    'green' => 'text-green-600 dark:text-green-400',
                    'purple' => 'text-purple-600 dark:text-purple-400',
                    'emerald' => 'text-emerald-600 dark:text-emerald-400',
                    default => 'text-zinc-600'
                } }}">
                    {{ $stats[$status->value] ?? 0 }}
                </p>
            </flux:card>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Content by Platform --}}
        <flux:card class="p-5">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4">Konten per Platform</h3>

            @php $maxCount = $byPlatform->max('contents_count') ?: 1; @endphp

            <div class="space-y-3">
                @forelse($byPlatform as $platform)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-zinc-600 dark:text-zinc-400">{{ $platform->name }}</span>
                            <span class="font-medium text-zinc-800 dark:text-white">{{ $platform->contents_count }}</span>
                        </div>
                        <div class="h-2 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-pink-500 rounded-full transition-all"
                                style="width: {{ ($platform->contents_count / $maxCount) * 100 }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Belum ada konten</p>
                @endforelse
            </div>
        </flux:card>

        {{-- Upcoming Schedule --}}
        <flux:card class="p-5">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4">Jadwal Mendatang</h3>

            <div class="space-y-3">
                @forelse($upcoming as $item)
                    <div class="flex items-center gap-3">
                        <div class="size-9 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold text-zinc-600 dark:text-zinc-300">{{ $item->publish_date->format('d') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-zinc-800 dark:text-white truncate">{{ $item->theme }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $item->platform->code }} · {{ $item->publish_date->format('M Y') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Tidak ada jadwal terdekat</p>
                @endforelse
            </div>
        </flux:card>

        {{-- Recent Activity --}}
        <flux:card class="p-5">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4">Konten Terbaru</h3>

            <div class="space-y-3">
                @forelse($recent as $item)
                    <div class="flex items-center gap-3">
                        <flux:badge size="sm" :color="$item->status->color()" class="shrink-0">
                            {{ $item->status->label() }}
                        </flux:badge>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-zinc-800 dark:text-white truncate">{{ $item->theme }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->content_code }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Belum ada konten</p>
                @endforelse
            </div>
        </flux:card>
    </div>
</div>
