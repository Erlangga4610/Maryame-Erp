<div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-3">
    <div class="flex items-center gap-2">
        <div class="size-2 rounded-full bg-amber-500"></div>
        <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Tipe Adjustment</h3>
    </div>

    <div class="grid grid-cols-3 gap-2">
        <label class="flex items-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-600 p-3 cursor-pointer has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 dark:has-[:checked]:bg-pink-900/20">
            <input type="radio" wire:model="adjustmentType" value="minor" class="text-pink-600" />
            <div>
                <div class="text-sm font-medium text-zinc-800 dark:text-white">Minor</div>
                <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Perubahan kecil, langsung diterapkan</div>
            </div>
        </label>
        <label class="flex items-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-600 p-3 cursor-pointer has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 dark:has-[:checked]:bg-pink-900/20">
            <input type="radio" wire:model="adjustmentType" value="major" class="text-pink-600" />
            <div>
                <div class="text-sm font-medium text-zinc-800 dark:text-white">Major</div>
                <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Perubahan besar, perlu approval CSP</div>
            </div>
        </label>
        <label class="flex items-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-600 p-3 cursor-pointer has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 dark:has-[:checked]:bg-pink-900/20">
            <input type="radio" wire:model="adjustmentType" value="reactive" class="text-pink-600" />
            <div>
                <div class="text-sm font-medium text-zinc-800 dark:text-white">Reactive</div>
                <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Darurat, langsung terapkan + konfirmasi</div>
            </div>
        </label>
    </div>

    <div x-show="$wire.adjustmentType !== 'minor'" x-cloak>
        <flux:field>
            <flux:label>Alasan Adjustment *</flux:label>
            <flux:textarea wire:model="adjustmentReason" rows="2" placeholder="Jelaskan alasan perubahan..." />
            <flux:error name="adjustmentReason" />
        </flux:field>
    </div>
</div>
