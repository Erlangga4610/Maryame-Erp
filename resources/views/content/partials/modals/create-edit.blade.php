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
            </div>

            <form wire:submit="save">
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
                    <flux:field>
                        <flux:label>Copy Brief</flux:label>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Tuliskan arahan copywriting, tone of voice, call to action, dan poin-poin penting yang harus disampaikan.</p>
                        <flux:textarea
                            wire:model="form.copy_brief"
                            rows="8"
                            placeholder="Contoh: Gunakan tone casual dan engaging. Highlight benefit produk di paragraf pertama. Sertakan CTA untuk klik link di bio."
                        />
                        <flux:error name="form.copy_brief" />
                    </flux:field>
                </div>

                <div x-show="tab === 'visual'" x-cloak>
                    <flux:field>
                        <flux:label>Visual Brief</flux:label>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Jelaskan konsep visual, referensi gaya desain, warna, layout, dan elemen grafis yang diinginkan.</p>
                        <flux:textarea
                            wire:model="form.visual_brief"
                            rows="8"
                            placeholder="Contoh: Flat lay style dengan background putih. Gunakan font sans-serif. Sertakan logo produk di pojok kanan atas."
                        />
                        <flux:error name="form.visual_brief" />
                    </flux:field>
                </div>

                <div x-show="tab === 'video'" x-cloak>
                    <flux:field>
                        <flux:label>Video Brief</flux:label>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Deskripsikan durasi, struktur scene, transisi, background music, dan referensi video yang diinginkan.</p>
                        <flux:textarea
                            wire:model="form.video_brief"
                            rows="8"
                            placeholder="Contoh: Video 30 detik. Buka dengan hook 3 detik, lalu demo produk 15 detik, tutup dengan CTA 5 detik. Gunakan BGM upbeat."
                        />
                        <flux:error name="form.video_brief" />
                    </flux:field>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
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
