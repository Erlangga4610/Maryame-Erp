<div x-show="$wire.viewMode === 'table'" class="overflow-x-auto">
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Kode</flux:table.column>
            <flux:table.column>Platform</flux:table.column>
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
                        $mcApproval = $content->approvals->where('stage', 'mc_bm')->first();
                        $legalApproval = $content->approvals->where('stage', 'legal')->first();
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
                                <flux:button size="sm" variant="outline" wire:click="submitForApproval({{ $content->id }})">
                                    Submit
                                </flux:button>

                                <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $content->id }})">
                                    Hapus
                                </flux:button>
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

                            @if($content->status->value === 'ready_review')
                                @if($userRole === 'MC_BM' && $mcApproval && $mcApproval->status === 'pending')
                                    <flux:button size="sm" variant="primary" color="green" wire:click="confirmApprove({{ $content->id }}, 'mc_bm')">
                                        Approve
                                    </flux:button>
                                @elseif($userRole === 'Legal' && $legalApproval && $legalApproval->status === 'pending' && $mcApproval?->status === 'approved')
                                    <flux:button size="sm" variant="primary" color="green" wire:click="confirmApprove({{ $content->id }}, 'legal')">
                                        Approve
                                    </flux:button>
                                @else
                                    <flux:badge size="sm" :color="$mcApproval?->status === 'approved' && $legalApproval?->status === 'approved' ? 'green' : 'amber'">
                                        {{ $mcApproval?->status === 'approved' && $legalApproval?->status === 'approved' ? 'Approved' : 'Pending' }}
                                    </flux:badge>
                                @endif
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center py-8 text-gray-500">
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
