<div x-show="tab === 'asset'" x-cloak>
    <div class="space-y-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="size-2 rounded-full bg-pink-500"></div>
                <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Final Asset</h3>
            </div>
            <p class="text-sm text-zinc-400 dark:text-zinc-500 italic mb-3 ml-4">
                Upload file asset final (gambar/video)
            </p>
            <flux:field>
                <input type="file" wire:model="finalAsset" accept="image/*,video/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 dark:file:bg-pink-900/30 dark:file:text-pink-300" />
                <flux:error name="finalAsset" />
                @if($existingFinalAsset)
                    <div class="mt-2">
                        @php
                            $ext = strtolower(pathinfo($existingFinalAsset, PATHINFO_EXTENSION));
                            $url = \App\Helpers\StorageHelper::url($existingFinalAsset);
                            $name = \Illuminate\Support\Str::after($existingFinalAsset, '/');
                            $displayName = \Illuminate\Support\Str::limit($name, 40);
                        @endphp
                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <a href="{{ $url }}" target="_blank" class="block group relative">
                                <img src="{{ $url }}" alt="Final asset"
                                    class="h-28 w-auto rounded-lg border border-zinc-200 dark:border-zinc-600 object-cover group-hover:opacity-90 transition-opacity" />
                                <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400 truncate max-w-[200px]">{{ $displayName }}</span>
                            </a>
                        @else
                            <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm text-pink-600 dark:text-pink-400 hover:underline">
                                <flux:icon.video-camera class="size-4" />
                                <span class="truncate max-w-[200px]">{{ $displayName }}</span>
                            </a>
                        @endif
                    </div>
                @endif
                <div wire:loading wire:target="finalAsset" class="mt-2 text-sm text-pink-600">Uploading...</div>
            </flux:field>
        </div>

        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="size-2 rounded-full bg-pink-500"></div>
                <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Thumbnail</h3>
            </div>
            <p class="text-sm text-zinc-400 dark:text-zinc-500 italic mb-3 ml-4">
                Upload thumbnail/gambar preview
            </p>
            <flux:field>
                <input type="file" wire:model="thumbnail" accept="image/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 dark:file:bg-pink-900/30 dark:file:text-pink-300" />
                <flux:error name="thumbnail" />
                @if($existingThumbnail)
                    @php $thumbUrl = \App\Helpers\StorageHelper::url($existingThumbnail); @endphp
                    <div class="mt-2">
                        <img src="{{ $thumbUrl }}" class="h-24 w-auto rounded-lg border border-zinc-200 dark:border-zinc-600 object-cover" alt="current thumbnail" />
                        <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400 truncate max-w-[200px]">{{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::after($existingThumbnail, '/'), 40) }}</span>
                    </div>
                @endif
                <div wire:loading wire:target="thumbnail" class="mt-2 text-sm text-pink-600">Uploading...</div>
            </flux:field>
        </div>
    </div>
</div>
