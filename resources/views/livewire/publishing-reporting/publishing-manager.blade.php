<div class="space-y-6">
    <flux:heading size="lg">Publishing — {{ $content->content_code }}</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-flux::card class="space-y-4">
            <flux:heading size="base">Schedule</flux:heading>
            @if ($content->status->value === 'approved')
                <flux:button wire:click="openScheduleModal" variant="primary">Jadwalkan</flux:button>
            @elseif ($content->status->value === 'scheduled')
                <p class="text-sm text-amber-600">
                    Terjadwal: {{ $content->publish_date?->format('d M Y') }} {{ $content->publish_time?->format('H:i') }}
                </p>
            @else
                <p class="text-sm text-zinc-500">Status: {{ $content->status->value }}</p>
            @endif
        </x-flux::card>

        <x-flux::card class="space-y-4">
            <flux:heading size="base">Publish</flux:heading>
            @if (in_array($content->status->value, ['approved', 'scheduled']))
                <flux:button wire:click="openPublishModal" variant="primary">Mark Published</flux:button>
            @elseif ($content->status->value === 'published')
                <p class="text-sm text-emerald-600">Published</p>
                <a href="{{ $content->live_url }}" target="_blank" class="text-sm text-blue-600 underline">Lihat Live</a>
            @else
                <p class="text-sm text-zinc-500">Status: {{ $content->status->value }}</p>
            @endif
        </x-flux::card>

        <x-flux::card class="space-y-4">
            <flux:heading size="base">Post-Publish Checklist</flux:heading>
            @if ($content->status->value === 'published')
                <flux:button wire:click="openChecklistModal" variant="primary">
                    {{ $checklist ? 'Edit Checklist' : 'Isi Checklist' }}
                </flux:button>
            @else
                <p class="text-sm text-zinc-500">Available setelah publish</p>
            @endif
        </x-flux::card>
    </div>

    @if ($showScheduleModal)
        <x-flux::modal wire:model="showScheduleModal" title="Jadwalkan Konten">
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Tanggal Publish</flux:label>
                    <flux:input type="date" wire:model.live="scheduleDate" />
                    <flux:error name="scheduleDate" />
                </flux:field>
                <flux:field>
                    <flux:label>Jam Publish</flux:label>
                    <flux:input type="time" wire:model.live="scheduleTime" />
                    <flux:error name="scheduleTime" />
                </flux:field>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="closeScheduleModal" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="confirmSchedule" variant="primary">Simpan Jadwal</flux:button>
                </div>
            </div>
        </x-flux::modal>
    @endif

    @if ($showPublishModal)
        <x-flux::modal wire:model="showPublishModal" title="Publikasikan Konten">
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Link Konten Live</flux:label>
                    <flux:input wire:model.live="publishLiveUrl" placeholder="https://..." />
                    <flux:error name="publishLiveUrl" />
                </flux:field>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="closePublishModal" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="confirmPublish" variant="primary">Publikasikan</flux:button>
                </div>
            </div>
        </x-flux::modal>
    @endif

    @if ($showChecklistModal)
        <x-flux::modal wire:model="showChecklistModal" title="Post-Publish Checklist">
            <div class="space-y-4">
                @php
                    $checklistItems = [
                        'link_works' => 'Link berfungsi',
                        'thumbnail_visible' => 'Thumbnail muncul',
                        'caption_accurate' => 'Caption sesuai',
                        'hashtags_included' => 'Hashtag tercantum',
                        'cta_functional' => 'CTA berfungsi',
                        'product_tagged' => 'Produk di-tag',
                        'no_typo' => 'Tidak ada typo',
                        'audio_sync' => 'Audio sync',
                    ];
                @endphp
                @foreach ($checklistItems as $key => $label)
                    <flux:field>
                        <flux:checkbox wire:model.live="checklist.{{ $key }}" label="{{ $label }}" />
                    </flux:field>
                @endforeach
                <flux:field>
                    <flux:label>Catatan</flux:label>
                    <flux:textarea wire:model.live="checklist.notes" rows="2" />
                </flux:field>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="closeChecklistModal" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="saveChecklist" variant="primary">Simpan Checklist</flux:button>
                </div>
            </div>
        </x-flux::modal>
    @endif
</div>
