<flux:modal name="post-publish-checklist-modal" wire:model="showChecklistModal" class="w-lg" wire:key="post-publish-checklist-modal">
    <div class="p-6 space-y-5">
        <div>
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Post-Publish Checklist</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">Verifikasi konten yang sudah live.</p>
        </div>

        <div class="space-y-3">
            @php
                $items = [
                    'link_works' => 'Link live berfungsi (bisa diakses)',
                    'thumbnail_visible' => 'Thumbnail tampil dengan benar',
                    'caption_accurate' => 'Caption sesuai brief (tidak ada perubahan)',
                    'hashtags_included' => 'Semua hashtag tercantum',
                    'cta_functional' => 'CTA berfungsi (link/button)',
                    'product_tagged' => 'Produk sudah di-tag dengan benar',
                    'no_typo' => 'Tidak ada typo di caption/teks',
                    'audio_sync' => 'Audio sinkron dengan visual',
                ];
            @endphp

            @foreach($items as $key => $label)
                <label class="flex items-start gap-3 cursor-pointer group">
                    <flux:checkbox wire:model="checklist.{{ $key }}" class="mt-0.5" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                </label>
            @endforeach
        </div>

        <div>
            <flux:field>
                <flux:label>Catatan</flux:label>
                <flux:textarea wire:model="checklist.notes" placeholder="Catatan tambahan..." rows="2" />
            </flux:field>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button type="button" variant="outline" wire:click="closeChecklistModal">
                Tutup
            </flux:button>
            <flux:button type="button" variant="primary" color="pink" wire:click="saveChecklist">
                Simpan Checklist
            </flux:button>
        </div>
    </div>
</flux:modal>
