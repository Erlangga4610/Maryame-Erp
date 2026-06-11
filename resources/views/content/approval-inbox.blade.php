<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-zinc-800 dark:text-white">Approval Inbox</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                @if($userRole === 'MC_BM')
                    Konten yang menunggu approval MC/BM
                @elseif($userRole === 'Legal')
                    Konten yang menunggu approval Legal
                @else
                    Semua konten yang menunggu approval
                @endif
            </p>
        </div>

        <flux:select wire:model.live="filterStage" class="w-44">
            <option value="">Semua Stage</option>
            <option value="mc_bm">MC/BM</option>
            <option value="legal">Legal</option>
        </flux:select>
    </div>

    @if($contents->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-zinc-400 dark:text-zinc-500">
            <flux:icon.inbox class="size-16 mb-4 stroke-1" />
            <p class="text-lg font-medium">Tidak ada konten yang perlu di-approve</p>
            <p class="text-sm mt-1">Semua konten sudah diproses.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($contents as $content)
                @php
                    $mc = $content->approvals->where('stage', 'mc_bm')->first();
                    $legal = $content->approvals->where('stage', 'legal')->first();
                @endphp

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $content->content_code }}</span>
                                <flux:badge size="sm" :color="$content->platform->code === 'TKM' ? 'purple' : 'blue'">
                                    {{ $content->platform->name }}
                                </flux:badge>
                                <flux:badge size="sm" :color="$content->priority_badge_color">
                                    {{ ucfirst($content->priority->value) }}
                                </flux:badge>
                            </div>

                            <h4 class="font-medium text-zinc-800 dark:text-white truncate">{{ $content->theme }}</h4>

                            <div class="flex items-center gap-4 mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                <span>PIC: {{ $content->picCopy?->name ?? '-' }}</span>
                                @if($content->publish_date)
                                    <span>Target: {{ $content->publish_date->format('d M Y') }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 mt-3">
                                @if($mc)
                                    <flux:badge size="sm" :color="$mc->status === 'approved' ? 'green' : ($mc->status === 'revision' ? 'red' : 'amber')">
                                        MC/BM: {{ ucfirst($mc->status) }}
                                    </flux:badge>
                                @endif
                                @if($legal)
                                    <flux:badge size="sm" :color="$legal->status === 'approved' ? 'green' : ($legal->status === 'revision' ? 'red' : 'amber')">
                                        Legal: {{ ucfirst($legal->status) }}
                                    </flux:badge>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-2 shrink-0">
                            <flux:button size="sm" variant="outline" href="/contents">
                                Detail
                            </flux:button>

                            @if(($userRole === 'MC_BM' && $mc && $mc->status === 'pending') || ($userRole === 'Legal' && $legal && $legal->status === 'pending' && $mc?->status === 'approved'))
                                <flux:button size="sm" variant="primary" color="green" wire:click="confirmApprove({{ $content->id }}, '{{ $userRole === 'MC_BM' ? 'mc_bm' : 'legal' }}')">
                                    Approve
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Approve Modal --}}
    <flux:modal name="approve-inbox-modal" wire:model="showApproveModal" class="w-md" wire:key="approve-inbox-modal">
        <div class="p-6 space-y-4">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">
                {{ $approveStage === 'legal' ? 'Approval Legal' : 'Approval MC/BM' }}
            </h3>

            <div>
                <flux:field>
                    <flux:label>Catatan</flux:label>
                    <flux:textarea wire:model="approveNotes" placeholder="Tambah catatan (opsional)" rows="3" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button type="button" variant="outline" wire:click="reviseContent">
                    <flux:icon.pencil class="size-4" />
                    Revisi
                </flux:button>
                <flux:button type="button" variant="primary" color="green" wire:click="approveContent">
                    <flux:icon.check class="size-4" />
                    Approve
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
