@if($showAdjustmentModal)
    <flux:modal size="lg" wire:model="showAdjustmentModal" wire:key="adjustment-log-modal">
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4">Log Penyesuaian Konten</h3>

            @php $logs = $this->adjustment_logs; @endphp

            @if($logs->isEmpty())
                <p class="text-zinc-500 text-sm">Belum ada perubahan.</p>
            @else
                <div class="space-y-2">
                    @foreach($logs as $log)
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-zinc-500 uppercase tracking-wide">
                                    {{ str_replace('_', ' ', $log->field) }}
                                </span>
                                <span class="text-xs text-zinc-400">
                                    {{ $log->created_at->format('d M Y H:i') }}
                                    oleh {{ $log->user?->name ?? 'System' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-red-50 dark:bg-red-900/20 rounded p-2 text-red-700 dark:text-red-300">
                                    <span class="font-medium">Sebelum:</span>
                                    <span class="block mt-0.5">{{ $log->old_value ?? '-' }}</span>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 rounded p-2 text-green-700 dark:text-green-300">
                                    <span class="font-medium">Sesudah:</span>
                                    <span class="block mt-0.5">{{ $log->new_value ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700 mt-4">
                <flux:button type="button" variant="outline" wire:click="closeAdjustmentModal">Tutup</flux:button>
            </div>
        </div>
    </flux:modal>
@endif
