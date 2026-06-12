<flux:modal name="tiktok-qc-modal" wire:model="showQcModal" class="w-lg" wire:key="tiktok-qc-modal">
    <div class="p-6 space-y-5">
        <div>
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">QC TikTok</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">Checklist 6 kriteria kualitas konten TikTok.</p>
        </div>

        <div class="space-y-4">
            @php
                $criteria = \App\Content\Models\TiktokQc::criteriaLabels();
            @endphp

            @foreach($criteria as $key => $label)
                <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">{{ $label }}</p>
                    <div class="flex gap-4">
                        @foreach(['pass' => 'Pass', 'fail' => 'Fail', 'na' => 'N/A'] as $val => $display)
                            <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="radio"
                                    wire:model="qc.{{ $key }}"
                                    value="{{ $val }}"
                                    name="qc_{{ $key }}"
                                    class="text-pink-600 focus:ring-pink-500"
                                >
                                <span class="text-zinc-600 dark:text-zinc-400">{{ $display }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
            <label class="flex items-center gap-3 cursor-pointer">
                <flux:checkbox wire:model="qc.has_shopping_cart" />
                <span class="text-sm text-zinc-700 dark:text-zinc-300">Keranjang Kuning (TikTok Shop cart)</span>
            </label>
        </div>

        @if($this->qc_suggested_subtype)
            <div class="bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg p-3">
                <p class="text-sm font-medium text-pink-700 dark:text-pink-300">
                    Sub-tipe tersaran: <span class="font-bold">{{ $this->qc_suggested_subtype }}</span>
                </p>
            </div>
        @endif

        <div>
            <flux:field>
                <flux:label>Catatan QC</flux:label>
                <flux:textarea wire:model="qc.notes" placeholder="Catatan tambahan (jika ada item yang perlu diperbaiki)" rows="2" />
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
