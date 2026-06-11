<div class="mb-6">
    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center gap-6 -mb-px">
            <button
                type="button"
                wire:click="$set('viewMode', 'kanban')"
                class="flex items-center gap-1.5 px-1 py-3 text-sm font-medium border-b-2 transition-all duration-150
                    {{ $viewMode === 'kanban'
                        ? 'border-pink-600 text-pink-600 dark:border-pink-400 dark:text-pink-400'
                        : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-600' }}"
            >
                <flux:icon.view-columns class="size-4" />
                Kanban
            </button>

            <button
                type="button"
                wire:click="$set('viewMode', 'table')"
                class="flex items-center gap-1.5 px-1 py-3 text-sm font-medium border-b-2 transition-all duration-150
                    {{ $viewMode === 'table'
                        ? 'border-pink-600 text-pink-600 dark:border-pink-400 dark:text-pink-400'
                        : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-600' }}"
            >
                <flux:icon.list-bullet class="size-4" />
                Table
            </button>

            <button
                type="button"
                wire:click="$set('viewMode', 'calendar')"
                class="flex items-center gap-1.5 px-1 py-3 text-sm font-medium border-b-2 transition-all duration-150
                    {{ $viewMode === 'calendar'
                        ? 'border-pink-600 text-pink-600 dark:border-pink-400 dark:text-pink-400'
                        : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-600' }}"
            >
                <flux:icon.calendar class="size-4" />
                Calendar
            </button>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                wire:click="$toggle('showFilters')"
                class="flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors duration-150"
            >
                <flux:icon.funnel class="size-3.5" />
                Filters
            </button>

            @if($canCreate)
                <flux:button size="sm" variant="primary" color="pink" wire:click="openCreateModal">
                    <flux:icon.plus class="size-3.5 mr-1" />
                    New
                </flux:button>
            @endif
        </div>
    </div>

    <div class="mt-4" x-show="$wire.showFilters">
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
</div>
