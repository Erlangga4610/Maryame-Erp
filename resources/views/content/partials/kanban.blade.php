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

                            <div class="flex items-center gap-1 mt-1">
                                <button type="button" wire:click="openBriefModal({{ $content->id }})" class="text-[10px] text-pink-600 dark:text-pink-400 hover:underline flex items-center gap-1">
                                    @if($content->is_brief_final)
                                        <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-1.5 py-0.5 rounded">Brief ✓</span>
                                    @else
                                        <span class="bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-1.5 py-0.5 rounded">Brief</span>
                                    @endif
                                    <span>Lihat</span>
                                </button>
                                @if($isCsp && !$content->is_brief_final && $content->status->value === 'draft')
                                    <button type="button" wire:click="finalizeBrief({{ $content->id }})" class="text-[10px] text-green-600 dark:text-green-400 hover:underline">
                                        Finalkan
                                    </button>
                                @endif
                            </div>

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
                                $cspApproval = $content->approvals->where('stage', 'csp')->first();
                                $smsApproval = $content->approvals->where('stage', 'sms')->first();
                                $rndApproval = $content->approvals->where('stage', 'rnd')->first();
                                $legalApproval = $content->approvals->where('stage', 'legal')->first();
                                $allDone = $cspApproval?->status === 'approved'
                                    && $smsApproval?->status === 'approved'
                                    && (!$rndApproval || $rndApproval->status === 'approved')
                                    && (!$legalApproval || $legalApproval->status === 'approved');
                            @endphp

                            @if($content->status->value === 'ready_review')
                                <div class="mt-1.5 flex flex-wrap gap-1">
                                    @php
                                        $myStage = match($userRole) {
                                            'CSP' => 'csp',
                                            'SMS' => 'sms',
                                            'RnD' => 'rnd',
                                            'Legal' => 'legal',
                                            default => null,
                                        };
                                        if ($userRole === 'Super Admin') {
                                            $pendingStage = $content->approvals->where('status', 'pending')->first();
                                            $myStage = $pendingStage?->stage;
                                        }
                                    @endphp

                                    @if($myStage && ($approval = $content->approvals->where('stage', $myStage)->first()) && $approval->status === 'pending')
                                        @php
                                            $prevStage = match($myStage) {
                                                'csp' => null,
                                                'sms' => 'csp',
                                                'rnd' => 'sms',
                                                'legal' => $rndApproval ? 'rnd' : 'sms',
                                                default => null,
                                            };
                                            $prevApproved = !$prevStage || $content->approvals->where('stage', $prevStage)->first()?->status === 'approved';
                                        @endphp
                                        @if($prevApproved)
                                            <button type="button" wire:click="confirmApprove({{ $content->id }}, '{{ $myStage }}')" class="text-xs text-white bg-green-600 hover:bg-green-700 px-2 py-0.5 rounded">Approve</button>
                                            <button type="button" wire:click="confirmApprove({{ $content->id }}, '{{ $myStage }}')" class="text-xs text-amber-600 border border-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 px-2 py-0.5 rounded">Revisi</button>
                                        @else
                                            <span class="text-xs text-amber-600 dark:text-amber-400">⏳ Menunggu approval sebelumnya</span>
                                        @endif
                                    @elseif($myStage && ($approval = $content->approvals->where('stage', $myStage)->first()) && $approval->status === 'approved')
                                        <span class="text-xs text-green-600 dark:text-green-400">{{ $myStage === 'rnd' ? 'RnD' : ucfirst($myStage) }} ✓</span>
                                    @elseif($allDone)
                                        <flux:badge size="sm" color="green">Approved</flux:badge>
                                    @elseif(!$myStage || $myStage === 'CW')
                                        @php
                                            $stageLabels = [];
                                            foreach (['csp', 'sms', 'rnd', 'legal'] as $s) {
                                                $a = $content->approvals->where('stage', $s)->first();
                                                if ($a) {
                                                    $stageLabels[] = strtoupper($s) . ': ' . ucfirst($a->status);
                                                }
                                            }
                                        @endphp
                                        <span class="text-xs text-amber-600 dark:text-amber-400">⏳ {{ implode(' | ', $stageLabels) }}</span>
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

                            @if($content->status->value === 'draft')
                                <div class="mt-1.5 flex items-center gap-2">
                                    @if($isCw)
                                        <button type="button" wire:click="submitForApproval({{ $content->id }})" class="text-xs text-pink-600 hover:text-pink-700 dark:text-pink-400 dark:hover:text-pink-300 hover:underline transition-colors">
                                            Submit Approval
                                        </button>
                                    @endif
                                    @if($canCreate)
                                        <button type="button" wire:click="confirmDelete({{ $content->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:underline transition-colors">
                                            Hapus
                                        </button>
                                    @endif
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
