@if($showVersionModal)
    <flux:modal size="lg" wire:model="showVersionModal" wire:key="version-history-modal">
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4">Riwayat Versi</h3>

            @php $versions = $this->version_history; @endphp

            @if($versions->isEmpty())
                <p class="text-zinc-500 text-sm">Belum ada riwayat versi.</p>
            @else
                <div class="space-y-3">
                    @foreach($versions as $version)
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-zinc-800 dark:text-white">v{{ $version->version }}</span>
                                <span class="text-xs text-zinc-400">
                                    {{ $version->created_at->format('d M Y H:i') }}
                                    @if($version->creator)
                                        oleh {{ $version->creator->name }}
                                    @endif
                                </span>
                            </div>
                            <div class="text-xs text-zinc-500 space-y-1">
                                @php $snapshot = $version->data; @endphp
                                @if(isset($snapshot['theme']))
                                    <div><span class="font-medium">Tema:</span> {{ $snapshot['theme'] }}</div>
                                @endif
                                @if(isset($snapshot['content_code']))
                                    <div><span class="font-medium">Kode:</span> {{ $snapshot['content_code'] }}</div>
                                @endif
                                @if(isset($snapshot['status']))
                                    <div><span class="font-medium">Status:</span> {{ $snapshot['status'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700 mt-4">
                <flux:button type="button" variant="outline" wire:click="closeVersionModal">Tutup</flux:button>
            </div>
        </div>
    </flux:modal>
@endif
