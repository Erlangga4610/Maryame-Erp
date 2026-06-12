<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-zinc-800 dark:text-white">Users</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola pengguna sistem</p>
        </div>

        @if($this->canEdit())
            <flux:button variant="primary" color="pink" wire:click="openCreate">
                <flux:icon.plus class="size-4 mr-1" /> Tambah User
            </flux:button>
        @endif
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Departemen</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($users as $user)
                <flux:table.row wire:key="user-{{ $user->id }}">
                    <flux:table.cell class="font-medium">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell class="text-xs">{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @foreach($user->roles as $role)
                            <flux:badge size="sm" :color="match($role->rbac_tier) { 1 => 'pink', 2 => 'blue', 3 => 'zinc', default => 'zinc' }">
                                {{ $role->name }}
                            </flux:badge>
                        @endforeach
                    </flux:table.cell>
                    <flux:table.cell class="text-xs">{{ $user->department ?? '-' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$user->is_active ? 'green' : 'zinc'">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($this->canEdit())
                            <flux:button size="sm" variant="outline" wire:click="openEdit({{ $user->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $user->id }})">Hapus</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">Belum ada user</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div>{{ $users->links() }}</div>

    @if($showDeleteModal)
        <flux:modal wire:model="showDeleteModal" class="w-md" wire:key="delete-modal">
            <div class="p-6 text-center space-y-4">
                <div class="mx-auto size-12 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center">
                    <flux:icon.exclamation-triangle class="size-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Hapus User</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Apakah Anda yakin ingin menghapus user <strong>{{ $deleteName }}</strong>?<br>
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
        <flux:modal wire:model="showModal" class="w-lg">
            <form wire:submit="save" class="p-6 space-y-4">
                <h3 class="text-lg font-bold">{{ $modalMode === 'create' ? 'Tambah User' : 'Edit User' }}</h3>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Nama *</flux:label>
                        <flux:input wire:model="name" placeholder="Nama lengkap" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Email *</flux:label>
                        <flux:input type="email" wire:model="email" placeholder="user@maryame.com" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Employee ID</flux:label>
                        <flux:input wire:model="employee_id" placeholder="EMP-XXX" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Role *</flux:label>
                        <flux:select wire:model="role">
                            <option value="">Pilih role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }} (Tier {{ $role->rbac_tier }})</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="role" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Posisi</flux:label>
                        <flux:input wire:model="position" placeholder="Jabatan" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Departemen</flux:label>
                        <flux:select wire:model="department">
                            <option value="">Pilih departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>Password @if($modalMode === 'edit')<span class="text-xs text-zinc-400">(kosongi jika tidak diubah)</span>@endif</flux:label>
                        <flux:input type="password" wire:model="password" placeholder="Min 8 karakter" />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Konfirmasi Password</flux:label>
                        <flux:input type="password" wire:model="password_confirmation" placeholder="Ulangi password" />
                        <flux:error name="password_confirmation" />
                    </flux:field>
                </div>

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
