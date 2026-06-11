<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-zinc-800 dark:text-white">Platforms</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola platform konten</p>
        </div>

        @if($this->canEdit())
            <flux:button variant="primary" color="pink" wire:click="openCreate">
                <flux:icon.plus class="size-4 mr-1" /> Tambah Platform
            </flux:button>
        @endif
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Kode</flux:table.column>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Originality Strict</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($platforms as $platform)
                <flux:table.row wire:key="platform-{{ $platform->id }}">
                    <flux:table.cell class="font-mono text-xs">{{ $platform->code }}</flux:table.cell>
                    <flux:table.cell>{{ $platform->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$platform->originality_strict ? 'red' : 'zinc'">
                            {{ $platform->originality_strict ? 'Ketat' : 'Longgar' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$platform->is_active ? 'green' : 'zinc'">
                            {{ $platform->is_active ? 'Aktif' : 'Nonaktif' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($this->canEdit())
                            <flux:button size="sm" variant="outline" wire:click="openEdit({{ $platform->id }})">
                                Edit
                            </flux:button>
                            <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $platform->id }})">
                                Hapus
                            </flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">Belum ada platform</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div>{{ $platforms->links() }}</div>

    @if($showDeleteModal)
        <flux:modal wire:model="showDeleteModal" class="w-md" wire:key="delete-modal">
            <div class="p-6 text-center space-y-4">
                <div class="mx-auto size-12 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center">
                    <flux:icon.exclamation-triangle class="size-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Hapus Platform</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Apakah Anda yakin ingin menghapus platform <strong>{{ $deleteName }}</strong>?<br>
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="flex justify-center gap-3">
                    <flux:button type="button" variant="outline" wire:click="cancelDelete">Batal</flux:button>
                    <flux:button type="button" variant="danger" wire:click="executeDelete">Ya, Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    @if($showModal)
        <flux:modal wire:model="showModal" class="w-md">
            <form wire:submit="save" class="p-6 space-y-4">
                <h3 class="text-lg font-bold">{{ $modalMode === 'create' ? 'Tambah Platform' : 'Edit Platform' }}</h3>

                <flux:field>
                    <flux:label>Kode Platform *</flux:label>
                    <flux:input wire:model="code" placeholder="Contoh: TKM" maxlength="5" />
                    <flux:error name="code" />
                </flux:field>

                <flux:field>
                    <flux:label>Nama Platform *</flux:label>
                    <flux:input wire:model="name" placeholder="Contoh: TikTok" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <div class="flex items-center gap-3">
                        <flux:checkbox wire:model="originality_strict" />
                        <flux:label>Originality Strict</flux:label>
                    </div>
                </flux:field>

                <flux:field>
                    <div class="flex items-center gap-3">
                        <flux:checkbox wire:model="is_active" />
                        <flux:label>Aktif</flux:label>
                    </div>
                </flux:field>

                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="button" variant="outline" wire:click="closeModal">Batal</flux:button>
                    <flux:button type="submit" variant="primary" color="pink">Simpan</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
