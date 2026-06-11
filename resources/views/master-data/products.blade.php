<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-zinc-800 dark:text-white">Products</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola produk Maryamé</p>
        </div>

        @if($this->canEdit())
            <flux:button variant="primary" color="pink" wire:click="openCreate">
                <flux:icon.plus class="size-4 mr-1" /> Tambah Produk
            </flux:button>
        @endif
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>SKU</flux:table.column>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Kategori</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($products as $product)
                <flux:table.row wire:key="product-{{ $product->id }}">
                    <flux:table.cell class="font-mono text-xs">{{ $product->sku }}</flux:table.cell>
                    <flux:table.cell>{{ $product->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm">{{ $product->category ?? '-' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$product->is_active ? 'green' : 'zinc'">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($this->canEdit())
                            <flux:button size="sm" variant="outline" wire:click="openEdit({{ $product->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="delete({{ $product->id }})" wire:confirm="Hapus produk {{ $product->name }}?">Hapus</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">Belum ada produk</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div>{{ $products->links() }}</div>

    @if($showModal)
        <flux:modal wire:model="showModal" class="w-md">
            <form wire:submit="save" class="p-6 space-y-4">
                <h3 class="text-lg font-bold">{{ $modalMode === 'create' ? 'Tambah Produk' : 'Edit Produk' }}</h3>

                <flux:field>
                    <flux:label>SKU *</flux:label>
                    <flux:input wire:model="sku" placeholder="Contoh: MYM-SERUM-001" />
                    <flux:error name="sku" />
                </flux:field>

                <flux:field>
                    <flux:label>Nama Produk *</flux:label>
                    <flux:input wire:model="name" placeholder="Nama produk" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Kategori</flux:label>
                    <flux:input wire:model="category" placeholder="Contoh: Serum, Moisturizer" />
                </flux:field>

                <flux:field>
                    <flux:label>Deskripsi</flux:label>
                    <flux:textarea wire:model="description" rows="2" placeholder="Deskripsi produk (opsional)" />
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
