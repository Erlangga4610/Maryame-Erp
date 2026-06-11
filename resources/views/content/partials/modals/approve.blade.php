<flux:modal name="approve-modal" wire:model="showApproveModal" class="w-md" wire:key="approve-modal">
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
