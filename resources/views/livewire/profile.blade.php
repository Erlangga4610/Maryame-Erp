<div class="space-y-6">
    {{-- Profil card --}}
    <flux:card class="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="relative shrink-0">
                <div class="size-20 rounded-2xl bg-gradient-to-br from-pink-500 to-purple-500 flex items-center justify-center overflow-hidden">
                    @if($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" alt="Preview" class="size-full object-cover" />
                    @elseif($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="size-full object-cover" />
                    @else
                        <span class="text-3xl font-bold text-white">{{ strtoupper(substr($name ?: 'U', 0, 1)) }}</span>
                    @endif
                </div>
                <label for="avatar-upload" class="absolute -bottom-2 -right-2 size-8 rounded-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-600 flex items-center justify-center cursor-pointer hover:bg-pink-50 dark:hover:bg-zinc-700 transition-colors">
                    <flux:icon name="camera" class="size-4 text-pink-500" />
                </label>
                <input id="avatar-upload" type="file" wire:model="avatar" accept="image/*" class="hidden" />
            </div>

            @if($avatar || $avatarUrl)
                <div class="flex items-center gap-2 sm:self-start">
                    @if($avatar)
                        <flux:button size="sm" variant="primary" color="pink" wire:click="uploadAvatar">
                            <flux:icon name="check" class="size-3.5 mr-1" /> Simpan Foto
                        </flux:button>
                    @endif
                    @if($avatarUrl)
                        <flux:button size="sm" variant="outline" wire:click="removeAvatar">Hapus Foto</flux:button>
                    @endif
                </div>
            @endif

            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-zinc-800 dark:text-white truncate">{{ $name ?: '-' }}</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $position ?: '-' }} @if($department) · {{ $department }} @endif
                </p>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    @forelse($roles as $role)
                        <flux:badge size="sm" :color="match($role->rbac_tier) { 1 => 'pink', 2 => 'blue', 3 => 'zinc', default => 'zinc' }">
                            {{ $role->name }} (Tier {{ $role->rbac_tier }})
                        </flux:badge>
                    @empty
                        <flux:badge size="sm" color="zinc">Tanpa role</flux:badge>
                    @endforelse
                </div>
            </div>
            <div class="text-sm text-zinc-500 dark:text-zinc-400 shrink-0 sm:text-right">
                <p class="flex items-center gap-1.5 sm:justify-end">
                    <flux:icon name="briefcase" class="size-4" />
                    <span>{{ $employee_id ?: '-' }}</span>
                </p>
                <p class="mt-1 text-xs">Bergabung sejak {{ Auth::user()->created_at?->format('d M Y') }}</p>
            </div>
        </div>

        <flux:error name="avatar" class="mt-3" />
        <div wire:loading wire:target="avatar" class="mt-2 text-sm text-pink-600">Uploading...</div>
    </flux:card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Edit profil --}}
        <flux:card class="p-6">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon name="user-circle" class="size-4 text-pink-500" />
                Data Diri
            </h3>

            <form wire:submit="updateProfile" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        <flux:input wire:model="employee_id" disabled placeholder="Tidak dapat diubah" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Nomor HP</flux:label>
                        <flux:input type="tel" wire:model="phone" placeholder="08xxxxxxxxxx" />
                        <flux:error name="phone" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Posisi</flux:label>
                        <flux:input wire:model="position" placeholder="Jabatan" />
                        <flux:error name="position" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Departemen</flux:label>
                        <flux:select wire:model="department">
                            <option value="">Pilih departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="department" />
                    </flux:field>
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="submit" variant="primary" color="pink">
                        <flux:icon name="check" class="size-4 mr-1" /> Simpan Profil
                    </flux:button>
                </div>
            </form>
        </flux:card>

        {{-- Ganti password --}}
        <flux:card class="p-6">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon name="key" class="size-4 text-pink-500" />
                Ubah Password
            </h3>

            <form wire:submit="updatePassword" class="space-y-4">
                <flux:field>
                    <flux:label>Password saat ini *</flux:label>
                    <flux:input type="password" wire:model="current_password" placeholder="Masukkan password lama" />
                    <flux:error name="current_password" />
                </flux:field>

                <flux:field>
                    <flux:label>Password baru *</flux:label>
                    <flux:input type="password" wire:model="password" placeholder="Min 8 karakter" />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Konfirmasi password baru *</flux:label>
                    <flux:input type="password" wire:model="password_confirmation" placeholder="Ulangi password baru" />
                    <flux:error name="password_confirmation" />
                </flux:field>

                <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="submit" variant="primary" color="pink">
                        <flux:icon name="arrow-path" class="size-4 mr-1" /> Ganti Password
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>