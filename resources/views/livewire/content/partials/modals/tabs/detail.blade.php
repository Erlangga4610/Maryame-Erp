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
                <option value="rutin">Rutin</option>
                <option value="campaign">Campaign</option>
                <option value="spontan">Spontan</option>
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
