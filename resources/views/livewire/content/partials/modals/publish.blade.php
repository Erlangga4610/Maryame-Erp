<flux:modal name="publish-modal" wire:model="showPublishModal" class="w-md" wire:key="publish-modal">
    <div class="p-6 space-y-4">
        <div>
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Mark Published</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">Konfirmasi bahwa konten sudah live.</p>
        </div>

        <flux:field>
            <flux:label>Live URL</flux:label>
            <flux:input type="url" wire:model="publishLiveUrl" placeholder="https://tiktok.com/@user/video/..." />
            <flux:error name="publishLiveUrl" />
        </flux:field>

        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button type="button" variant="outline" wire:click="closePublishModal">
                Batal
            </flux:button>
            <flux:button type="button" variant="primary" color="pink" wire:click="confirmPublish">
                Mark Published
            </flux:button>
        </div>
    </div>
</flux:modal>
