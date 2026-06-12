<div class="space-y-6">
    {{-- Header + Filters --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <flux:button size="sm" variant="outline" wire:click="previousWeek">
                <flux:icon.chevron-left class="size-4" />
            </flux:button>

            <div class="text-center min-w-[200px]">
                <span class="text-sm font-semibold text-zinc-800 dark:text-white">
                    {{ $weekLabelStart->isoFormat('D MMM YYYY') }} — {{ $weekLabelEnd->isoFormat('D MMM YYYY') }}
                </span>
            </div>

            <flux:button size="sm" variant="outline" wire:click="nextWeek">
                <flux:icon.chevron-right class="size-4" />
            </flux:button>

            <flux:button size="sm" variant="filled" wire:click="goToCurrentWeek">
                Minggu Ini
            </flux:button>
        </div>

        <div class="flex gap-2">
            <flux:select wire:model.live="selectedPlatform" class="w-40">
                <option value="">Semua Platform</option>
                @foreach($platforms as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="selectedStatus" class="w-40">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </flux:select>

            <flux:input wire:model.live="search" placeholder="Cari konten..." class="w-48" />
        </div>
    </div>

    {{-- Week Grid --}}
    <div class="grid grid-cols-7 gap-2">
        @foreach($days as $day)
            <div class="min-h-[300px] rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
                {{-- Day Header --}}
                <div class="px-3 py-2 text-center border-b border-zinc-200 dark:border-zinc-700 {{ $day['isToday'] ? 'bg-pink-50 dark:bg-pink-900/20' : ($day['isPast'] ? 'bg-zinc-50 dark:bg-zinc-800/50' : '') }}">
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $day['label'] }}</div>
                    <div class="text-lg font-bold {{ $day['isToday'] ? 'text-pink-600 dark:text-pink-400' : 'text-zinc-800 dark:text-white' }}">
                        {{ $day['day'] }}
                        <span class="text-xs font-normal text-zinc-400 dark:text-zinc-500"> {{ $day['month'] }}</span>
                    </div>
                </div>

                {{-- Content Cards --}}
                <div class="p-1.5 space-y-1.5">
                    @php $dateKey = $day['date']->format('Y-m-d'); @endphp
                    @forelse(($groupedByDay[$dateKey] ?? collect()) as $content)
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-700 p-2 text-[11px] shadow-xs {{ $content->is_blocked ? 'border-l-4 border-l-red-500 opacity-75' : '' }}">
                            <div class="flex items-start justify-between gap-1">
                                <span class="font-mono text-zinc-500 dark:text-zinc-400 truncate">{{ $content->content_code }}</span>
                                <flux:badge size="sm" :color="$content->priority_badge_color">
                                    {{ strtoupper(substr($content->priority->value, 0, 1)) }}
                                </flux:badge>
                            </div>

                            <p class="font-medium text-zinc-800 dark:text-white mt-0.5 truncate">{{ $content->theme }}</p>

                            <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 mt-1">
                                <span>{{ $content->platform->code }}</span>
                                <span>{{ $content->picCopy?->name ?? '-' }}</span>
                            </div>

                            <div class="mt-1">
                                <flux:badge size="sm" :color="$content->status->color()">
                                    {{ $content->status->label() }}
                                </flux:badge>
                            </div>

                            <div class="flex items-center gap-1 mt-1.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-600">
                                {{-- Mark Briefed --}}
                                <button type="button" wire:click="toggleBriefed({{ $content->id }})"
                                    class="text-[10px] px-1.5 py-0.5 rounded {{ $content->is_briefed ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                    {{ $content->is_briefed ? 'Briefed ✓' : 'Briefed' }}
                                </button>

                                {{-- Block/Unblock --}}
                                <button type="button" wire:click="toggleBlocked({{ $content->id }})"
                                    class="text-[10px] px-1.5 py-0.5 rounded {{ $content->is_blocked ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-zinc-100 dark:bg-zinc-600 text-zinc-600 dark:text-zinc-300' }}">
                                    {{ $content->is_blocked ? 'Blokir' : 'Blokir' }}
                                </button>

                                {{-- Block reason edit (only when blocked) --}}
                                @if($content->is_blocked)
                                    <button type="button" wire:click="openBlockReason({{ $content->id }})"
                                        class="text-[10px] text-red-600 dark:text-red-400 hover:underline ml-auto">
                                        Alasan
                                    </button>
                                @endif
                            </div>

                            @if($content->is_blocked && $content->blocked_reason)
                                <p class="text-[10px] text-red-600 dark:text-red-400 mt-1 truncate" title="{{ $content->blocked_reason }}">
                                    ⚠ {{ $content->blocked_reason }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="flex items-center justify-center h-20 text-zinc-400 dark:text-zinc-500 italic text-[11px]">
                            -
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Block Reason Modal --}}
    <flux:modal name="block-reason-modal" wire:model="showBlockReasonModal" class="w-md">
        <div class="p-6 space-y-4">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Alasan Blokir</h3>
            <flux:textarea wire:model="blockReasonInput" rows="3" placeholder="Jelaskan alasan blokir..." />
            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button type="button" variant="outline" wire:click="$set('showBlockReasonModal', false)">Batal</flux:button>
                <flux:button type="button" variant="primary" color="green" wire:click="saveBlockReason">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Unscheduled Panel --}}
    @if($unscheduled->isNotEmpty())
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                Tanpa Deadline Produksi ({{ $unscheduled->count() }})
            </h3>
            <div class="grid grid-cols-4 gap-2">
                @foreach($unscheduled as $content)
                    <div class="rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 p-2 text-[11px]">
                        <div class="flex items-start justify-between gap-1">
                            <span class="font-mono text-zinc-500 dark:text-zinc-400 truncate">{{ $content->content_code }}</span>
                            <flux:badge size="sm" :color="$content->priority_badge_color">
                                {{ strtoupper(substr($content->priority->value, 0, 1)) }}
                            </flux:badge>
                        </div>
                        <p class="font-medium text-zinc-800 dark:text-white mt-0.5 truncate">{{ $content->theme }}</p>
                        <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 mt-1">
                            <span>{{ $content->platform->code }}</span>
                            <flux:badge size="sm" :color="$content->status->color()">{{ $content->status->label() }}</flux:badge>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
