<flux:modal name="tiktok-qc-modal" wire:model="showQcModal" class="w-lg" wire:key="tiktok-qc-modal">
    <div class="p-6 space-y-5">
        <div>
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">QC TikTok</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">Checklist kualitas konten TikTok sebelum dipublikasikan.</p>
        </div>

        <div class="space-y-3">
            @php
                $checklist = [
                    'hook_strong' => 'Hook kuat di 3 detik pertama',
                    'cta_clear' => 'Call to action jelas',
                    'audio_clear' => 'Audio jernih, tidak pecah',
                    'visual_quality' => 'Kualitas visual baik (HD, cukup cahaya)',
                    'caption_complete' => 'Caption lengkap dengan hashtag relevan',
                    'product_visible' => 'Produk terlihat jelas',
                    'duration_appropriate' => 'Durasi sesuai (15–60 detik)',
                    'branding_included' => 'Branding/logo tercantum',
                    'no_sensitive_content' => 'Tidak ada konten sensitif/berisiko',
                ];
            @endphp

            @foreach($checklist as $key => $label)
                <label class="flex items-start gap-3 cursor-pointer group">
                    <flux:checkbox wire:model="qc.{{ $key }}" class="mt-0.5" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                </label>
            @endforeach
        </div>

        <div>
            <flux:field>
                <flux:label>Catatan QC</flux:label>
                <flux:textarea wire:model="qc.notes" placeholder="Catatan tambahan (jika ada item yang perlu diperbaiki)" rows="3" />
            </flux:field>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button type="button" variant="outline" wire:click="closeQcModal">
                Batal
            </flux:button>
            <flux:button type="button" variant="primary" color="pink" wire:click="saveQc">
                Simpan QC
            </flux:button>
        </div>
    </div>
</flux:modal>
