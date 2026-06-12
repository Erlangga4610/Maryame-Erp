<div x-show="tab === 'strategic'" x-cloak>
    <div class="space-y-4">
        <div class="flex items-center gap-2 mb-1">
            <div class="size-2 rounded-full bg-pink-500"></div>
            <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Strategic Brief</h3>
            <span class="text-[11px] text-zinc-400 dark:text-zinc-500 italic">(CSP only)</span>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <flux:field>
                    <flux:label>Angle</flux:label>
                     <flux:input wire:model="form.angle" placeholder="Angle konten..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
                    @if(!$isCsp || $isBriefFinal)<flux:error name="form.angle" />@endif
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Positioning</flux:label>
                <flux:input wire:model="form.positioning" placeholder="Posisi brand..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
            </flux:field>

            <flux:field>
                <flux:label>Target Audience</flux:label>
                <flux:input wire:model="form.target_audience" placeholder="Target audiens..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
            </flux:field>

            <div class="col-span-2">
                <flux:field>
                    <flux:label>Key Message</flux:label>
                    <flux:textarea wire:model="form.key_message" rows="2" placeholder="Pesan utama..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Tone</flux:label>
                <flux:input wire:model="form.tone" placeholder="Tone of voice..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
            </flux:field>
        </div>

        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="size-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Copy Direction</h4>
            </div>
            <flux:field>
                <flux:textarea
                    wire:model="form.copy_brief"
                    rows="4"
                    placeholder="• Tone profesional namun santai&#10;• Fokus pada promo diskon 50%&#10;• Sertakan CTA &quot;Beli Sekarang&quot;&#10;• Highlight benefit produk di paragraf pertama"
                    :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))"
                />
            </flux:field>
        </div>
    </div>
</div>
