<div class="flex justify-between items-center mb-6">
    <div class="flex gap-4">
        <flux:button.group>
            <flux:button
                :variant="$viewMode === 'kanban' ? 'primary' : 'outline'"
                wire:click="$set('viewMode', 'kanban')"
            >
                <flux:icon.view-columns class="w-4 h-4 mr-1" />
                Kanban
            </flux:button>
            <flux:button
                :variant="$viewMode === 'table' ? 'primary' : 'outline'"
                wire:click="$set('viewMode', 'table')"
            >
                <flux:icon.list-bullet class="w-4 h-4 mr-1" />
                Table
            </flux:button>
            <flux:button
                :variant="$viewMode === 'calendar' ? 'primary' : 'outline'"
                wire:click="$set('viewMode', 'calendar')"
            >
                <flux:icon.calendar class="w-4 h-4 mr-1" />
                Calendar
            </flux:button>
        </flux:button.group>

        <flux:button variant="outline" wire:click="$toggle('showFilters')">
            <flux:icon.funnel class="w-4 h-4 mr-1" />
            Filters
        </flux:button>
    </div>

    @if($canCreate)
        <flux:button variant="primary" color="pink" wire:click="openCreateModal">
            <flux:icon.plus class="w-4 h-4 mr-1" />
            New
        </flux:button>
    @endif
</div>

<div class="mb-6" x-show="$wire.showFilters">
    <flux:card class="!p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <flux:input
                label="Search"
                placeholder="Cari kode/tema..."
                wire:model.live.debounce="search"
            />

            <flux:select
                label="Platform"
                wire:model.live="selectedPlatform"
                placeholder="Semua Platform"
            >
                @foreach($platforms as $platform)
                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                @endforeach
            </flux:select>

            <flux:select
                label="Status"
                wire:model.live="selectedStatus"
                placeholder="Semua Status"
            >
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </flux:select>

            <flux:select
                label="Priority"
                wire:model.live="selectedPriority"
                placeholder="Semua Priority"
            >
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </flux:select>
        </div>

        <div class="mt-3 flex justify-end">
            <flux:button size="sm" wire:click="resetFilters">
                Reset Filters
            </flux:button>
        </div>
    </flux:card>
</div>
