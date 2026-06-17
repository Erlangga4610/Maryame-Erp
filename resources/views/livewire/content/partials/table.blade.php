<div x-show="$wire.viewMode === 'table'" class="overflow-x-auto">
    <flux:table class="text-xs">
        <flux:table.columns>
            <flux:table.column class="!p-2 !text-[11px]">Kode</flux:table.column>
            <flux:table.column class="!p-2 !text-[11px]">Platform</flux:table.column>
            <flux:table.column class="!p-2 !text-[11px]">Group</flux:table.column>
            <flux:table.column class="!p-2 !text-[11px]">Tema</flux:table.column>
            <flux:table.column class="!p-2 !text-[11px]">Publish</flux:table.column>
            <flux:table.column class="!p-2 !text-[11px]">Priority</flux:table.column>
            <flux:table.column class="!p-2 !text-[11px]">Status</flux:table.column>
            <flux:table.column class="!p-2 !text-[11px]">Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($contents as $content)
                <flux:table.row wire:key="content-{{ $content->id }}">
                    <flux:table.cell class="!p-1.5 font-mono text-[11px]">
                        {{ $content->content_code }}
                    </flux:table.cell>

                    <flux:table.cell class="!p-1.5">
                        <flux:badge size="sm" :color="$content->platform->code === 'TKM' ? 'purple' : 'blue'" class="text-[10px]">
                            {{ $content->platform->name }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="!p-1.5 text-[11px]">
                        @if($content->contentGroup)
                            <span class="text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-1.5 py-0.5 rounded">
                                {{ \Illuminate\Support\Str::limit($content->contentGroup->name, 12) }}
                            </span>
                        @else
                            <span class="text-zinc-400">-</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="!p-1.5 max-w-[160px] truncate text-[11px]">
                        {{ $content->theme }}
                    </flux:table.cell>

                    <flux:table.cell class="!p-1.5 whitespace-nowrap text-[11px]">
                        {{ $content->full_publish_date ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="!p-1.5">
                        <flux:badge size="sm" :color="$content->priority_badge_color" class="text-[10px]">
                            {{ ucfirst($content->priority->value) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="!p-1.5">
                        <flux:badge size="sm" :color="$content->status->color()" class="text-[10px]">
                            {{ $content->status->label() }}
                        </flux:badge>
                    </flux:table.cell>

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
                        $isPic = auth()->user()->isSuperAdmin()
                            || auth()->user()->id === $content->pic_copy_id
                            || auth()->user()->id === $content->pic_visual_id
                            || auth()->user()->id === $content->pic_video_id;
                        $qc = $content->tiktokQc;
                    @endphp

                    <flux:table.cell class="!p-1.5">
                        <div class="flex items-center gap-1 flex-nowrap">
                            <button type="button" wire:click="openEditModal({{ $content->id }})" class="text-[11px] text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap">
                                Edit
                            </button>

                            @if($content->status->value === 'draft')
                                <button type="button" wire:click="startProduction({{ $content->id }})" class="text-[11px] text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap">
                                    Produksi
                                </button>
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('CSP') || auth()->user()->hasRole('CW'))
                                    <button type="button" wire:click="confirmDelete({{ $content->id }})" class="text-[11px] text-red-500 hover:underline whitespace-nowrap">
                                        Hapus
                                    </button>
                                @endif
                            @endif

                            @if($content->status->value === 'in_production' && $isPic)
                                <button type="button" wire:click="submitForApproval({{ $content->id }})" class="text-[11px] text-pink-600 dark:text-pink-400 hover:underline whitespace-nowrap">
                                    Submit
                                </button>
                            @endif

                            @if($content->platform->code === 'TKM' && in_array($content->status->value, ['in_production', 'ready_review', 'approved']))
                                @if($qc && $qc->status === 'passed')
                                    <span class="text-[11px] text-green-600">QC✓</span>
                                @else
                                    <button type="button" wire:click="openQcModal({{ $content->id }})" class="text-[11px] text-purple-600 dark:text-purple-400 hover:underline whitespace-nowrap">
                                        {{ $qc && $qc->status === 'need_revision' ? 'QC⚠' : 'QC' }}
                                    </button>
                                @endif
                            @endif

                            @if($content->status->value === 'ready_review')
                                @if($myStage && ($approval = $content->approvals->where('stage', $myStage)->first()) && $approval->status === 'pending')
                                    @php
                                        $prevStage = match($myStage) {
                                            'cw' => null, 'csp' => 'cw', 'sms' => 'csp',
                                            'rnd' => 'sms', 'legal' => $rndApproval ? 'rnd' : 'sms',
                                            default => null,
                                        };
                                        $prevApproved = !$prevStage || $content->approvals->where('stage', $prevStage)->first()?->status === 'approved';
                                    @endphp
                                    @if($prevApproved)
                                        <button type="button" wire:click="confirmApprove({{ $content->id }}, '{{ $myStage }}')" class="text-[11px] text-green-600 dark:text-green-400 hover:underline whitespace-nowrap">
                                            Approve
                                        </button>
                                    @else
                                        <span class="text-[11px] text-amber-600">⏳</span>
                                    @endif
                                @elseif($allDone)
                                    <span class="text-[11px] text-green-600">✓</span>
                                @else
                                    <span class="text-[11px] text-amber-600">⏳</span>
                                @endif
                            @endif

                            @if(in_array($content->status->value, ['ready_review', 'approved', 'scheduled', 'published']))
                                <button type="button" wire:click="openVersionModal({{ $content->id }})" class="text-[11px] text-zinc-500 hover:underline whitespace-nowrap">
                                    v{{ $content->version }}
                                </button>
                                <button type="button" wire:click="openAdjustmentModal({{ $content->id }})" class="text-[11px] text-zinc-500 hover:underline whitespace-nowrap">
                                    Log
                                </button>
                            @endif

                            @if($content->status->value === 'approved')
                                <button type="button" wire:click="openScheduleModal({{ $content->id }})" class="text-[11px] text-purple-600 dark:text-purple-400 hover:underline whitespace-nowrap">
                                    Schedule
                                </button>
                            @endif

                            @if($content->status->value === 'scheduled')
                                <button type="button" wire:click="openPublishModal({{ $content->id }})" class="text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline whitespace-nowrap">
                                    Publish
                                </button>
                            @endif

                            @if($content->status->value === 'published')
                                <button type="button" wire:click="openChecklistModal({{ $content->id }})" class="text-[11px] text-zinc-500 hover:underline whitespace-nowrap">
                                    Checklist
                                </button>
                                @if($content->brief?->copy_brief ?? $content->copy_brief || $content->brief?->visual_brief ?? $content->visual_brief || $content->brief?->video_brief ?? $content->video_brief)
                                    <button type="button" wire:click="openBriefModal({{ $content->id }})" class="text-[11px] text-pink-600 dark:text-pink-400 hover:underline whitespace-nowrap">
                                        Brief
                                    </button>
                                @endif
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="!p-4 text-center text-zinc-500">
                        Tidak ada data konten
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-3">
        {{ $contents->links() }}
    </div>
</div>
