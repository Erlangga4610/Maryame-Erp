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
