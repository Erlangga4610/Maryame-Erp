<div wire:key="kanban-board" x-show="$wire.viewMode === 'kanban'"
     x-data="{
         dragId: null,
         dragEl: null,
         sbDragging: false,
         sbStartX: 0,
         sbScrollLeft: 0,
         shouldSkip(el) {
             return el.closest('button, a, input, select, textarea, [draggable]');
         },
         grabStart(e) {
             if (e.button !== 0 || this.shouldSkip(e.target)) return;
             this.sbDragging = true;
             this.sbStartX = e.pageX - this.$el.getBoundingClientRect().left;
             this.sbScrollLeft = this.$el.scrollLeft;
             this.$el.classList.remove('cursor-grab');
             this.$el.classList.add('cursor-grabbing', 'select-none');
         },
         grabMove(e) {
             if (!this.sbDragging) return;
             e.preventDefault();
             const x = e.pageX - this.$el.getBoundingClientRect().left;
             this.$el.scrollLeft = this.sbScrollLeft - (x - this.sbStartX);
         },
         grabEnd() {
             if (!this.sbDragging) return;
             this.sbDragging = false;
             this.$el.classList.remove('cursor-grabbing', 'select-none');
             this.$el.classList.add('cursor-grab');
         },
         touchStart(e) {
             this.sbStartX = e.touches[0].pageX - this.$el.getBoundingClientRect().left;
             this.sbScrollLeft = this.$el.scrollLeft;
         },
         touchMove(e) {
             if (e.touches.length !== 1) return;
             const x = e.touches[0].pageX - this.$el.getBoundingClientRect().left;
             this.$el.scrollLeft = this.sbScrollLeft - (x - this.sbStartX);
         }
     }"
     x-on:kanban-move.window="$wire.moveToColumn($event.detail.id, $event.detail.column)"
     x-on:mousedown="grabStart"
     x-on:mousemove="grabMove"
     x-on:mouseup="grabEnd"
     x-on:mouseleave="grabEnd"
     x-on:touchstart="touchStart"
     x-on:touchmove="touchMove"
     x-on:touchend="sbDragging = false"
     class="pb-0 overflow-x-auto cursor-grab scrollbar-thin relative"
