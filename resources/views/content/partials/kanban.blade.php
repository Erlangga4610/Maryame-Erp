<div wire:key="kanban-board" x-show="$wire.viewMode === 'kanban'" class="pb-2"
     x-on:kanban-move.window="$wire.moveToColumn($event.detail.id, $event.detail.column)"
>
    <div class="grid grid-cols-4 gap-3">
        @foreach($kanbanColumns as $key => $column)
            <div class="flex flex-col rounded-xl bg-zinc-100 dark:bg-zinc-800/50">
                <div class="flex items-center justify-between px-3 pt-3 pb-2">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ $column['label'] }}</h3>
                        <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $column['items']->count() }}</span>
                    </div>
                </div>

                <div
                    data-column="{{ $key }}"
                    class="flex flex-col gap-1.5 px-2 pb-2 min-h-[180px]"
                >
                    @forelse($column['items'] as $content)
                        <div
                            data-content-id="{{ $content->id }}"
                            class="rounded-lg bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 p-2.5 shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow text-[12px]"
                            wire:key="kanban-{{ $content->id }}"
                        >
                            <div class="flex items-start justify-between gap-1.5 mb-1.5">
                                <span class="font-mono font-medium text-zinc-500 dark:text-zinc-400">{{ $content->content_code }}</span>
                                <flux:badge size="sm" :color="$content->priority_badge_color">
                                    {{ ucfirst($content->priority->value) }}
                                </flux:badge>
                            </div>

                            <p class="font-medium text-zinc-800 dark:text-white mb-1.5 line-clamp-2">{{ $content->theme }}</p>

                            <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400">
                                <span>{{ $content->platform->code }}</span>
                                <span class="truncate">{{ $content->picCopy?->name ?? '-' }}</span>
                            </div>

                            @php $hasBrief = $content->copy_brief || $content->visual_brief || $content->video_brief; @endphp
                            @if($hasBrief)
                                <div class="flex items-center gap-1 mt-1">
                                    <button type="button" wire:click="openBriefModal({{ $content->id }})" class="text-[10px] text-pink-600 dark:text-pink-400 hover:underline">
                                        @if($content->copy_brief)<span class="bg-pink-100 dark:bg-pink-900/30 px-1.5 py-0.5 rounded">Copy</span>@endif
                                        @if($content->visual_brief)<span class="bg-pink-100 dark:bg-pink-900/30 px-1.5 py-0.5 rounded">Visual</span>@endif
                                        @if($content->video_brief)<span class="bg-pink-100 dark:bg-pink-900/30 px-1.5 py-0.5 rounded">Video</span>@endif
                                        <span class="ml-0.5 hover:underline">Lihat</span>
                                    </button>
                                </div>
                            @endif

                            @if($content->publish_date)
                                <div class="mt-1.5 text-zinc-500 dark:text-zinc-400">
                                    {{ $content->publish_date->format('d M') }}
                                </div>
                            @endif

                            <div class="mt-1.5 flex items-center gap-2 text-[10px] text-zinc-400">
                                @if($content->thumbnail_link)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($content->thumbnail_link) }}" class="size-6 rounded object-cover border border-zinc-200 dark:border-zinc-600" alt="thumb" />
                                @endif
                                @if($content->final_asset_link)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($content->final_asset_link) }}" target="_blank" class="hover:text-pink-600 dark:hover:text-pink-400" title="Download asset">
                                        <flux:icon.paper-clip class="size-3" />
                                    </a>
                                @endif
                                <button type="button" wire:click="openVersionModal({{ $content->id }})" class="hover:text-pink-600 dark:hover:text-pink-400 hover:underline">v{{ $content->version }}</button>
                                <button type="button" wire:click="openAdjustmentModal({{ $content->id }})" class="hover:text-pink-600 dark:hover:text-pink-400 hover:underline">Log</button>
                            </div>

                            @php
                                $mcApproval = $content->approvals->where('stage', 'mc_bm')->first();
                                $legalApproval = $content->approvals->where('stage', 'legal')->first();
                            @endphp

                            @if($content->status->value === 'ready_review')
                                <div class="mt-1.5 flex flex-wrap gap-1">
                                    @if($userRole === 'MC_BM' && $mcApproval && $mcApproval->status === 'pending')
                                        <button type="button" wire:click="confirmApprove({{ $content->id }}, 'mc_bm')" class="text-xs text-white bg-green-600 hover:bg-green-700 px-2 py-0.5 rounded">Approve</button>
                                        <button type="button" wire:click="confirmApprove({{ $content->id }}, 'mc_bm')" class="text-xs text-amber-600 border border-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 px-2 py-0.5 rounded">Revisi</button>
                                    @elseif($userRole === 'MC_BM' && $mcApproval && $mcApproval->status === 'approved')
                                        <span class="text-xs text-green-600 dark:text-green-400">MC/BM ✓</span>
                                    @elseif($userRole === 'Legal' && $legalApproval && $legalApproval->status === 'pending' && $mcApproval && $mcApproval->status === 'approved')
                                        <button type="button" wire:click="confirmApprove({{ $content->id }}, 'legal')" class="text-xs text-white bg-green-600 hover:bg-green-700 px-2 py-0.5 rounded">Approve</button>
                                        <button type="button" wire:click="confirmApprove({{ $content->id }}, 'legal')" class="text-xs text-amber-600 border border-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 px-2 py-0.5 rounded">Revisi</button>
                                    @elseif($userRole === 'Legal' && $legalApproval && $legalApproval->status === 'approved')
                                        <span class="text-xs text-green-600 dark:text-green-400">Legal ✓</span>
                                    @elseif($userRole === 'CSP' && $mcApproval?->status === 'approved' && $legalApproval?->status === 'approved')
                                        <flux:badge size="sm" color="green">Approved</flux:badge>
                                    @elseif($userRole === 'CSP')
                                        <span class="text-xs text-amber-600 dark:text-amber-400">⏳ Pending Approval</span>
                                    @endif
                                </div>
                            @endif

                            @if($content->platform->code === 'TKM' && in_array($content->status->value, ['in_production', 'ready_review', 'approved']))
                                <div class="mt-1.5">
                                    @php $qc = $content->tiktokQc; @endphp
                                    @if($qc && $qc->status === 'passed')
                                        <span class="text-xs text-green-600 dark:text-green-400">QC: ✓ Lulus</span>
                                    @elseif($qc && $qc->status === 'need_revision')
                                        <button type="button" wire:click="openQcModal({{ $content->id }})" class="text-xs text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 hover:underline transition-colors">
                                            QC: ⚠ Revisi (klik)
                                        </button>
                                    @else
                                        <button type="button" wire:click="openQcModal({{ $content->id }})" class="text-xs text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 hover:underline transition-colors">
                                            QC TikTok
                                        </button>
                                    @endif
                                </div>
                            @endif

                            @if($canCreate && $content->status->value === 'draft')
                                <div class="mt-1.5 flex items-center gap-2">
                                    <button type="button" wire:click="submitForApproval({{ $content->id }})" class="text-xs text-pink-600 hover:text-pink-700 dark:text-pink-400 dark:hover:text-pink-300 hover:underline transition-colors">
                                        Submit Approval
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $content->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:underline transition-colors">
                                        Hapus
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex-1 flex items-center justify-center">
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Tidak ada konten</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
