<div class="space-y-6">
    <flux:heading size="lg">Adjustment #{{ $adjustment->id }}</flux:heading>

    <x-flux::card class="space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium">Tipe:</span>
                <flux:badge :color="$adjustment->type === 'major' ? 'red' : ($adjustment->type === 'reactive' ? 'amber' : 'zinc')">
                    {{ ucfirst($adjustment->type) }}
                </flux:badge>
            </div>
            <div><span class="font-medium">Status:</span> {{ $adjustment->status }}</div>
            <div><span class="font-medium">Konten:</span> {{ $adjustment->content?->content_code }}</div>
            <div><span class="font-medium">Request oleh:</span> {{ $adjustment->requester?->name ?? '—' }}</div>
            <div class="col-span-2"><span class="font-medium">Alasan:</span> {{ $adjustment->reason }}</div>
        </div>

        @if ($adjustment->changed_fields)
            <div>
                <span class="font-medium text-sm">Field yang diubah:</span>
                <div class="mt-1 text-xs text-zinc-500">
                    {{ implode(', ', array_keys($adjustment->changed_fields)) }}
                </div>
            </div>
        @endif

        @if ($adjustment->status === 'pending')
            <div class="flex justify-end gap-2 pt-4 border-t">
                <flux:button wire:click="reject" variant="danger">Tolak</flux:button>
                <flux:button wire:click="approve" variant="primary">Setujui</flux:button>
            </div>
        @endif
    </x-flux::card>
</div>
