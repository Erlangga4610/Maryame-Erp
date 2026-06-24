<div class="space-y-6">
    <flux:heading size="lg">Assets — {{ $content->content_code }}</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-flux::card class="space-y-4">
            <flux:heading size="base">Final Asset</flux:heading>

            @if ($existingFinalAsset)
                <div class="relative">
                    @php $isImage = in_array(pathinfo($existingFinalAsset, PATHINFO_EXTENSION), ['jpg','jpeg','png','gif','webp']); @endphp
                    @if ($isImage)
                        <img src="{{ \App\Helpers\StorageHelper::url($existingFinalAsset) }}" class="h-28 w-auto object-cover rounded" />
                    @else
                        <div class="flex items-center gap-2 text-sm text-zinc-500">
                            <flux:icon.video /> Video asset
                        </div>
                    @endif
                    <flux:button wire:click="deleteFinalAsset" size="xs" variant="danger" class="absolute top-1 right-1">
                        Hapus
                    </flux:button>
                </div>
            @endif

            <flux:field>
                <flux:label>Upload Asset Final</flux:label>
                <flux:input type="file" wire:model.live="finalAsset" accept="image/*,video/*" />
                <flux:error name="finalAsset" />
            </flux:field>
            <flux:button wire:click="uploadFinalAsset" variant="primary" :disabled="!$finalAsset">
                Upload
            </flux:button>
        </x-flux::card>

        <x-flux::card class="space-y-4">
            <flux:heading size="base">Thumbnail</flux:heading>

            @if ($existingThumbnail)
                <div class="relative">
                    <img src="{{ \App\Helpers\StorageHelper::url($existingThumbnail) }}" class="h-28 w-auto object-cover rounded" />
                    <flux:button wire:click="deleteThumbnail" size="xs" variant="danger" class="absolute top-1 right-1">
                        Hapus
                    </flux:button>
                </div>
            @endif

            <flux:field>
                <flux:label>Upload Thumbnail</flux:label>
                <flux:input type="file" wire:model.live="thumbnail" accept="image/*" />
                <flux:error name="thumbnail" />
            </flux:field>
            <flux:button wire:click="uploadThumbnail" variant="primary" :disabled="!$thumbnail">
                Upload
            </flux:button>
        </x-flux::card>
    </div>

    <x-flux::card class="space-y-4">
        <flux:heading size="base">Version History ({{ $versions->count() }})</flux:heading>
        <div class="space-y-2">
            @forelse ($versions as $version)
                <div class="flex items-center justify-between p-2 bg-zinc-50 dark:bg-zinc-800 rounded text-sm">
                    <span>v{{ $version->version }} — {{ $version->created_at->format('d M Y H:i') }}</span>
                    <span class="text-zinc-500">{{ $version->creator?->name ?? 'System' }}</span>
                </div>
            @empty
                <p class="text-zinc-500 text-sm">Belum ada versi.</p>
            @endforelse
        </div>
    </x-flux::card>
</div>
