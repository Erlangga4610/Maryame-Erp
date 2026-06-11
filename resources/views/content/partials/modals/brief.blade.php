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
                        @if($briefContent->is_brief_final)
                            <flux:badge size="sm" color="green">Brief Final</flux:badge>
                        @else
                            <flux:badge size="sm" color="amber">Brief Draft</flux:badge>
                        @endif
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

            <div class="space-y-5">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="size-2 rounded-full bg-pink-500"></div>
                        <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Strategic Brief</h4>
                        <span class="text-[11px] text-zinc-400 dark:text-zinc-500 italic">(CSP)</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        @if($briefContent->angle)
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Angle</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->angle }}</p>
                            </div>
                        @endif
                        @if($briefContent->positioning)
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Positioning</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->positioning }}</p>
                            </div>
                        @endif
                        @if($briefContent->target_audience)
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Target Audience</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->target_audience }}</p>
                            </div>
                        @endif
                        @if($briefContent->tone)
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Tone</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->tone }}</p>
                            </div>
                        @endif
                        @if($briefContent->key_message)
                            <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Key Message</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300 whitespace-pre-line">{{ $briefContent->key_message }}</p>
                            </div>
                        @endif
                        @if($briefContent->copy_brief)
                            <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Copy Direction</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300 whitespace-pre-line">{{ $briefContent->copy_brief }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="size-2 rounded-full bg-pink-500"></div>
                        <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Technical Brief</h4>
                        <span class="text-[11px] text-zinc-400 dark:text-zinc-500 italic">(SMS)</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        @if($briefContent->aspect_ratio)
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Aspect Ratio</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->aspect_ratio }}</p>
                            </div>
                        @endif
                        @if($briefContent->resolution)
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Resolution</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->resolution }}</p>
                            </div>
                        @endif
                        @if($briefContent->duration)
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Durasi</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->duration }}</p>
                            </div>
                        @endif
                        @if($briefContent->format_file)
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Format File</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->format_file }}</p>
                            </div>
                        @endif
                        @if($briefContent->hashtag)
                            <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Hashtag</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $briefContent->hashtag }}</p>
                            </div>
                        @endif
                        @if($briefContent->audio_guidance)
                            <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Audio Guidance</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300 whitespace-pre-line">{{ $briefContent->audio_guidance }}</p>
                            </div>
                        @endif
                        @if($briefContent->originality_instruction)
                            <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Originality Instruction</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300 whitespace-pre-line">{{ $briefContent->originality_instruction }}</p>
                            </div>
                        @endif
                        @if($briefContent->thumbnail_note)
                            <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Thumbnail Note</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300 whitespace-pre-line">{{ $briefContent->thumbnail_note }}</p>
                            </div>
                        @endif
                        @if($briefContent->visual_brief)
                            <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Visual Direction</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300 whitespace-pre-line">{{ $briefContent->visual_brief }}</p>
                            </div>
                        @endif
                        @if($briefContent->video_brief)
                            <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wide">Video Direction</span>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300 whitespace-pre-line">{{ $briefContent->video_brief }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @unless($briefContent->copy_brief || $briefContent->visual_brief || $briefContent->video_brief || $briefContent->angle || $briefContent->aspect_ratio)
                <div class="flex flex-col items-center justify-center py-12 text-zinc-300 dark:text-zinc-600">
                    <flux:icon.document-text class="size-16 mb-4 stroke-1" />
                    <p class="text-base font-medium text-zinc-400 dark:text-zinc-500">Brief belum diisi</p>
                    <p class="text-sm mt-1">Buka edit konten untuk menambahkan brief pada tab Strategic atau Technical.</p>
                </div>
            @endunless
        </div>
    </flux:modal>
@endif
