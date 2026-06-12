@if($showVersionModal)
    <flux:modal size="lg" wire:model="showVersionModal" wire:key="version-history-modal">
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4">Riwayat Versi</h3>

            @php $versions = $this->version_history; @endphp

            @if($versions->isEmpty())
                <p class="text-zinc-500 text-sm">Belum ada riwayat versi.</p>
            @else
                <div class="space-y-3 max-h-[60vh] overflow-y-auto">
                    @foreach($versions as $version)
                        @php $snapshot = $version->data; @endphp
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 {{ $version->is_archived ? 'opacity-50' : '' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-zinc-800 dark:text-white">v{{ $version->version }}</span>
                                    @if($version->is_archived)
                                        <span class="text-xs bg-zinc-200 dark:bg-zinc-600 text-zinc-500 dark:text-zinc-400 px-1.5 py-0.5 rounded">Diarsipkan</span>
                                    @endif
                                </div>
                                <span class="text-xs text-zinc-400">
                                    {{ $version->created_at->format('d M Y H:i') }}
                                    @if($version->creator)
                                        oleh {{ $version->creator->name }}
                                    @endif
                                </span>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 space-y-1.5">
                                @if(isset($snapshot['content_code']))
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-zinc-400">Kode:</span>
                                        <span class="font-mono">{{ $snapshot['content_code'] }}</span>
                                    </div>
                                @endif
                                @if(isset($snapshot['theme']))
                                    <div><span class="font-medium text-zinc-400">Tema:</span> {{ $snapshot['theme'] }}</div>
                                @endif
                                @if(isset($snapshot['status']))
                                    <div>
                                        <span class="font-medium text-zinc-400">Status:</span>
                                        <span class="bg-zinc-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded">{{ $snapshot['status'] }}</span>
                                    </div>
                                @endif
                                @if(isset($snapshot['priority']))
                                    <div><span class="font-medium text-zinc-400">Priority:</span> {{ $snapshot['priority'] }}</div>
                                @endif
                                @if(isset($snapshot['final_asset_link']) && $snapshot['final_asset_link'])
                                    <div><span class="font-medium text-zinc-400">Asset:</span> {{ \Illuminate\Support\Str::after($snapshot['final_asset_link'], '/') }}</div>
                                @endif
                                @if(isset($snapshot['thumbnail_link']) && $snapshot['thumbnail_link'])
                                    <div><span class="font-medium text-zinc-400">Thumbnail:</span> {{ \Illuminate\Support\Str::after($snapshot['thumbnail_link'], '/') }}</div>
                                @endif
                            </div>
                            @unless($version->is_archived)
                                <div class="flex justify-end mt-2 pt-2 border-t border-zinc-100 dark:border-zinc-700/50">
                                    <flux:button type="button" size="xs" variant="ghost" wire:click="archiveVersion({{ $version->id }})" class="text-xs text-zinc-400 hover:text-red-500">
                                        Arsipkan
                                    </flux:button>
                                </div>
                            @endunless
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
