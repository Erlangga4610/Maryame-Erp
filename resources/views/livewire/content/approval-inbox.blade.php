<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-zinc-800 dark:text-white">Approval Inbox</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                @php $stageLabel = match($userRole) {
                    'CW' => 'CW',
                    'CSP' => 'CSP',
                    'SMS' => 'SMS',
                    'RnD' => 'RnD',
                    'Legal' => 'Legal',
                    default => null,
                }; @endphp
                @if($userRole === 'Super Admin')
                    Semua konten yang menunggu approval (Super Admin)
                @elseif($stageLabel)
                    Konten yang menunggu approval {{ $stageLabel }}
                @else
                    Semua konten yang menunggu approval
                @endif
            </p>
        </div>

        <flux:select wire:model.live="filterStage" class="w-44">
            <option value="">Semua Stage</option>
            <option value="cw">CW</option>
            <option value="csp">CSP</option>
            <option value="sms">SMS</option>
            <option value="rnd">RnD</option>
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
                        $cwApproval = $content->approvals->where('stage', 'cw')->first();
                        $cspApproval = $content->approvals->where('stage', 'csp')->first();
                        $smsApproval = $content->approvals->where('stage', 'sms')->first();
                        $rndApproval = $content->approvals->where('stage', 'rnd')->first();
                        $legalApproval = $content->approvals->where('stage', 'legal')->first();
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
                                @foreach(['cw' => 'CW', 'csp' => 'CSP', 'sms' => 'SMS', 'rnd' => 'RnD', 'legal' => 'Legal'] as $stageKey => $stageLabel)
                                    @php $a = $content->approvals->where('stage', $stageKey)->first(); @endphp
                                    @if($a)
                                        <flux:badge size="sm" :color="$a->status === 'approved' ? 'green' : ($a->status === 'revision' ? 'red' : 'amber')">
                                            {{ $stageLabel }}: {{ ucfirst($a->status) }}
                                        </flux:badge>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-2 shrink-0">
                            <flux:button size="sm" variant="outline" href="/contents">
                                Detail
                            </flux:button>

                            @php
                                $stageMap = ['CW' => 'cw', 'CSP' => 'csp', 'SMS' => 'sms', 'RnD' => 'rnd', 'Legal' => 'legal'];
                                $myStage = $stageMap[$userRole] ?? null;
                                $myApproval = $myStage ? $content->approvals->where('stage', $myStage)->first() : null;
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
                            @if($myApproval && $myApproval->status === 'pending' && $prevApproved)
                                <flux:button size="sm" variant="primary" color="green" wire:click="confirmApprove({{ $content->id }}, '{{ $myStage }}')">
                                    Approve
                                </flux:button>
                            @elseif($userRole === 'Super Admin')
                                @php $pendingStage = $content->approvals->where('status', 'pending')->first(); @endphp
                                @if($pendingStage)
                                    <flux:button size="sm" variant="primary" color="green" wire:click="confirmApprove({{ $content->id }}, '{{ $pendingStage->stage }}')">
                                        Approve ({{ strtoupper($pendingStage->stage) }})
                                    </flux:button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Pending Adjustments --}}
    @if($adjustments->isNotEmpty())
        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <h3 class="text-base font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                Adjustment Pending ({{ $adjustments->count() }})
            </h3>
            <div class="space-y-3">
                @foreach($adjustments as $adj)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 {{ $adj->type === 'reactive' ? 'border-l-4 border-l-red-500' : 'border-l-4 border-l-amber-500' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $adj->content?->content_code ?? '#' . $adj->content_id }}</span>
                                    <flux:badge size="sm" :color="$adj->type === 'reactive' ? 'red' : 'amber'">
                                        {{ ucfirst($adj->type) }}
                                    </flux:badge>
                                    <flux:badge size="sm" color="blue">
                                        {{ $adj->requester?->name ?? '-' }}
                                    </flux:badge>
                                </div>
                                <h4 class="font-medium text-zinc-800 dark:text-white truncate">{{ $adj->content?->theme ?? '-' }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $adj->reason }}</p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <flux:button size="sm" variant="outline" wire:click="approveAdjustment({{ $adj->id }})">
                                    Setujui
                                </flux:button>
                                <flux:button size="sm" variant="outline" color="red" wire:click="rejectAdjustment({{ $adj->id }})">
                                    Tolak
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Approve Modal --}}
    <flux:modal name="approve-inbox-modal" wire:model="showApproveModal" class="w-md" wire:key="approve-inbox-modal">
        <div class="p-6 space-y-4">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">
                Approval {{ strtoupper($approveStage) }}
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
