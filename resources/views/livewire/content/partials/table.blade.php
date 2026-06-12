<div x-show="$wire.viewMode === 'table'" class="overflow-x-auto">
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Kode</flux:table.column>
            <flux:table.column>Platform</flux:table.column>
            <flux:table.column>Group</flux:table.column>
            <flux:table.column>Tema</flux:table.column>
            <flux:table.column>Publish Date</flux:table.column>
            <flux:table.column>Priority</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($contents as $content)
                <flux:table.row wire:key="content-{{ $content->id }}">
                    <flux:table.cell class="font-mono text-xs">
                        {{ $content->content_code }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge :color="$content->platform->code === 'TKM' ? 'purple' : 'blue'">
                            {{ $content->platform->name }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($content->contentGroup)
                            <span class="text-xs text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-2 py-0.5 rounded">
                                {{ \Illuminate\Support\Str::limit($content->contentGroup->name, 15) }}
                            </span>
                        @else
                            <span class="text-xs text-zinc-400">-</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="max-w-xs truncate">
                        {{ $content->theme }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $content->full_publish_date ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge :color="$content->priority_badge_color">
                            {{ ucfirst($content->priority->value) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge :color="$content->status->color()">
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
                    @endphp

                    <flux:table.cell>
                        <div class="flex gap-2 flex-wrap">
                            @if($content->copy_brief || $content->visual_brief || $content->video_brief)
                                <flux:button size="sm" variant="outline" wire:click="openBriefModal({{ $content->id }})">
                                    Brief
                                </flux:button>
                            @endif

                            <flux:button size="sm" variant="outline" wire:click="openEditModal({{ $content->id }})">
                                Edit
                            </flux:button>

                            <flux:button size="sm" variant="outline" wire:click="openVersionModal({{ $content->id }})">
                                v{{ $content->version }}
                            </flux:button>

                            <flux:button size="sm" variant="outline" wire:click="openAdjustmentModal({{ $content->id }})">
                                Log
                            </flux:button>

                            @if($content->status->value === 'draft')
                                <flux:button size="sm" variant="primary" color="blue" wire:click="startProduction({{ $content->id }})">
                                    Mulai Produksi
                                </flux:button>
                                <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $content->id }})">
                                    Hapus
                                </flux:button>
                            @endif

                            @if($content->status->value === 'in_production')
                                @php
                                    $isPic = auth()->user()->isSuperAdmin()
                                        || auth()->user()->id === $content->pic_copy_id
                                        || auth()->user()->id === $content->pic_visual_id
                                        || auth()->user()->id === $content->pic_video_id;
                                @endphp
                                @if($isPic)
                                    <flux:button size="sm" variant="primary" color="pink" wire:click="submitForApproval({{ $content->id }})">
                                        Submit for Review
                                    </flux:button>
                                @endif
                            @endif

                            @if($content->platform->code === 'TKM' && in_array($content->status->value, ['in_production', 'ready_review', 'approved']))
                                @php $qc = $content->tiktokQc; @endphp
                                @if($qc && $qc->status === 'passed')
                                    <flux:badge size="sm" color="green">QC: Lulus</flux:badge>
                                @elseif($qc && $qc->status === 'need_revision')
                                    <flux:button size="sm" variant="outline" wire:click="openQcModal({{ $content->id }})">
                                        QC: Revisi
                                    </flux:button>
                                @else
                                    <flux:button size="sm" variant="outline" wire:click="openQcModal({{ $content->id }})">
                                        QC
                                    </flux:button>
                                @endif
                            @endif

                            @if($content->status->value === 'approved')
                                <flux:button size="sm" variant="primary" color="purple" wire:click="openScheduleModal({{ $content->id }})">
                                    Schedule
                                </flux:button>
                            @endif

                            @if($content->status->value === 'scheduled')
                                <flux:button size="sm" variant="primary" color="emerald" wire:click="openPublishModal({{ $content->id }})">
                                    Mark Published
                                </flux:button>
                            @endif

                            @if($content->status->value === 'published')
                                <flux:button size="sm" variant="outline" wire:click="openChecklistModal({{ $content->id }})">
                                    Checklist
                                </flux:button>
                                @if($content->live_url)
                                    <a href="{{ $content->live_url }}" target="_blank" class="text-xs text-blue-500 hover:underline">🔗</a>
                                @endif
                            @endif

                            @if($content->status->value === 'ready_review')
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
                                        <flux:button size="sm" variant="primary" color="green" wire:click="confirmApprove({{ $content->id }}, '{{ $myStage }}')">
                                            Approve
                                        </flux:button>
                                    @else
                                        <flux:badge size="sm" color="amber">Menunggu</flux:badge>
                                    @endif
                                @elseif($allDone)
                                    <flux:badge size="sm" color="green">Approved</flux:badge>
                                @else
                                    <flux:badge size="sm" color="amber">Pending</flux:badge>
                                @endif
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-8 text-gray-500">
                        Tidak ada data konten
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $contents->links() }}
    </div>
</div>
