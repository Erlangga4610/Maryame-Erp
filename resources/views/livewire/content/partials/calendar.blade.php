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
