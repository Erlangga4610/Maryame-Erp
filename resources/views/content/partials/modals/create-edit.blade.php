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

            <div class="flex gap-1 mb-5 border-b border-zinc-200 dark:border-zinc-700">
                <button type="button" @click="tab = 'detail'" :class="tab === 'detail' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors">
                    Detail
                </button>
                <button type="button" @click="tab = 'copy'" :class="tab === 'copy' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors">
                    Copy Brief
                </button>
                <button type="button" @click="tab = 'visual'" :class="tab === 'visual' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors">
                    Visual Brief
                </button>
                <button type="button" @click="tab = 'video'" :class="tab === 'video' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors">
                    Video Brief
                </button>
                <button type="button" @click="tab = 'asset'" :class="tab === 'asset' ? 'border-b-2 border-pink-600 text-pink-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'" class="px-3 py-2 text-sm font-medium transition-colors">
                    Asset
                </button>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div x-show="tab === 'detail'" x-cloak>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Platform *</flux:label>
                            <flux:select
                                wire:model="form.platform_id"
                                placeholder="Pilih platform"
                            >
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="form.platform_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Priority *</flux:label>
                            <flux:select
                                wire:model="form.priority"
                                placeholder="Pilih priority"
                            >
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </flux:select>
                            <flux:error name="form.priority" />
                        </flux:field>

                        <div class="col-span-2">
                            <flux:field>
                                <flux:label>Tema *</flux:label>
                                <flux:input
                                    wire:model="form.theme"
                                    placeholder="Masukkan tema konten..."
                                />
                                <flux:error name="form.theme" />
                            </flux:field>
                        </div>

                        <div class="col-span-2">
                            <flux:field>
                                <flux:label>Caption</flux:label>
                                <flux:textarea
                                    wire:model="form.caption"
                                    rows="2"
                                    placeholder="Caption konten..."
                                />
                                <flux:error name="form.caption" />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>Tanggal Publish</flux:label>
                            <flux:input
                                type="date"
                                wire:model="form.publish_date"
                            />
                            <flux:error name="form.publish_date" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Deadline Produksi</flux:label>
                            <flux:input
                                type="date"
                                wire:model="form.deadline_produksi"
                            />
                            <flux:error name="form.deadline_produksi" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Product</flux:label>
                            <flux:select
                                wire:model="form.product_id"
                                placeholder="Pilih product (opsional)"
                            >
                                <option value="">Pilih product (opsional)</option>
                                @foreach(\App\Models\Product::pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="form.product_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Campaign</flux:label>
                            <flux:select
                                wire:model="form.campaign_id"
                                placeholder="Pilih campaign (opsional)"
                            >
                                <option value="">Pilih campaign (opsional)</option>
                                @foreach(\App\Models\Campaign::pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="form.campaign_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>PIC Copy</flux:label>
                            <flux:select wire:model="form.pic_copy_id" placeholder="Pilih PIC Copy">
                                <option value="">Pilih PIC Copy</option>
                                @foreach($assignees as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="form.pic_copy_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>PIC Visual</flux:label>
                            <flux:select wire:model="form.pic_visual_id" placeholder="Pilih PIC Visual">
                                <option value="">Pilih PIC Visual</option>
                                @foreach($assignees as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="form.pic_visual_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>PIC Video</flux:label>
                            <flux:select wire:model="form.pic_video_id" placeholder="Pilih PIC Video">
                                <option value="">Pilih PIC Video</option>
                                @foreach($assignees as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="form.pic_video_id" />
                        </flux:field>
                    </div>
                </div>

                <div x-show="tab === 'copy'" x-cloak>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="size-2 rounded-full bg-pink-500"></div>
                            <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Copy Brief</h3>
                        </div>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 italic mb-3 ml-4">
                            Arahan copywriting, tone of voice, CTA
                        </p>
                        <flux:field>
                            <flux:textarea
                                wire:model="form.copy_brief"
                                rows="5"
                                placeholder="• Tone profesional namun santai&#10;• Fokus pada promo diskon 50%&#10;• Sertakan CTA \"Beli Sekarang\"&#10;• Highlight benefit produk di paragraf pertama"
                            />
                            <flux:error name="form.copy_brief" />
                        </flux:field>
                    </div>
                </div>

                <div x-show="tab === 'visual'" x-cloak>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="size-2 rounded-full bg-pink-500"></div>
                            <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Visual Brief</h3>
                        </div>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 italic mb-3 ml-4">
                            Konsep visual, referensi desain, layout
                        </p>
                        <flux:field>
                            <flux:textarea
                                wire:model="form.visual_brief"
                                rows="5"
                                placeholder="• Flat lay style dengan background putih&#10;• Gunakan font sans-serif modern&#10;• Sertakan logo produk di pojok kanan atas&#10;• Warna pastel, hindari kontras tinggi"
                            />
                            <flux:error name="form.visual_brief" />
                        </flux:field>
                    </div>
                </div>

                <div x-show="tab === 'video'" x-cloak>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="size-2 rounded-full bg-pink-500"></div>
                            <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Video Brief</h3>
                        </div>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 italic mb-3 ml-4">
                            Durasi, struktur scene, transisi, BGM
                        </p>
                        <flux:field>
                            <flux:textarea
                                wire:model="form.video_brief"
                                rows="5"
                                placeholder="• Video 30 detik, format vertikal 9:16&#10;• Hook 3 detik pertama dengan visual produk&#10;• Demo produk 15 detik dari berbagai angle&#10;• Tutup dengan CTA 5 detik + logo"
                            />
                            <flux:error name="form.video_brief" />
                        </flux:field>
                    </div>
                </div>

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
                                    <div class="mt-2 flex items-center gap-2 text-sm text-zinc-500">
                                        <flux:icon.paper-clip class="size-4" />
                                        <span>{{ \Illuminate\Support\Str::after($existingFinalAsset, '/') }}</span>
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
                                    <div class="mt-2 flex items-center gap-2 text-sm text-zinc-500">
                                        <flux:icon.paper-clip class="size-4" />
                                        <span>{{ \Illuminate\Support\Str::after($existingThumbnail, '/') }}</span>
                                    </div>
                                @endif
                                <div wire:loading wire:target="thumbnail" class="mt-2 text-sm text-pink-600">Uploading...</div>
                            </flux:field>
                        </div>
                    </div>
                </div>

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