>
    <div class="flex gap-3 min-w-[900px]">
        @php
            $columnColors = [
                'todo' => ['border' => 'border-l-zinc-400', 'badge' => 'bg-zinc-400'],
                'in_progress' => ['border' => 'border-l-blue-400', 'badge' => 'bg-blue-400'],
                'in_review' => ['border' => 'border-l-amber-400', 'badge' => 'bg-amber-400'],
                'done' => ['border' => 'border-l-emerald-400', 'badge' => 'bg-emerald-400'],
            ];
        @endphp
        @foreach($kanbanColumns as $key => $column)
            @php $cc = $columnColors[$key] ?? $columnColors['todo']; @endphp
            <div wire:key="kanban-col-{{ $key }}" class="flex flex-col flex-1 min-w-[210px] rounded-xl bg-zinc-100/80 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-700 {{ $cc['border'] }} border-l-4">
                {{-- Header --}}
                <div class="flex items-center justify-between px-3 pt-3 pb-2 sticky top-0 bg-zinc-100/80 dark:bg-zinc-800/40 rounded-tr-xl z-10">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full {{ $cc['badge'] }}"></span>
                        <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ $column['label'] }}</h3>
                        <span class="text-xs font-mono text-zinc-400 dark:text-zinc-500">{{ $column['items']->count() }}</span>
                    </div>
                </div>

                {{-- Card list / drop zone --}}
                <div
                    data-column="{{ $key }}"
                    class="flex flex-col gap-1.5 px-2 pb-2 flex-1 min-h-[220px] transition-all duration-150 rounded-b-xl"
                    x-on:dragenter.prevent="
                        if (! $el._dragCounter) $el._dragCounter = 0;
                        $el._dragCounter++;
                        $el.classList.add('bg-pink-50/50', 'dark:bg-pink-900/15');
                        $el.classList.add('border-2', 'border-dashed', 'border-pink-300', 'dark:border-pink-700');
                    "
                    x-on:dragover.prevent="
                        $el.classList.add('bg-pink-50/50', 'dark:bg-pink-900/15');
                        $el.classList.add('border-2', 'border-dashed', 'border-pink-300', 'dark:border-pink-700');
                    "
                    x-on:dragleave="
                        if (! $el._dragCounter) $el._dragCounter = 0;
                        $el._dragCounter--;
                        if ($el._dragCounter <= 0) {
                            $el._dragCounter = 0;
                            $el.classList.remove('bg-pink-50/50', 'dark:bg-pink-900/15');
                            $el.classList.remove('border-2', 'border-dashed', 'border-pink-300', 'dark:border-pink-700');
                        }
                    "
                    x-on:drop.prevent="
                        $el._dragCounter = 0;
                        $el.classList.remove('bg-pink-50/50', 'dark:bg-pink-900/15');
                        $el.classList.remove('border-2', 'border-dashed', 'border-pink-300', 'dark:border-pink-700');
                        if (dragId) $wire.moveToColumn(dragId, '{{ $key }}');
                        dragId = null;
                        if (dragEl) { dragEl.style.opacity = '1'; dragEl = null; }
                    "
                >
                    @forelse($column['items'] as $content)
                        <div
                            data-content-id="{{ $content->id }}"
                            class="rounded-lg bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 p-2.5 shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow text-[12px]"
                            wire:key="kanban-{{ $content->id }}"
                            draggable="true"
                            x-on:dragstart="
                                dragId = {{ $content->id }};
                                dragEl = $el;
                                $el.style.opacity = '0.4';
                            "
                            x-on:dragend="
                                dragId = null;
                                $el.style.opacity = '1';
                                dragEl = null;
                            "
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
                                @if($content->contentGroup)
                                    <span class="text-[10px] text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-1.5 py-0.5 rounded ml-auto" title="Group: {{ $content->contentGroup->name }}">
                                        {{ \Illuminate\Support\Str::limit($content->contentGroup->name, 12) }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1 mt-1">
                                <button type="button" wire:click="openBriefModal({{ $content->id }})" class="text-[10px] text-pink-600 dark:text-pink-400 hover:underline flex items-center gap-1">
                                    @if($content->brief?->is_final ?? $content->is_brief_final)
                                        <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-1.5 py-0.5 rounded">Brief ✓</span>
                                    @else
                                        <span class="bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-1.5 py-0.5 rounded">Brief</span>
                                    @endif
                                    <span>Lihat</span>
                                </button>
                                @if($isCsp && !($content->brief?->is_final ?? $content->is_brief_final) && $content->status->value === 'draft')
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
                                    @php
                                        $faExt = strtolower(pathinfo($content->final_asset_link, PATHINFO_EXTENSION));
                                        $faUrl = \Illuminate\Support\Facades\Storage::url($content->final_asset_link);
                                    @endphp
                                    @if(in_array($faExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <a href="{{ $faUrl }}" target="_blank" title="Lihat asset">
                                            <img src="{{ $faUrl }}" class="size-6 rounded object-cover border border-zinc-200 dark:border-zinc-600 hover:opacity-80 transition-opacity" alt="asset" />
                                        </a>
                                    @else
                                        <a href="{{ $faUrl }}" target="_blank" class="hover:text-pink-600 dark:hover:text-pink-400" title="Download asset">
                                            <flux:icon.video-camera class="size-3" />
                                        </a>
                                    @endif
                                @endif
                                <button type="button" wire:click="openVersionModal({{ $content->id }})" class="hover:text-pink-600 dark:hover:text-pink-400 hover:underline">v{{ $content->version }}</button>
                                <button type="button" wire:click="openAdjustmentModal({{ $content->id }})" class="hover:text-pink-600 dark:hover:text-pink-400 hover:underline">Log</button>
                            </div>

                            @php
                                $cwApproval = $content->approvals->where('stage', 'cw')->first();
                                $cspApproval = $content->approvals->where('stage', 'csp')->first();
                                $smsApproval = $content->approvals->where('stage', 'sms')->first();
                                $rndApproval = $content->approvals->where('stage', 'rnd')->first();
                                $legalApproval = $content->approvals->where('stage', 'legal')->first();
                                $allDone = $cwApproval?->status === 'approved'
                                    && $cspApproval?->status === 'approved'
                                    && $smsApproval?->status === 'approved'
                                    && (!$rndApproval || $rndApproval->status === 'approved')
                                    && (!$legalApproval || $legalApproval->status === 'approved');
                            @endphp

                            @if($content->status->value === 'ready_review')
                                <div class="mt-1.5 flex flex-wrap gap-1">
                                    @php
                                        $myStage = match($userRole) {
                                            'CW' => 'cw',
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
                                                'cw' => null,
                                                'csp' => 'cw',
                                                'sms' => 'csp',
                                                'rnd' => 'sms',
                                                'legal' => $rndApproval ? 'rnd' : 'sms',
                                                default => null,
                                            };
                                            $prevApproved = !$prevStage || $content->approvals->where('stage', $prevStage)->first()?->status === 'approved';
                                        @endphp
                                        @if($prevApproved)
                                            <button type="button" wire:click="confirmApprove({{ $content->id }}, '{{ $myStage }}')" class="text-xs text-white bg-green-600 hover:bg-green-700 px-2 py-0.5 rounded">Approve</button>
                                            <button type="button" wire:click="directRevise({{ $content->id }}, '{{ $myStage }}')" class="text-xs text-amber-600 border border-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 px-2 py-0.5 rounded">Revisi</button>
                                        @else
                                            <span class="text-xs text-amber-600 dark:text-amber-400">⏳ Menunggu approval sebelumnya</span>
                                        @endif
                                    @elseif($myStage && ($approval = $content->approvals->where('stage', $myStage)->first()) && $approval->status === 'approved')
                                        <span class="text-xs text-green-600 dark:text-green-400">{{ match($myStage) { 'cw' => 'CW', 'csp' => 'CSP', 'sms' => 'SMS', 'rnd' => 'RnD', 'legal' => 'Legal', default => ucfirst($myStage) } }} ✓</span>
                                    @elseif($allDone)
                                        <flux:badge size="sm" color="green">Approved</flux:badge>
                                    @elseif(!$myStage)
                                        @php
                                            $stageLabels = [];
                                            foreach (['cw', 'csp', 'sms', 'rnd', 'legal'] as $s) {
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

                            @if($content->status->value === 'approved')
                                <div class="mt-1.5">
                                    <flux:button size="xs" variant="primary" color="purple" wire:click="openScheduleModal({{ $content->id }})">
                                        Schedule
                                    </flux:button>
                                </div>
                            @endif

                            @if($content->status->value === 'scheduled')
                                <div class="mt-1.5 flex items-center gap-1">
                                    <flux:button size="xs" variant="primary" color="emerald" wire:click="openPublishModal({{ $content->id }})">
                                        Mark Published
                                    </flux:button>
                                </div>
                            @endif

                            @if(in_array($content->status->value, ['published']))
                                <div class="mt-1.5 flex items-center gap-1">
                                    <flux:button size="xs" variant="ghost" wire:click="openChecklistModal({{ $content->id }})" class="text-xs text-zinc-500">
                                        Post-Publish Checklist
                                    </flux:button>
                                    @if($content->live_url)
                                        <a href="{{ $content->live_url }}" target="_blank" class="text-xs text-blue-500 hover:underline" title="Buka link live">
                                            🔗
                                        </a>
                                    @endif
                                </div>
                            @endif

                            @if($content->status->value === 'draft')
                                <div class="mt-1.5 flex items-center gap-2">
                                    <button type="button" wire:click="startProduction({{ $content->id }})" class="text-xs text-blue-600 dark:text-blue-400 hover:underline transition-colors whitespace-nowrap">
                                        Mulai Produksi
                                    </button>
                                    @if($canCreate)
                                        <button type="button" wire:click="confirmDelete({{ $content->id }})" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:underline transition-colors">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            @elseif($content->status->value === 'in_production')
                                @php
                                    $isPic = auth()->user()->isSuperAdmin()
                                        || auth()->user()->id === $content->pic_copy_id
                                        || auth()->user()->id === $content->pic_visual_id
                                        || auth()->user()->id === $content->pic_video_id;
                                @endphp
                                @if($isPic)
                                    <div class="mt-1.5">
                                        <button type="button" wire:click="submitForApproval({{ $content->id }})" class="text-xs text-pink-600 hover:text-pink-700 dark:text-pink-400 dark:hover:text-pink-300 hover:underline transition-colors">
                                            Submit for Review
                                        </button>
                                    </div>
                                @endif
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

    {{-- DRAG ZONE — scroll grab area below the board --}}
    <div class="min-w-[900px] h-28 rounded-lg mt-3 flex items-center justify-center cursor-grab hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
        <div class="flex items-center gap-2 text-zinc-400 dark:text-zinc-500 pointer-events-none">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
            </svg>
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-xs font-mono tracking-wider uppercase">Klik &amp; Geser untuk scroll</span>
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
            </svg>
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </div>
    </div>
</div>
