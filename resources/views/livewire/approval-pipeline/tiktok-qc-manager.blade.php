<div class="space-y-6">
    <flux:heading size="lg">QC TikTok — {{ $content->content_code }}</flux:heading>

    <x-flux::card class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            @php
                $criteriaLabels = [
                    'k1_audio_original' => '1. Voice/Audio Original',
                    'k2_demo_penggunaan' => '2. Demo Penggunaan',
                    'k3_produk_visible' => '3. Produk Visible Jelas',
                    'k4_manfaat_verbal' => '4. Manfaat/Benefit Disebut Verbal',
                    'k5_tambahan' => '5. ' . ($qc['k5_label'] ?: 'Kriteria Tambahan 1'),
                    'k6_tambahan' => '6. ' . ($qc['k6_label'] ?: 'Kriteria Tambahan 2'),
                ];
            @endphp

            @foreach ($criteriaLabels as $key => $label)
                <flux:field>
                    <flux:label>{{ $label }}</flux:label>
                    <div class="flex gap-2 mt-1">
                        <flux:radio.group wire:model.live="qc.{{ $key }}">
                            <flux:radio value="pass" label="Pass" />
                            <flux:radio value="fail" label="Fail" />
                            <flux:radio value="na" label="N/A" />
                        </flux:radio.group>
                    </div>
                </flux:field>
            @endforeach

            <flux:field>
                <flux:label>Label Kriteria 5</flux:label>
                <flux:input wire:model.live="qc.k5_label" placeholder="Kriteria tambahan 1..." />
            </flux:field>

            <flux:field>
                <flux:label>Label Kriteria 6</flux:label>
                <flux:input wire:model.live="qc.k6_label" placeholder="Kriteria tambahan 2..." />
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model.live="qc.has_shopping_cart" label="Memiliki Shopping Cart (Keranjang Kuning)" />
            </flux:field>

            <div class="col-span-2">
                <flux:field>
                    <flux:label>Sub-tipe yang disarankan</flux:label>
                    <flux:input :value="$this->suggested_subtype ?? '—'" readonly />
                </flux:field>
            </div>

            <div class="col-span-2">
                <flux:field>
                    <flux:label>Catatan</flux:label>
                    <flux:textarea wire:model.live="qc.notes" rows="3" />
                    <flux:error name="qc.notes" />
                </flux:field>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <flux:button wire:click="$dispatch('close-modal', {modal: 'qc'})" variant="ghost">Batal</flux:button>
            <flux:button wire:click="save" variant="primary">Simpan QC</flux:button>
        </div>
    </x-flux::card>
</div>
