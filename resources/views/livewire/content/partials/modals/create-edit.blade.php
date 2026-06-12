<style>
    [data-flux-textarea] {
        padding: 16px !important;
        line-height: 1.6 !important;
        min-height: 120px !important;
        font-size: 14px !important;
        font-weight: 400 !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    }

    [data-flux-textarea]:focus {
        border-color: #ec4899 !important;
        box-shadow: 0 0 0 2px rgba(236, 72, 153, 0.15) !important;
    }

    [data-flux-textarea]::placeholder {
        color: #a1a1aa !important;
        font-weight: 400 !important;
        opacity: 0.8 !important;
    }

    .dark [data-flux-textarea]::placeholder {
        color: #71717a !important;
    }
</style>

@if($showModal)
    <flux:modal size="lg" wire:model="showModal" wire:key="create-edit-modal">
        <div x-data="{ tab: 'detail' }" class="p-6">
            <h2 class="text-lg font-bold mb-4">
                {{ $modalMode === 'create' ? 'Buat Konten Baru' : 'Edit Konten' }}
            </h2>

            <div class="flex gap-1 mb-5 border-b border-zinc-200 dark:border-zinc-700 overflow-x-auto">
                <button type="button" @click="tab = 'detail'" :class="tab === 'detail' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors whitespace-nowrap">
                    Detail
                </button>
                <button type="button" @click="tab = 'strategic'" :class="tab === 'strategic' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors whitespace-nowrap">
                    Strategic Brief @if(!$isCsp)<span class="text-[10px] text-zinc-400 ml-1">(read-only)</span>@endif
                </button>
                <button type="button" @click="tab = 'technical'" :class="tab === 'technical' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors whitespace-nowrap">
                    Technical Brief @if(!$isSms)<span class="text-[10px] text-zinc-400 ml-1">(read-only)</span>@endif
                </button>
                <button type="button" @click="tab = 'asset'" :class="tab === 'asset' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors whitespace-nowrap">
                    Asset
                </button>
            </div>

            @if($isBriefFinal && $modalMode === 'edit')
                <div class="mb-4 px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
                    <flux:icon.check-circle class="size-4" />
                    Brief telah difinalisasi — field strategis & teknis tidak bisa diubah.
                </div>
            @endif

            <form wire:submit="save" class="space-y-6">
                @include('livewire.content.partials.modals.tabs.detail')
                @include('livewire.content.partials.modals.tabs.strategic')
                @include('livewire.content.partials.modals.tabs.technical')
                @include('livewire.content.partials.modals.tabs.asset')

                @if($modalMode === 'edit' && $editingContentStatus !== 'draft')
                    @include('livewire.content.partials.modals._adjustment')
                @endif

                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="button" variant="outline" wire:click="closeModal">
                        Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" color="pink">
                        {{ $modalMode === 'create' ? 'Simpan' : 'Update' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
@endif
