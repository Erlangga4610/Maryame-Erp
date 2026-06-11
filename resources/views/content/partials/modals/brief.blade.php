@if($briefContent)
    <flux:modal wire:model="showBriefModal" class="w-lg" wire:key="brief-modal">
        <div class="p-6">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-zinc-800 dark:text-white">Content Brief</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="font-mono text-xs text-zinc-400 dark:text-zinc-500">{{ $briefContent->content_code }}</span>
                        <flux:badge size="sm" :color="$briefContent->platform->code === 'TKM' ? 'purple' : 'blue'">
                            {{ $briefContent->platform->name }}
                        </flux:badge>
                        <flux:badge size="sm" :color="$briefContent->priority_badge_color">
                            {{ ucfirst($briefContent->priority->value) }}
                        </flux:badge>
                    </div>
                </div>
            </div>

            <p class="text-base font-semibold text-zinc-800 dark:text-white mb-6 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                {{ $briefContent->theme }}
            </p>

            @if($briefContent->final_asset_link || $briefContent->thumbnail_link)
                <div class="mb-6 grid grid-cols-2 gap-3">
                    @if($briefContent->final_asset_link)
                        <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                            <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Final Asset</span>
                            <div class="mt-2">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($briefContent->final_asset_link) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-pink-600 dark:text-pink-400 hover:underline">
                                    <flux:icon.paper-clip class="size-3.5" />
                                    {{ \Illuminate\Support\Str::after($briefContent->final_asset_link, '/') }}
                                </a>
                            </div>
                        </div>
                    @endif
                    @if($briefContent->thumbnail_link)
                        <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                            <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Thumbnail</span>
                            <div class="mt-2">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($briefContent->thumbnail_link) }}" class="w-full h-24 rounded object-cover border border-zinc-200 dark:border-zinc-600" alt="thumbnail preview" />
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @php
                $briefs = [
                    'copy_brief' => ['label' => 'Copy Brief', 'desc' => 'Arahan copywriting, tone of voice, CTA'],
                    'visual_brief' => ['label' => 'Visual Brief', 'desc' => 'Konsep visual, referensi desain, layout'],
                    'video_brief' => ['label' => 'Video Brief', 'desc' => 'Durasi, struktur scene, transisi, BGM'],
                ];
            @endphp

            @foreach($briefs as $key => $info)
                @if($briefContent->$key)
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="size-2 rounded-full bg-pink-500"></div>
                            <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ $info['label'] }}</h4>
                            <span class="text-[11px] text-zinc-400 dark:text-zinc-500 italic">{{ $info['desc'] }}</span>
                        </div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-300 whitespace-pre-line bg-zinc-50 dark:bg-zinc-800/50 rounded-lg px-5 py-4 border border-zinc-200 dark:border-zinc-700 leading-[1.6] text-left">
                            {{ $briefContent->$key }}
                        </div>
                    </div>
                @endif
            @endforeach

            @unless($briefContent->copy_brief || $briefContent->visual_brief || $briefContent->video_brief)
                <div class="flex flex-col items-center justify-center py-12 text-zinc-300 dark:text-zinc-600">
                    <flux:icon.document-text class="size-16 mb-4 stroke-1" />
                    <p class="text-base font-medium text-zinc-400 dark:text-zinc-500">Brief belum diisi</p>
                    <p class="text-sm mt-1">Buka edit konten untuk menambahkan brief pada tab Copy, Visual, atau Video.</p>
                </div>
            @endunless
        </div>
    </flux:modal>
@endif
