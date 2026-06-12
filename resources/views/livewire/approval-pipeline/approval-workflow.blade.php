<div class="space-y-6">
    <flux:heading size="lg">Approval — {{ $content->content_code }}</flux:heading>

    <x-flux::card class="space-y-4">
        <div class="text-sm">
            <p><span class="font-medium">Stage:</span> {{ strtoupper($approval->stage) }}</p>
            <p><span class="font-medium">Status:</span> {{ $approval->status }}</p>
        </div>

        @if ($approval->status === 'pending')
            <div class="flex justify-end gap-2 pt-4 border-t">
                <flux:button wire:click="confirmApprove" variant="primary">Approve</flux:button>
                <flux:button wire:click="reviseContent" variant="warning">Request Revision</flux:button>
            </div>
        @endif
    </x-flux::card>

    @if ($showApproveModal)
        <x-flux::modal wire:model="showApproveModal" title="Konfirmasi Approval">
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Catatan (opsional)</flux:label>
                    <flux:textarea wire:model.live="approveNotes" rows="3" />
                </flux:field>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="cancelApprove" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="approveContent" variant="primary">Approve</flux:button>
                </div>
            </div>
        </x-flux::modal>
    @endif
</div>
