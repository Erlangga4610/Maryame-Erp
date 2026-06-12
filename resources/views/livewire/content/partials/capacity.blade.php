<div x-show="$wire.viewMode === 'capacity'" class="space-y-6">
    {{-- Week navigation --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <flux:button variant="outline" wire:click="capacityPreviousWeek" icon="chevron-left" />
            <h3 class="text-base font-semibold text-zinc-800 dark:text-white">
                {{ $capacityWeekStart->format('d M') }} — {{ $this->capacity_week_end->format('d M Y') }}
            </h3>
            <flux:button variant="outline" wire:click="capacityGoToToday" size="sm">Minggu Ini</flux:button>
        </div>
    </div>

    {{-- Summary bar --}}
    <div class="flex flex-wrap gap-3">
        <div class="text-xs px-3 py-1.5 rounded-full bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300">
            OK: {{ count(array_filter($this->capacity_data, fn($row) => $row['total'] <= $row['max'])) }}
        </div>
        <div class="text-xs px-3 py-1.5 rounded-full bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300">
            Over: {{ count(array_filter($this->capacity_data, fn($row) => $row['total'] > $row['max'])) }}
        </div>
    </div>

    {{-- Capacity table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                    <th class="text-left py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">PIC</th>
                    <th class="text-right py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Role</th>
                    <th class="text-right py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Total Jam</th>
                    <th class="text-right py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Max</th>
                    <th class="text-center py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="text-left py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Konten Terkait</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->capacity_data as $picId => $row)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="py-2 px-3 font-medium text-zinc-800 dark:text-white">{{ $row['name'] }}</td>
                        <td class="py-2 px-3 text-right text-zinc-500">{{ $row['role'] }}</td>
                        <td class="py-2 px-3 text-right font-mono {{ $row['total'] > $row['max'] ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ number_format($row['total'], 1) }}
                        </td>
                        <td class="py-2 px-3 text-right font-mono text-zinc-500">{{ number_format($row['max'], 1) }}</td>
                        <td class="py-2 px-3 text-center">
                            @if($row['total'] > $row['max'])
                                <flux:badge size="sm" color="red">Over</flux:badge>
                            @else
                                <flux:badge size="sm" color="green">OK</flux:badge>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            <div class="space-y-0.5">
                                @forelse($row['contents'] as $c)
                                    <div class="text-xs flex items-center gap-2">
                                        <span class="font-mono text-zinc-400">{{ $c['code'] }}</span>
                                        <span class="text-zinc-600 dark:text-zinc-400 truncate max-w-[200px]">{{ $c['theme'] }}</span>
                                        <span class="text-zinc-400">{{ $c['hours'] }}h</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-zinc-400">—</span>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-zinc-400">Tidak ada data kapasitas untuk minggu ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 5-Step Action Panel --}}
    @if(count(array_filter($this->capacity_data, fn($row) => $row['total'] > $row['max'])) > 0)
        <div class="border border-red-200 dark:border-red-800 rounded-lg p-4 bg-red-50 dark:bg-red-900/10">
            <h4 class="text-sm font-semibold text-red-700 dark:text-red-300 mb-3">Over Capacity — 5-Step Action</h4>
            <div class="space-y-2">
                @php $overRows = array_filter($this->capacity_data, fn($row) => $row['total'] > $row['max']); @endphp
                @foreach($overRows as $picId => $row)
                    <div class="flex items-start gap-3 text-xs text-red-600 dark:text-red-400">
                        <span class="font-medium shrink-0 w-20">{{ $row['name'] }}</span>
                        <span>{{ $row['total'] - $row['max'] }}h over ({{ $row['total'] }}h / {{ $row['max'] }}h max)</span>
                        <flux:button size="xs" variant="outline" wire:click="openCapacityEdit({{ $picId }})" class="text-xs">
                            Atur
                        </flux:button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Capacity Edit Modal --}}
    @if($showCapacityEditModal)
        <flux:modal wire:model="showCapacityEditModal" class="w-lg" wire:key="capacity-edit-modal">
            <div class="p-6 space-y-4">
                <h3 class="text-lg font-semibold">Atur Kapasitas — {{ $capacityEditUserName }}</h3>

                <div>
                    <flux:field>
                        <flux:label>Max Jam/Minggu</flux:label>
                        <flux:input type="number" step="0.5" wire:model="capacityEditMaxHours" />
                    </flux:field>
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Konten Minggu Ini:</p>
                    @forelse($capacityEditContents as $c)
                        <div class="flex items-center justify-between text-xs p-2 rounded border border-zinc-200 dark:border-zinc-700">
                            <div>
                                <span class="font-mono text-zinc-400">{{ $c['code'] }}</span>
                                <span class="ml-2 text-zinc-600 dark:text-zinc-400">{{ $c['theme'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-zinc-500">{{ $c['hours'] }}h</span>
                                <flux:button size="xs" variant="ghost" wire:click="capacityEditContent({{ $c['id'] }})">
                                    Edit
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-400">Tidak ada konten</p>
                    @endforelse
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button variant="outline" wire:click="closeCapacityEditModal">Tutup</flux:button>
                    <flux:button variant="primary" color="pink" wire:click="saveCapacitySettings">Simpan</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
