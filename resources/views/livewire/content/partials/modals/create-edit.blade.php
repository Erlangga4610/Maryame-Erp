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
                            <flux:label>Content Group</flux:label>
                            <flux:select wire:model="form.content_group_id" placeholder="Pilih group (opsional)">
                                <option value="">Pilih group (opsional)</option>
                                @foreach($contentGroups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                                <option value="__create__">+ Buat Group Baru...</option>
                            </flux:select>
                            <flux:error name="form.content_group_id" />
                            <div x-show="$wire.form.content_group_id === '__create__'" x-cloak class="mt-2">
                                <flux:input wire:model="newContentGroupName" placeholder="Nama group baru..." />
                                <flux:error name="newContentGroupName" />
                            </div>
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

                        <flux:field>
                            <flux:label>Est. Jam Copy</flux:label>
                            <flux:input type="number" step="0.5" wire:model="form.est_copy_hours" placeholder="0" />
                            <flux:error name="form.est_copy_hours" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Est. Jam Visual</flux:label>
                            <flux:input type="number" step="0.5" wire:model="form.est_visual_hours" placeholder="0" />
                            <flux:error name="form.est_visual_hours" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Est. Jam Video</flux:label>
                            <flux:input type="number" step="0.5" wire:model="form.est_video_hours" placeholder="0" />
                            <flux:error name="form.est_video_hours" />
                        </flux:field>
                    </div>
                </div>

                <div x-show="tab === 'strategic'" x-cloak>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="size-2 rounded-full bg-pink-500"></div>
                            <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Strategic Brief</h3>
                            <span class="text-[11px] text-zinc-400 dark:text-zinc-500 italic">(CSP only)</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <flux:field>
                                    <flux:label>Angle</flux:label>
                                     <flux:input wire:model="form.angle" placeholder="Angle konten..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
                                    @if(!$isCsp || $isBriefFinal)<flux:error name="form.angle" />@endif
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label>Positioning</flux:label>
                                <flux:input wire:model="form.positioning" placeholder="Posisi brand..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Target Audience</flux:label>
                                <flux:input wire:model="form.target_audience" placeholder="Target audiens..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
                            </flux:field>

                            <div class="col-span-2">
                                <flux:field>
                                    <flux:label>Key Message</flux:label>
                                    <flux:textarea wire:model="form.key_message" rows="2" placeholder="Pesan utama..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label>Tone</flux:label>
                                <flux:input wire:model="form.tone" placeholder="Tone of voice..." :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))" />
                            </flux:field>
                        </div>

                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="size-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
                                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Copy Direction</h4>
                            </div>
                            <flux:field>
                                <flux:textarea
                                    wire:model="form.copy_brief"
                                    rows="4"
                                    placeholder="• Tone profesional namun santai&#10;• Fokus pada promo diskon 50%&#10;• Sertakan CTA &quot;Beli Sekarang&quot;&#10;• Highlight benefit produk di paragraf pertama"
                                    :disabled="(!$isSuperAdmin && (!$isCsp || $isBriefFinal))"
                                />
                            </flux:field>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'technical'" x-cloak>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="size-2 rounded-full bg-pink-500"></div>
                            <h3 class="text-base font-semibold text-zinc-800 dark:text-white">Technical Brief</h3>
                            <span class="text-[11px] text-zinc-400 dark:text-zinc-500 italic">(SMS only)</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Aspect Ratio</flux:label>
                                <flux:select wire:model="form.aspect_ratio" :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))">
                                    <option value="">Pilih ratio</option>
                                    <option value="1:1">1:1 (Square)</option>
                                    <option value="4:5">4:5 (Portrait)</option>
                                    <option value="9:16">9:16 (Story/Reels)</option>
                                    <option value="16:9">16:9 (Landscape)</option>
                                </flux:select>
                            </flux:field>

                            <flux:field>
                                <flux:label>Resolution</flux:label>
                                <flux:select wire:model="form.resolution" :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))">
                                    <option value="">Pilih resolusi</option>
                                    <option value="720p">720p</option>
                                    <option value="1080p">1080p (Full HD)</option>
                                    <option value="4k">4K</option>
                                </flux:select>
                            </flux:field>

                            <flux:field>
                                <flux:label>Durasi</flux:label>
                                <flux:input wire:model="form.duration" placeholder="Contoh: 30 detik" :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Format File</flux:label>
                                <flux:select wire:model="form.format_file" :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))">
                                    <option value="">Pilih format</option>
                                    <option value="mp4">MP4</option>
                                    <option value="jpg">JPG</option>
                                    <option value="png">PNG</option>
                                    <option value="gif">GIF</option>
                                </flux:select>
                            </flux:field>

                            <div class="col-span-2">
                                <flux:field>
                                    <flux:label>Hashtag</flux:label>
                                    <flux:input wire:model="form.hashtag" placeholder="#maryame #skincare" :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))" />
                                </flux:field>
                            </div>

                            <div class="col-span-2">
                                <flux:field>
                                    <flux:label>Audio Guidance</flux:label>
                                    <flux:textarea wire:model="form.audio_guidance" rows="2" placeholder="Referensi musik, BGM, voice over..." :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))" />
                                </flux:field>
                            </div>

                            <div class="col-span-2">
                                <flux:field>
                                    <flux:label>Originality Instruction</flux:label>
                                    <flux:textarea wire:model="form.originality_instruction" rows="2" placeholder="Instruksi originality untuk platform..." :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))" />
                                </flux:field>
                            </div>

                            <div class="col-span-2">
                                <flux:field>
                                    <flux:label>Thumbnail Note</flux:label>
                                    <flux:textarea wire:model="form.thumbnail_note" rows="2" placeholder="Catatan untuk thumbnail..." :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))" />
                                </flux:field>
                            </div>
                        </div>

                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="size-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
                                    <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Visual Direction</h4>
                                </div>
                                <flux:field>
                                    <flux:textarea
                                        wire:model="form.visual_brief"
                                        rows="4"
                                        placeholder="• Flat lay style dengan background putih&#10;• Gunakan font sans-serif modern&#10;• Sertakan logo produk di pojok kanan atas&#10;• Warna pastel, hindari kontras tinggi"
                                        :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))"
                                    />
                                </flux:field>
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="size-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
                                    <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Video Direction</h4>
                                </div>
                                <flux:field>
                                    <flux:textarea
                                        wire:model="form.video_brief"
                                        rows="4"
                                        placeholder="• Video 30 detik, format vertikal 9:16&#10;• Hook 3 detik pertama dengan visual produk&#10;• Demo produk 15 detik dari berbagai angle&#10;• Tutup dengan CTA 5 detik + logo"
                                        :disabled="(!$isSuperAdmin && (!$isSms || $isBriefFinal))"
                                    />
                                </flux:field>
                            </div>
                        </div>
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
                                    <div class="mt-2">
                                        @php
                                            $ext = strtolower(pathinfo($existingFinalAsset, PATHINFO_EXTENSION));
                                            $url = \Illuminate\Support\Facades\Storage::url($existingFinalAsset);
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
                                    @php $thumbUrl = \Illuminate\Support\Facades\Storage::url($existingThumbnail); @endphp
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

                @if($modalMode === 'edit' && $editingContentStatus !== 'draft')
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="size-2 rounded-full bg-amber-500"></div>
                            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Tipe Adjustment</h3>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-600 p-3 cursor-pointer has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 dark:has-[:checked]:bg-pink-900/20">
                                <input type="radio" wire:model="adjustmentType" value="minor" class="text-pink-600" />
                                <div>
                                    <div class="text-sm font-medium text-zinc-800 dark:text-white">Minor</div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Perubahan kecil, langsung diterapkan</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-600 p-3 cursor-pointer has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 dark:has-[:checked]:bg-pink-900/20">
                                <input type="radio" wire:model="adjustmentType" value="major" class="text-pink-600" />
                                <div>
                                    <div class="text-sm font-medium text-zinc-800 dark:text-white">Major</div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Perubahan besar, perlu approval CSP</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 rounded-lg border border-zinc-200 dark:border-zinc-600 p-3 cursor-pointer has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 dark:has-[:checked]:bg-pink-900/20">
                                <input type="radio" wire:model="adjustmentType" value="reactive" class="text-pink-600" />
                                <div>
                                    <div class="text-sm font-medium text-zinc-800 dark:text-white">Reactive</div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400">Darurat, langsung terapkan + konfirmasi</div>
                                </div>
                            </label>
                        </div>

                        <div x-show="$wire.adjustmentType !== 'minor'" x-cloak>
                            <flux:field>
                                <flux:label>Alasan Adjustment *</flux:label>
                                <flux:textarea wire:model="adjustmentReason" rows="2" placeholder="Jelaskan alasan perubahan..." />
                                <flux:error name="adjustmentReason" />
                            </flux:field>
                        </div>
                    </div>
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
