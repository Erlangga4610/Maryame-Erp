<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <flux:heading size="lg">My Tasks</flux:heading>
    </div>

    {{-- Status summary --}}
    <div class="flex flex-wrap gap-2">
        @php $allStatuses = ['draft' => 'Draft', 'in_production' => 'In Production', 'ready_review' => 'Ready for Review', 'approved' => 'Approved', 'scheduled' => 'Scheduled', 'published' => 'Published']; @endphp
        <button wire:click="$set('filterStatus', '')"
            class="text-xs px-3 py-1.5 rounded-full transition-colors {{ !$filterStatus ? 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 font-semibold' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600' }}">
            Semua ({{ array_sum($statusCounts->toArray() ?: [0]) }})
        </button>
        @foreach($allStatuses as $val => $label)
            @php $count = $statusCounts->get($val, 0); @endphp
            <button wire:click="$set('filterStatus', '{{ $val }}')"
                class="text-xs px-3 py-1.5 rounded-full transition-colors {{ $filterStatus === $val ? 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 font-semibold' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600' }}">
                {{ $label }} ({{ $count }})
            </button>
        @endforeach
    </div>

    {{-- Search --}}
    <div>
        <flux:input
            placeholder="Cari kode/tema..."
            wire:model.live.debounce="search"
        />
    </div>

    {{-- Task list --}}
    <div class="space-y-2">
        @forelse($tasks as $content)
            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-mono font-medium text-zinc-500 dark:text-zinc-400">{{ $content->content_code }}</span>
                            <flux:badge size="sm" :color="match($content->status->value) {
                                'draft' => 'zinc',
                                'in_production' => 'blue',
                                'ready_review' => 'amber',
                                'approved' => 'green',
                                'scheduled' => 'purple',
                                'published' => 'emerald',
                                default => 'zinc',
                            }">{{ $content->status->label() }}</flux:badge>
                            <flux:badge size="sm" :color="match($content->priority->value) {
                                'high' => 'red',
                                'medium' => 'amber',
                                'low' => 'zinc',
                                default => 'zinc',
                            }">{{ ucfirst($content->priority->value) }}</flux:badge>
                        </div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-white truncate">{{ $content->theme }}</p>
                        <div class="flex items-center gap-3 mt-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                            <span>{{ $content->platform->name }}</span>
                            @if($content->deadline_produksi)
                                <span>Deadline: {{ $content->deadline_produksi->format('d M') }}</span>
                            @endif
                            @if($content->publish_date)
                                <span>Publish: {{ $content->publish_date->format('d M') }}</span>
                            @endif
                            <span>v{{ $content->version }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1.5 text-xs text-zinc-500">
                            @php
                                $myRole = match(true) {
                                    $content->pic_copy_id === auth()->id() => 'Copy',
                                    $content->pic_visual_id === auth()->id() => 'Visual',
                                    $content->pic_video_id === auth()->id() => 'Video',
                                    default => null,
                                };
                            @endphp
                            @if($myRole)
                                <span class="font-medium text-pink-600 dark:text-pink-400">Role: {{ $myRole }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if($content->status->value === 'draft')
                            <flux:button size="xs" variant="primary" color="blue" wire:click="startProduction({{ $content->id }})">
                                Mulai Produksi
                            </flux:button>
                            <a href="/contents">
                                <flux:button size="xs" variant="outline">
                                    Buka
                                </flux:button>
                            </a>
                        @endif

                        @if($content->status->value === 'in_production')
                            <flux:button size="xs" variant="primary" color="pink" wire:click="submitForReview({{ $content->id }})">
                                Submit for Review
                            </flux:button>
                            <a href="/contents">
                                <flux:button size="xs" variant="outline">
                                    Buka
                                </flux:button>
                            </a>
                        @endif

                        @if(in_array($content->status->value, ['ready_review', 'approved', 'scheduled', 'published']))
                            <a href="/contents">
                                <flux:button size="xs" variant="outline">
                                    Buka
                                </flux:button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-zinc-400 dark:text-zinc-500">
                <p class="text-sm">Tidak ada task untuk Anda.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
</div>
