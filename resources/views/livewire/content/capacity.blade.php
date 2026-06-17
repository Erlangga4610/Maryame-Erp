<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="lg">Capacity Planning</flux:heading>
            <flux:subheading>Perencanaan kapasitas produksi per PIC</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="outline" wire:click="capacityPreviousWeek" icon="chevron-left" />
            <h3 class="text-base font-semibold text-zinc-800 dark:text-white">
                {{ $capacityWeekStart->format('d M') }} — {{ $this->capacity_week_end->format('d M Y') }}
            </h3>
            <flux:button variant="outline" wire:click="capacityGoToToday" size="sm">Minggu Ini</flux:button>
            <flux:button variant="outline" wire:click="capacityNextWeek" icon="chevron-right" />
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <div class="text-xs px-3 py-1.5 rounded-full bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300">
            OK: {{ count(array_filter($this->capacity_data, fn($row) => $row['total'] <= $row['max'])) }}
        </div>
        <div class="text-xs px-3 py-1.5 rounded-full bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300">
            Over: {{ count(array_filter($this->capacity_data, fn($row) => $row['total'] > $row['max'])) }}
        </div>
        @php
            $overCount = count(array_filter($this->capacity_data, fn($row) => $row['total'] > $row['max']));
            $resolvedCount = count(array_filter($this->capacity_data, fn($row) => $row['total'] > $row['max'] && $row['confirmed_by']));
        @endphp
        @if($overCount > 0)
            <div class="text-xs px-3 py-1.5 rounded-full {{ $overCount === $resolvedCount ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300' : 'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300' }}">
                Teratasi: {{ $resolvedCount }}/{{ $overCount }}
            </div>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                    <th class="text-left py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">PIC</th>
                    <th class="text-right py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Role</th>
                    <th class="text-right py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Total Jam</th>
                    <th class="text-right py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Max</th>
                    <th class="text-center py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="text-center py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Resolusi</th>
                    <th class="text-center py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Confirmed</th>
                    <th class="text-left py-2 px-3 font-medium text-zinc-500 dark:text-zinc-400">Konten</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->capacity_data as $picId => $row)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="py-2 px-3 font-medium text-zinc-800 dark:text-white">
                            <div class="flex items-center gap-2">
                                <span>{{ $row['name'] }}</span>
                                <flux:button size="xs" variant="ghost" wire:click="openCapacityEdit({{ $picId }})" icon="pencil" class="opacity-50 hover:opacity-100" />
                            </div>
                        </td>
                        <td class="py-2 px-3 text-right text-zinc-500">{{ $row['role'] }}</td>
                        <td class="py-2 px-3 text-right font-mono {{ $row['total'] > $row['max'] ? 'text-red-600 dark:text-red-400 font-bold' : 'text-green-600 dark:text-green-400' }}">
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
                        <td class="py-2 px-3 text-center">
                            @if($row['resolution_step'] > 0)
                                <flux:badge size="sm" color="blue">Step {{ $row['resolution_step'] }}</flux:badge>
                                <div class="text-xs text-zinc-500 mt-1 max-w-[150px] truncate">{{ $row['resolution_notes'] }}</div>
                            @else
                                <span class="text-xs text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($row['confirmed_by'])
                                <flux:badge size="sm" color="green">OK</flux:badge>
                                <div class="text-xs text-zinc-500 mt-1">{{ $row['confirmed_by'] }}</div>
                            @else
                                <span class="text-xs text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            <div class="space-y-0.5 max-w-[200px]">
                                @forelse($row['contents'] as $c)
                                    <div class="text-xs flex items-center gap-2">
                                        <span class="font-mono text-zinc-400">{{ $c['code'] }}</span>
                                        <span class="text-zinc-600 dark:text-zinc-400 truncate">{{ $c['theme'] }}</span>
                                        <span class="text-zinc-400 shrink-0">{{ $c['hours'] }}h</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-zinc-400">—</span>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-zinc-400">Tidak ada data kapasitas untuk minggu ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php
        $overRows = array_filter($this->capacity_data, fn($row) => $row['total'] > $row['max']);
        $stepLabels = [
            1 => 'Redistribusi beban internal',
            2 => 'Prioritas ulang (campaign tetap jalan, rutin digeser)',
            3 => 'Sederhanakan format (mis. 3 platform → 2 platform)',
            4 => 'Freelancer/vendor (butuh approval MC + Finance)',
            5 => 'Eskalasi ke MC untuk tambah tim permanen',
        ];
    @endphp

    @if(count($overRows) > 0)
        <div class="border border-red-200 dark:border-red-800 rounded-lg p-5 bg-red-50 dark:bg-red-900/10 space-y-5">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-red-700 dark:text-red-300">Over Capacity — 5-Step Action Required</h4>
                @php $allResolved = collect($overRows)->every(fn($r) => $r['confirmed_by']); @endphp
                @if($allResolved)
                    <flux:badge color="green" size="sm">Semua Teratasi</flux:badge>
                @else
                    <flux:badge color="red" size="sm">Perlu Tindakan</flux:badge>
                @endif
            </div>

            @foreach($overRows as $picId => $row)
                <div class="border border-red-100 dark:border-red-800/50 rounded-lg p-4 bg-white dark:bg-red-950/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="font-medium text-sm text-red-800 dark:text-red-200">{{ $row['name'] }}</span>
                            <span class="text-xs text-red-600 dark:text-red-400">{{ number_format($row['total'] - $row['max'], 1) }}h over ({{ number_format($row['total'], 1) }}h / {{ number_format($row['max'], 1) }}h)</span>
                        </div>
                        @if($row['confirmed_by'])
                            <flux:badge color="green" size="sm">✓ {{ $row['confirmed_by'] }}</flux:badge>
                        @endif
                    </div>

                    @if(!$row['confirmed_by'])
                        <div>
                            <label class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Pilih Step Tindakan:</label>
                            <select wire:model.live="capacityResolutionStep" class="mt-1 block w-full text-sm rounded border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800">
                                <option value="0">— Pilih Step —</option>
                                @foreach($stepLabels as $step => $label)
                                    <option value="{{ $step }}">Step {{ $step }}: {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Catatan Tindakan:</label>
                            <textarea wire:model.live="capacityResolutionNotes" rows="2" class="mt-1 block w-full text-sm rounded border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800" placeholder="Jelaskan tindakan yang diambil..."></textarea>
                        </div>
                        <div class="flex items-center gap-2 pt-2">
                            <flux:button size="sm" wire:click="saveCapacityResolution({{ $picId }})" variant="primary">Simpan Tindakan</flux:button>
                            <flux:button size="sm" wire:click="confirmCapacity({{ $picId }})" variant="outline">✓ Tandai Teratasi</flux:button>
                        </div>
                    @endif

                    @if($row['resolution_step'] > 0)
                        <div class="text-xs text-zinc-500 bg-zinc-50 dark:bg-zinc-800/50 rounded p-2">
                            <span class="font-medium">Step {{ $row['resolution_step'] }}:</span>
                            {{ $stepLabels[$row['resolution_step']] ?? '—' }}
                            @if($row['resolution_notes'])
                                <br><span class="italic">Catatan:</span> {{ $row['resolution_notes'] }}
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

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
                            <span class="text-zinc-500">{{ $c['hours'] }}h</span>
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
