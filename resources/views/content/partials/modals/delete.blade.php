<flux:modal name="delete-modal" wire:model="showDeleteModal" class="w-md" wire:key="delete-modal">
    <div class="p-6 text-center space-y-4">
        <div class="mx-auto size-12 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center">
            <flux:icon.exclamation-triangle class="size-6 text-red-600 dark:text-red-400" />
        </div>

        <div>
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Hapus Konten</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Apakah Anda yakin ingin menghapus konten ini?<br>
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <div class="flex justify-center gap-3">
            <flux:button type="button" variant="outline" wire:click="cancelDelete">
                Batal
            </flux:button>
            <flux:button type="button" variant="danger" wire:click="executeDelete">
                Ya, Hapus
            </flux:button>
        </div>
    </div>
</flux:modal>
