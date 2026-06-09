<div>
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

    {{-- KANBAN VIEW --}}
    <div wire:key="kanban-board" x-show="$wire.viewMode === 'kanban'" class="pb-2"
         x-on:kanban-move.window="$wire.moveToColumn($event.detail.id, $event.detail.column)"
    >
        <div class="grid grid-cols-4 gap-3">
            @foreach($kanbanColumns as $key => $column)
                <div class="flex flex-col rounded-xl bg-zinc-100 dark:bg-zinc-800/50">
                    <div class="flex items-center justify-between px-3 pt-3 pb-2">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ $column['label'] }}</h3>
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $column['items']->count() }}</span>
                        </div>
                    </div>

                    <div
                        data-column="{{ $key }}"
                        class="flex flex-col gap-1.5 px-2 pb-2 min-h-[180px]"
                    >
                        @forelse($column['items'] as $content)
                            <div
                                data-content-id="{{ $content->id }}"
                                class="rounded-lg bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 p-2.5 shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow text-[12px]"
                                wire:key="kanban-{{ $content->id }}"
                            >
                                <div class="flex items-start justify-between gap-1.5 mb-1.5">
                                    <span class="font-mono font-medium text-zinc-500 dark:text-zinc-400">{{ $content->content_code }}</span>
                                    <flux:badge size="sm" :color="$content->priority_badge_color">
                                        {{ ucfirst($content->priority->value) }}
                                    </flux:badge>
                                </div>

                                <p class="font-medium text-zinc-800 dark:text-white mb-1.5 line-clamp-2">{{ $content->theme }}</p>

                                <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400">
                                    <span>{{ $content->platform->code }}</span>
                                    <span class="truncate">{{ $content->picCopy?->name ?? '-' }}</span>
                                </div>

                                @if($content->publish_date)
                                    <div class="mt-1.5 text-zinc-500 dark:text-zinc-400">
                                        {{ $content->publish_date->format('d M') }}
                                    </div>
                                @endif

                                @if($canCreate && $content->status->value === 'draft')
                                    <div class="mt-1.5 flex justify-end">
                                        <button
                                            type="button"
                                            wire:click="confirmDelete({{ $content->id }})"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:underline transition-colors"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="flex-1 flex items-center justify-center">
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Tidak ada konten</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- TABLE VIEW --}}
    <div x-show="$wire.viewMode === 'table'" class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Kode</flux:table.column>
                <flux:table.column>Platform</flux:table.column>
                <flux:table.column>Tema</flux:table.column>
                <flux:table.column>Publish Date</flux:table.column>
                <flux:table.column>Priority</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($contents as $content)
                    <flux:table.row wire:key="content-{{ $content->id }}">
                        <flux:table.cell class="font-mono text-xs">
                            {{ $content->content_code }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge :color="$content->platform->code === 'TKM' ? 'purple' : 'blue'">
                                {{ $content->platform->name }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell class="max-w-xs truncate">
                            {{ $content->theme }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $content->full_publish_date ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge :color="$content->priority_badge_color">
                                {{ ucfirst($content->priority->value) }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge :color="$content->status->color()">
                                {{ $content->status->label() }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="outline" wire:click="openEditModal({{ $content->id }})">
                                    Edit
                                </flux:button>

                                @if($canCreate && $content->status->value === 'draft')
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        wire:click="confirmDelete({{ $content->id }})"
                                    >
                                        Hapus
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-gray-500">
                            Tidak ada data konten
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $contents->links() }}
        </div>
    </div>

    {{-- CALENDAR VIEW --}}
    <div x-show="$wire.viewMode === 'calendar'" class="flex gap-4">
        {{-- Left: Unscheduled Panel --}}
        <div class="w-72 shrink-0 flex flex-col">
            <div class="mb-3">
                <h3 class="text-sm font-semibold text-zinc-800 dark:text-white">Unscheduled Work</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Drag each work item onto the calendar to set a due date for the work.
                </p>
            </div>

            <flux:input
                placeholder="Search unscheduled items"
                wire:model.live.debounce="unscheduledSearch"
            />

            <div class="flex flex-wrap gap-1 mt-2">
                <flux:select
                    placeholder="Assignee"
                    wire:model.live="unscheduledAssignee"
                    class="min-w-0 flex-1 text-xs"
                >
                    <option value="">Semua</option>
                    @foreach($assignees as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select
                    placeholder="Type"
                    wire:model.live="unscheduledType"
                    class="min-w-0 flex-1 text-xs"
                >
                    <option value="">Semua</option>
                    @foreach($contentTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                <flux:select
                    placeholder="Status"
                    wire:model.live="unscheduledStatus"
                    class="min-w-0 flex-1 text-xs"
                >
                    <option value="">Semua</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                <flux:button size="sm" variant="ghost" wire:click="$toggle('showUnscheduledFilters')" class="text-xs" icon="funnel">
                    Filters
                </flux:button>
            </div>

            @if($showUnscheduledFilters)
                <div class="mt-2 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 space-y-2">
                    <flux:select label="Priority" wire:model.live="selectedPriority" placeholder="Semua">
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </flux:select>
                    <flux:select label="Platform" wire:model.live="selectedPlatform" placeholder="Semua">
                        @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
            @endif

            <div class="flex items-center gap-2 mt-3 mb-2">
                <flux:icon.arrow-path class="size-3.5 text-zinc-400" />
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Most recent</span>
            </div>

            <div class="flex-1 overflow-y-auto space-y-1.5 min-h-0">
                @forelse($unscheduledItems as $item)
                    <div
                        draggable="true"
                        data-content-id="{{ $item->id }}"
                        class="rounded-lg bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 p-2.5 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow select-none"
                        @dragstart="event.dataTransfer.setData('text/plain', {{ $item->id }})"
                        x-data
                    >
                        <div class="flex items-start justify-between gap-1.5 mb-1">
                            <span class="text-[11px] font-mono font-medium text-zinc-500 dark:text-zinc-400">{{ $item->content_code }}</span>
                            <flux:badge size="sm" :color="$item->priority_badge_color">
                                {{ ucfirst($item->priority->value) }}
                            </flux:badge>
                        </div>
                        <p class="text-xs font-medium text-zinc-800 dark:text-white line-clamp-2">{{ $item->theme }}</p>
                        <div class="flex items-center gap-2 mt-1.5 text-[11px] text-zinc-500 dark:text-zinc-400">
                            <span>{{ $item->platform->code }}</span>
                            <span>{{ $item->picCopy?->name ?? '-' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 italic text-center py-8">
                        Tidak ada item yang belum dijadwalkan
                    </p>
                @endforelse
            </div>
        </div>

        {{-- Right: Calendar Grid --}}
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-center mb-3">
                <flux:button variant="outline" wire:click="previousMonth">
                    <flux:icon.chevron-left class="w-4 h-4" />
                </flux:button>

                <h2 class="text-base font-semibold text-zinc-800 dark:text-white">
                    {{ Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
                </h2>

                <flux:button variant="outline" wire:click="nextMonth">
                    <flux:icon.chevron-right class="w-4 h-4" />
                </flux:button>
            </div>

            <div class="grid grid-cols-7 gap-px bg-zinc-200 dark:bg-zinc-700 rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
                @php
                    $firstDay = Carbon\Carbon::create($currentYear, $currentMonth, 1);
                    $lastDay = $firstDay->copy()->endOfMonth();
                    $startOfWeek = $firstDay->copy()->startOfWeek();
                    $endOfWeek = $lastDay->copy()->endOfWeek();
                    $days = [];
                    $currentDay = $startOfWeek;

                    while ($currentDay <= $endOfWeek) {
                        $days[] = $currentDay->copy();
                        $currentDay->addDay();
                    }
                @endphp

                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                    <div class="bg-zinc-50 dark:bg-zinc-800/80 p-1.5 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                        {{ $day }}
                    </div>
                @endforeach

                @foreach($days as $day)
                    @php
                        $dayContents = $contents->filter(function($content) use ($day) {
                            return $content->publish_date && $content->publish_date->isSameDay($day);
                        });
                        $isCurrentMonth = $day->month == $currentMonth;
                        $isToday = $day->isToday();
                    @endphp

                    <div
                        class="min-h-[100px] p-1.5 bg-white dark:bg-zinc-800 {{ $isCurrentMonth ? '' : 'bg-zinc-50 dark:bg-zinc-800/40' }}"
                        @dragover.prevent
                        @drop.prevent="
                            const id = event.dataTransfer.getData('text/plain');
                            if (id) $wire.setPublishDate(id, '{{ $day->format('Y-m-d') }}');
                        "
                        x-data
                    >
                        <div class="text-right text-xs mb-1 {{ $isToday ? 'font-bold text-pink-600' : ($isCurrentMonth ? 'text-zinc-800 dark:text-white' : 'text-zinc-400 dark:text-zinc-600') }}">
                            {{ $day->format('j') }}
                        </div>

                        <div class="space-y-0.5">
                            @foreach($dayContents as $content)
                                <div
                                    class="text-[11px] px-1.5 py-0.5 rounded cursor-pointer truncate {{ match($content->status->value) {
                                        'draft' => 'bg-zinc-200 dark:bg-zinc-600 text-zinc-700 dark:text-zinc-300',
                                        'in_production' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
                                        'ready_review' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
                                        'approved' => 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300',
                                        'scheduled' => 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300',
                                        'published' => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300',
                                        default => 'bg-zinc-100 dark:bg-zinc-700'
                                    } }}"
                                    wire:click="openEditModal({{ $content->id }})"
                                >
                                    {{ $content->platform->code }}: {{ Str::limit($content->theme, 18) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- CREATE/EDIT MODAL --}}
    @if($showModal)
        <flux:modal size="lg" wire:model="showModal">
            <div class="p-6">
                <h2 class="text-lg font-bold mb-4">
                    {{ $modalMode === 'create' ? 'Buat Konten Baru' : 'Edit Konten' }}
                </h2>

                <form wire:submit="save">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:select
                            label="Platform *"
                            wire:model="form.platform_id"
                            required
                            placeholder="Pilih platform"
                        >
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select
                            label="Priority *"
                            wire:model="form.priority"
                            required
                            placeholder="Pilih priority"
                        >
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </flux:select>

                        <div class="col-span-2">
                            <flux:input
                                label="Tema *"
                                wire:model="form.theme"
                                placeholder="Masukkan tema konten..."
                                required
                            />
                        </div>

                        <div class="col-span-2">
                            <flux:textarea
                                label="Caption"
                                wire:model="form.caption"
                                rows="3"
                                placeholder="Caption konten..."
                            />
                        </div>

                        <flux:input
                            type="date"
                            label="Tanggal Publish"
                            wire:model="form.publish_date"
                        />

                        <flux:input
                            type="date"
                            label="Deadline Produksi"
                            wire:model="form.deadline_produksi"
                        />

                        <flux:select
                            label="Product"
                            wire:model="form.product_id"
                            placeholder="Pilih product (opsional)"
                        >
                            <option value="">Pilih product (opsional)</option>
                            @foreach(\App\Models\Product::pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select
                            label="Campaign"
                            wire:model="form.campaign_id"
                            placeholder="Pilih campaign (opsional)"
                        >
                            <option value="">Pilih campaign (opsional)</option>
                            @foreach(\App\Models\Campaign::pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <flux:button type="button" variant="outline" wire:click="$set('showModal', false)">
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

    <flux:modal name="delete-modal" wire:model="showDeleteModal" class="w-md">
        <div class="p-6 text-center space-y-4">
            <div class="mx-auto size-12 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center">
                <flux:icon.exclamation-triangle class="size-6 text-red-600 dark:text-red-400" />
            </div>

            <div>
                <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Hapus Konten</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Apakah Anda yakin ingin menghapus konten ini?<br>
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="flex justify-center gap-3">
                <flux:button type="button" variant="outline" wire:click="cancelDelete">
                    Batal
                </flux:button>
                <flux:button type="button" variant="danger" wire:click="executeDelete">
                    Ya, Hapus
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
