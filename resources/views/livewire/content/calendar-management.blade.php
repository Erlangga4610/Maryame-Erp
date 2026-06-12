<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <flux:button variant="outline" wire:click="previousMonth" icon="chevron-left" />
            <h2 class="text-xl font-bold text-zinc-800 dark:text-white">
                {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
            </h2>
            <flux:button variant="outline" wire:click="nextMonth" icon="chevron-right" />
            <flux:button variant="ghost" wire:click="goToToday" size="sm">Hari Ini</flux:button>
        </div>

        {{-- Calendar status & actions --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:badge size="sm" :color="match($calendar->status) {
                    'draft' => 'zinc',
                    'in_review' => 'amber',
                    'approved' => 'green',
                    'distributed' => 'purple',
                    'archived' => 'zinc',
                    default => 'zinc',
                }">{{ ucfirst(str_replace('_', ' ', $calendar->status)) }}</flux:badge>
            </div>
            <div class="flex items-center gap-2">
                @if($calendar->status === 'draft' && (Auth::user()->isSuperAdmin() || Auth::user()->hasRole('CSP')))
                    <flux:button wire:click="submitForReview" size="sm" variant="primary">
                        Submit for Review
                    </flux:button>
                @endif
                @if($calendar->status === 'in_review' && (Auth::user()->isSuperAdmin() || Auth::user()->hasRole('MC_BM')))
                    <flux:button wire:click="approveCalendar" size="sm" variant="primary" color="green">
                        Approve
                    </flux:button>
                @endif
                @if($calendar->status === 'approved' && (Auth::user()->isSuperAdmin() || Auth::user()->hasRole('CSP')))
                    <flux:button wire:click="distributeCalendar" size="sm" variant="primary" color="purple">
                        Distribute
                    </flux:button>
                @endif
                @if($calendar->status === 'distributed' && (Auth::user()->isSuperAdmin() || Auth::user()->hasRole('CSP')))
                    <flux:button wire:click="archiveCalendar" size="sm" variant="outline">
                        Archive
                    </flux:button>
                @endif
            </div>
        </div>
    </div>

    {{-- Status summary --}}
    <div class="flex flex-wrap gap-2">
        @foreach($entryStatuses as $status)
            @php $count = $calendar->entries->filter(fn($e) => $e->status?->value === $status->value)->count(); @endphp
            <div class="text-xs px-2 py-1 rounded-full {{ match($status->value) {
                'draft' => 'bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300',
                'in_review' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                'distributed' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                'archived' => 'bg-zinc-100 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500',
                default => 'bg-zinc-100 dark:bg-zinc-700',
            } }}">
                {{ $status->label() }}: {{ $count }}
            </div>
        @endforeach
    </div>

    {{-- Calendar Grid --}}
    <div class="grid grid-cols-7 gap-px bg-zinc-200 dark:bg-zinc-700 rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
            <div class="bg-zinc-50 dark:bg-zinc-800/80 p-2 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                {{ $day }}
            </div>
        @endforeach

        @foreach($days as $day)
            @php
                $dateKey = $day->format('Y-m-d');
                $dayEntries = $entriesByDate->get($dateKey, collect());
                $isCurrentMonth = $day->month == $currentMonth;
                $isToday = $day->isToday();
            @endphp

            <div
                class="min-h-[180px] p-2 bg-white dark:bg-zinc-800 {{ $isCurrentMonth ? '' : 'bg-zinc-50 dark:bg-zinc-800/40' }}"
                wire:key="day-{{ $dateKey }}"
            >
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs {{ $isToday ? 'font-bold text-pink-600' : ($isCurrentMonth ? 'text-zinc-800 dark:text-white' : 'text-zinc-400 dark:text-zinc-600') }}">
                        {{ $day->format('j') }}
                    </span>
                    @if($isCurrentMonth)
                        <button
                            type="button"
                            wire:click="openAddPanel('{{ $dateKey }}')"
                            class="text-xs text-pink-500 hover:text-pink-700 dark:hover:text-pink-300"
                        >
                            + Tambah
                        </button>
                    @endif
                </div>

                <div class="space-y-1">
                    @foreach($dayEntries as $entry)
                        @php $content = $entry->content; @endphp
                        <div
                            class="text-[11px] rounded px-1.5 py-1 cursor-pointer hover:shadow-sm transition-shadow border-l-2 {{ match($entry->status->value) {
                                'draft' => 'border-zinc-300 dark:border-zinc-500 bg-zinc-50 dark:bg-zinc-700',
                                'in_review' => 'border-amber-400 bg-amber-50 dark:bg-amber-900/20',
                                'approved' => 'border-green-400 bg-green-50 dark:bg-green-900/20',
                                'distributed' => 'border-purple-400 bg-purple-50 dark:bg-purple-900/20',
                                'archived' => 'border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700/50 opacity-60',
                                default => 'border-zinc-300 dark:border-zinc-500',
                            } }}"
                            x-data="{ open: false }"
                            @click.outside="open = false"
                        >
                            <div class="flex items-center justify-between gap-1" @click="open = !open">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300 truncate">
                                    {{ $content?->platform?->code ?? '-' }}: {{ \Illuminate\Support\Str::limit($content?->theme ?? '-', 15) }}
                                </span>
                            </div>

                            {{-- Dropdown actions --}}
                            <div x-show="open" class="mt-1 pt-1 border-t border-zinc-200 dark:border-zinc-600 space-y-0.5">
                                @foreach($entryStatuses as $s)
                                    @if($s->value !== $entry->status->value)
                                        <button
                                            type="button"
                                            wire:click="updateStatus({{ $entry->id }}, '{{ $s->value }}')"
                                            class="block w-full text-left text-[10px] px-1 py-0.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-600 text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ $s->label() }}
                                        </button>
                                    @endif
                                @endforeach
                                <button
                                    type="button"
                                    wire:click="removeEntry({{ $entry->id }})"
                                    class="block w-full text-left text-[10px] px-1 py-0.5 rounded hover:bg-red-100 dark:hover:bg-red-900/30 text-red-500"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Add Content Panel --}}
    @if($showAddPanel)
        <flux:modal wire:model="showAddPanel" class="w-md" wire:key="add-content-panel">
            <div class="p-6 space-y-4">
                <h3 class="text-lg font-semibold">Tambah Konten ke {{ $selectedDate }}</h3>

                <div class="flex gap-2">
                    <flux:input
                        placeholder="Cari tema atau kode..."
                        wire:model.live.debounce="searchContent"
                        class="flex-1"
                    />
                    <flux:select wire:model.live="selectedPlatform" class="w-32">
                        <option value="">Semua</option>
                        @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="max-h-60 overflow-y-auto space-y-1">
                    @forelse($searchResults as $item)
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700">
                            <div>
                                <span class="text-xs font-mono text-zinc-500">{{ $item['content_code'] }}</span>
                                <p class="text-sm font-medium">{{ $item['theme'] }}</p>
                            </div>
                            <flux:button size="xs" variant="primary" color="pink" wire:click="addContent({{ $item['id'] }})">
                                Tambah
                            </flux:button>
                        </div>
                    @empty
                        <div class="text-sm text-zinc-400 text-center py-4 space-y-2">
                            @if(empty($searchResults) && !$searchContent && !$selectedPlatform)
                                <p>Menampilkan konten terbaru. Ketik untuk filter...</p>
                            @elseif(empty($searchResults))
                                <p>Tidak ada hasil.</p>
                                <a href="/contents" class="text-pink-500 hover:text-pink-700 underline text-xs">
                                    Buat konten dulu di Content Calendar →
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>

                <div class="flex justify-end">
                    <flux:button variant="outline" wire:click="closeAddPanel">Tutup</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
