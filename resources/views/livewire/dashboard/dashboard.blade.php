<div class="space-y-6">
    {{-- 1. Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
        <flux:card class="dashboard-stat-card p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="size-7 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                    <flux:icon name="document-text" class="size-3.5 text-pink-500" />
                </div>
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Konten</span>
            </div>
            <p class="text-2xl font-bold text-zinc-800 dark:text-white">{{ $total }}</p>
            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">Bulan ini</p>
        </flux:card>

        @foreach($stats as $item)
            @php
                $color = $item['status']->color();
                $colorClass = match($color) {
                    'gray' => 'text-zinc-600 dark:text-zinc-300',
                    'blue' => 'text-blue-600 dark:text-blue-400',
                    'amber' => 'text-amber-600 dark:text-amber-400',
                    'green' => 'text-green-600 dark:text-green-400',
                    'purple' => 'text-purple-600 dark:text-purple-400',
                    'emerald' => 'text-emerald-600 dark:text-emerald-400',
                    default => 'text-zinc-600'
                };
            @endphp
            <flux:card class="dashboard-stat-card p-4">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $item['status']->label() }}</p>
                <p class="text-2xl font-bold mt-1 {{ $colorClass }}">{{ $item['count'] }}</p>
                <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">{{ $item['percentage'] }}%</p>
            </flux:card>
        @endforeach
    </div>

    {{-- 2. Middle Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Campaign Aktif --}}
        <flux:card class="p-5">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon name="megaphone" class="size-4 text-pink-500" />
                Campaign Aktif
            </h3>
            <div class="space-y-3">
                @forelse($campaigns as $campaign)
                    <div class="flex items-center gap-3 p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700/50">
                        <div class="size-10 rounded-lg bg-gradient-to-br from-pink-100 to-purple-100 dark:from-pink-900/20 dark:to-purple-900/20 flex items-center justify-center shrink-0">
                            <flux:icon name="chart-bar" class="size-5 text-pink-500" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-zinc-800 dark:text-white truncate">{{ $campaign->name }}</p>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">
                                {{ $campaign->start_date?->format('d M') }} – {{ $campaign->end_date?->format('d M') }}
                            </p>
                        </div>
                        <flux:badge size="sm" :color="$campaign->is_active ? 'green' : 'zinc'">
                            {{ $campaign->is_active ? 'Berjalan' : 'Perencanaan' }}
                        </flux:badge>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Belum ada campaign aktif</p>
                @endforelse
            </div>
        </flux:card>

        {{-- Kalender Minggu Ini --}}
        <flux:card class="p-5">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon name="calendar-days" class="size-4 text-pink-500" />
                Kalender Minggu Ini
            </h3>
            <div class="grid grid-cols-7 gap-1">
                @foreach($weekDates as $date)
                    @php
                        $key = $date->format('Y-m-d');
                        $dayContent = $weekContent->get($key, collect());
                        $isToday = $date->isToday();
                    @endphp
                    <div class="text-center">
                        <p class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500 mb-1">{{ $date->isoFormat('dd') }}</p>
                        <div class="mx-auto size-7 flex items-center justify-center rounded-full {{ $isToday ? 'bg-pink-500 text-white font-bold' : 'text-zinc-700 dark:text-zinc-300' }} text-xs">
                            {{ $date->format('d') }}
                        </div>
                        <div class="mt-1 space-y-0.5">
                            @foreach($dayContent as $content)
                                @php
                                    $code = $content->platform?->code ?? '';
                                    $isSelected = $content->id === $selectedContentId;
                                    $blockClass = match($code) {
                                        'TKM' => $isSelected ? 'bg-pink-200 dark:bg-pink-800/50 text-pink-800 dark:text-pink-200 ring-2 ring-pink-500' : 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300',
                                        'IG' => $isSelected ? 'bg-purple-200 dark:bg-purple-800/50 text-purple-800 dark:text-purple-200 ring-2 ring-purple-500' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                                        'SHOP' => $isSelected ? 'bg-emerald-200 dark:bg-emerald-800/50 text-emerald-800 dark:text-emerald-200 ring-2 ring-emerald-500' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                        default => $isSelected ? 'bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 ring-2 ring-pink-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'
                                    };
                                @endphp
                                <div class="text-[8px] leading-tight px-1 py-0.5 rounded cursor-pointer transition-all {{ $blockClass }}"
                                     x-on:click="$wire.selectContent({{ $content->id }})">
                                    {{ $content->platform?->code ?? '—' }} {{ $content->content_type?->label() ?? '' }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>

        {{-- Task Saya Hari Ini --}}
        <flux:card class="p-5">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon name="clipboard-document-list" class="size-4 text-pink-500" />
                Task Saya Hari Ini
            </h3>
            <div class="space-y-2">
                @forelse($myTasks as $task)
                    @php $isCompleted = in_array($task->id, $completedTasks); @endphp
                    <div class="flex items-start gap-2.5" x-data>
                        <div wire:click="toggleTask({{ $task->id }})"
                             class="mt-0.5 size-4 rounded border-2 shrink-0 cursor-pointer transition-all duration-150 flex items-center justify-center
                             {{ $isCompleted ? 'bg-pink-500 border-pink-500' : ($task->status === 'ready_review' ? 'border-amber-400' : 'border-zinc-300 dark:border-zinc-600') }}
                             hover:border-pink-400">
                            @if($isCompleted)
                                <flux:icon name="check" class="size-3 text-white" />
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs {{ $isCompleted ? 'text-zinc-400 dark:text-zinc-500 line-through' : 'text-zinc-700 dark:text-zinc-300' }} leading-snug">{{ $task->theme }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <flux:badge size="sm" :color="$task->status->color()">{{ $task->status->label() }}</flux:badge>
                                <span class="text-[10px] text-zinc-400">{{ $task->platform?->code ?? '—' }}</span>
                                @if($task->deadline_produksi)
                                    <span class="text-[10px] {{ $task->deadline_produksi->isPast() ? 'text-red-500' : 'text-zinc-400' }}">
                                        {{ $task->deadline_produksi->format('d/m') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Tidak ada task hari ini</p>
                @endforelse
            </div>
            @if($lateCount > 0)
                <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                    <p class="text-xs font-medium text-orange-500 dark:text-orange-400 flex items-center gap-1.5">
                        <flux:icon name="exclamation-triangle" class="size-3.5" />
                        Konten Terlambat: {{ $lateCount }}
                    </p>
                </div>
            @endif
        </flux:card>
    </div>

    {{-- 3. Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kanban Produksi --}}
        <flux:card class="p-5">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon name="view-columns" class="size-4 text-pink-500" />
                Kanban Produksi
            </h3>

            <div class="space-y-3">
                @foreach($stats as $item)
                    @php
                        $color = $item['status']->color();
                        $labelClass = match($color) {
                            'gray' => 'text-zinc-500',
                            'blue' => 'text-blue-600 dark:text-blue-400',
                            'amber' => 'text-amber-600 dark:text-amber-400',
                            'green' => 'text-green-600 dark:text-green-400',
                            'purple' => 'text-purple-600 dark:text-purple-400',
                            'emerald' => 'text-emerald-600 dark:text-emerald-400',
                            default => 'text-zinc-500'
                        };
                        $barClass = match($color) {
                            'gray' => 'bg-zinc-400',
                            'blue' => 'bg-blue-500',
                            'amber' => 'bg-amber-500',
                            'green' => 'bg-green-500',
                            'purple' => 'bg-purple-500',
                            'emerald' => 'bg-emerald-500',
                            default => 'bg-zinc-400'
                        };
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-medium {{ $labelClass }}">
                                {{ $item['status']->label() }} ({{ $item['count'] }})
                            </span>
                            <span class="text-[10px] text-zinc-400">{{ $item['percentage'] }}%</span>
                        </div>
                        <div class="h-1.5 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $barClass }}" style="width: {{ $item['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 space-y-2 max-h-[200px] overflow-y-auto">
                @foreach(collect($kanbanData)->flatten(1)->take(5) as $card)
                    @php $isSelectedCard = $card->id === $selectedContentId; @endphp
                    <div class="flex items-center gap-2.5 p-2 rounded-lg cursor-pointer transition-all
                         {{ $isSelectedCard ? 'bg-pink-50 dark:bg-pink-900/20 border-pink-300 dark:border-pink-700 ring-2 ring-pink-500' : 'bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700/50 hover:border-pink-200 dark:hover:border-pink-800' }}"
                         x-on:click="$wire.selectContent({{ $card->id }})">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium {{ $isSelectedCard ? 'text-pink-700 dark:text-pink-300' : 'text-zinc-700 dark:text-zinc-300' }} truncate">{{ $card->theme }}</p>
                            <p class="text-[10px] text-zinc-400">{{ $card->content_code }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[10px] text-zinc-500">{{ $card->picCopy?->name ?? '—' }}</p>
                            @if($card->deadline_produksi)
                                <p class="text-[10px] {{ $card->deadline_produksi->isPast() ? 'text-red-500' : 'text-zinc-400' }}">
                                    {{ $card->deadline_produksi->format('d/m') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Alur Approval --}}
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700">
                <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-3">Alur Approval</p>
                <div class="flex items-center justify-between">
                    @php
                        $approvalSteps = [
                            ['icon' => 'pencil', 'label' => 'CW', 'color' => 'text-blue-500'],
                            ['icon' => 'light-bulb', 'label' => 'CSP', 'color' => 'text-purple-500'],
                            ['icon' => 'share', 'label' => 'SMS', 'color' => 'text-green-500'],
                            ['icon' => 'beaker', 'label' => 'RnD', 'color' => 'text-amber-500', 'cond' => 'if claim'],
                            ['icon' => 'scale', 'label' => 'Legal', 'color' => 'text-red-500', 'cond' => 'if sensitive'],
                            ['icon' => 'check-circle', 'label' => 'Published', 'color' => 'text-emerald-500'],
                        ];
                    @endphp
                    @foreach($approvalSteps as $i => $step)
                        <div class="flex flex-col items-center {{ $i < count($approvalSteps) - 1 ? 'relative flex-1' : '' }}">
                            <div class="size-7 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center {{ $step['color'] }}">
                                <flux:icon name="{{ $step['icon'] }}" class="size-3.5" />
                            </div>
                            <p class="text-[9px] font-medium text-zinc-600 dark:text-zinc-400 mt-1">{{ $step['label'] }}</p>
                            @if(isset($step['cond']))
                                <p class="text-[7px] text-zinc-400 dark:text-zinc-500">({{ $step['cond'] }})</p>
                            @endif
                            @if($i < count($approvalSteps) - 1)
                                <div class="absolute top-3.5 left-[calc(50%+14px)] right-[calc(50%-40px)] h-px bg-zinc-200 dark:bg-zinc-700 hidden lg:block"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </flux:card>

        {{-- Detail Konten --}}
        <flux:card class="p-5">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon name="document-text" class="size-4 text-pink-500" />
                Detail Konten
            </h3>

            @if($selectedContent)
                <div class="flex gap-4 mb-4">
                    <div class="size-16 rounded-lg bg-gradient-to-br from-pink-100 to-purple-100 dark:from-pink-900/20 dark:to-purple-900/20 flex items-center justify-center shrink-0">
                        <flux:icon name="photo" class="size-8 text-pink-400" />
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $selectedContent->content_code }}</p>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-white mt-0.5">{{ $selectedContent->theme }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <flux:badge size="sm" :color="$selectedContent->status->color()">{{ $selectedContent->status->label() }}</flux:badge>
                            <span class="text-[10px] text-zinc-400">{{ $selectedContent->platform?->code }} · {{ $selectedContent->content_type?->label() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Brief --}}
                @php
                    $briefFields = [
                        ['label' => 'Tema', 'value' => $selectedContent->theme],
                        ['label' => 'Message', 'value' => $selectedContent->key_message],
                        ['label' => 'Target Audiens', 'value' => $selectedContent->target_audience],
                        ['label' => 'Jenis Konten', 'value' => $selectedContent->content_type?->label()],
                        ['label' => 'Tone of Voice', 'value' => $selectedContent->tone],
                    ];
                @endphp
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 mb-4">
                    @foreach($briefFields as $field)
                        <div>
                            <p class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500">{{ $field['label'] }}</p>
                            <p class="text-xs text-zinc-700 dark:text-zinc-300 truncate">{{ $field['value'] ?? '—' }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- File Attachments --}}
                @if($assets->isNotEmpty())
                    <div class="mb-4 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                        <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-2">Lampiran File</p>
                        <div class="space-y-1.5">
                            @foreach($assets as $asset)
                                <div class="flex items-center gap-2 py-1">
                                    <div class="size-7 rounded bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center shrink-0">
                                        <flux:icon name="{{ match($asset->type) { 'image' => 'photo', 'video' => 'video-camera', 'document' => 'document-text', default => 'paper-clip' } }}" class="size-3.5 text-zinc-500" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-zinc-700 dark:text-zinc-300 truncate">{{ basename($asset->link_or_path ?? '') ?: $asset->type }}</p>
                                        <p class="text-[9px] text-zinc-400">
                                            {{ $asset->uploader?->name ?? '—' }} · v{{ $asset->version ?? 1 }}
                                        </p>
                                    </div>
                                    @if($asset->link_or_path)
                                        <a href="{{ $asset->link_or_path }}" target="_blank" class="text-[10px] text-pink-600 hover:underline shrink-0">Lihat</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Approval Progress (real data from approvals table) --}}
                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-700">
                    <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-2">Approval Progress</p>
                    @php
                        $approvals = $selectedContent->approvals ?? collect();
                        $stages = ['cw', 'csp', 'sms'];
                        if ($selectedContent->has_claim) $stages[] = 'rnd';
                        if ($selectedContent->is_sensitive) $stages[] = 'legal';
                    @endphp
                    <div class="space-y-1.5">
                        @forelse($stages as $stage)
                            @php
                                $approval = $approvals->firstWhere('stage', $stage);
                                $status = $approval?->status ?? 'pending';
                                $approverName = $approval?->approver?->name ?? '—';
                                $isDone = $status === 'approved';
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="size-5 rounded-full flex items-center justify-center shrink-0
                                    {{ $isDone ? 'bg-green-500' : ($status === 'revision' ? 'bg-red-400' : 'bg-zinc-200 dark:bg-zinc-700') }}">
                                    @if($isDone)
                                        <flux:icon name="check" class="size-3 text-white" />
                                    @elseif($status === 'revision')
                                        <flux:icon name="arrow-uturn-left" class="size-3 text-white" />
                                    @else
                                        <span class="text-[8px] font-medium text-zinc-400 dark:text-zinc-500">{{ $loop->iteration }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between">
                                        <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ strtoupper($stage) }}</span>
                                        <span class="text-[9px] {{ $isDone ? 'text-green-600' : ($status === 'revision' ? 'text-red-500' : 'text-zinc-400') }}">
                                            {{ $status === 'approved' ? 'Approved' : ($status === 'revision' ? 'Revision' : 'Pending') }}
                                        </span>
                                    </div>
                                    @if($approval)
                                        <p class="text-[9px] text-zinc-400 dark:text-zinc-500 truncate">{{ $approverName }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-[10px] text-zinc-400 italic">Belum ada approval</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 text-zinc-400 dark:text-zinc-600">
                    <flux:icon name="document-text" class="size-10 mb-2" />
                    <p class="text-xs">Pilih konten dari Kanban atau Kalender</p>
                </div>
            @endif
        </flux:card>

        {{-- Team Capacity, Asset Library, Version History --}}
        <flux:card class="p-5">
            {{-- Team Capacity --}}
            <div class="mb-5">
                <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                    <flux:icon name="chart-bar" class="size-4 text-pink-500" />
                    Team Capacity ({{ $roleCapacity->first()['total'] ?? 40 }}h/minggu)
                </h3>
                <div class="space-y-3">
                    @foreach($roleCapacity as $role => $data)
                        @php
                            $colors = [
                                'GVD' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-600 dark:text-purple-400'],
                                'VG' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600 dark:text-blue-400'],
                                'CW' => ['bg' => 'bg-pink-500', 'text' => 'text-pink-600 dark:text-pink-400'],
                                'SMS' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                            ];
                            $c = $colors[$role] ?? ['bg' => 'bg-zinc-500', 'text' => 'text-zinc-600'];
                            $pct = $data['percentage'];
                            $used = $data['used'];
                            $total = $data['total'];
                            $isOver = $pct >= 100;
                            $barClass = $isOver ? 'bg-red-500' : $c['bg'];
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $role }}</span>
                                <span class="{{ $isOver ? 'text-red-500 font-bold' : $c['text'] }}">
                                    {{ $used }}j / {{ $total }}j
                                </span>
                            </div>
                            <div class="h-2 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $barClass }}" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Asset Library --}}
            <div class="mb-5 pt-4 border-t border-zinc-100 dark:border-zinc-700">
                <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-3 flex items-center gap-2">
                    <flux:icon name="folder" class="size-4 text-pink-500" />
                    Asset Library
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    @forelse($assetProducts as $product)
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700/50">
                            <div class="size-8 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center shrink-0">
                                <flux:icon name="folder" class="size-4 text-pink-400" />
                            </div>
                            <span class="text-xs text-zinc-600 dark:text-zinc-400 truncate">{{ $product->name }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 italic col-span-2">Belum ada produk</p>
                    @endforelse
                </div>
            </div>

            {{-- Versi Terakhir --}}
            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-700">
                <h3 class="text-sm font-semibold text-zinc-800 dark:text-white mb-3 flex items-center gap-2">
                    <flux:icon name="clock" class="size-4 text-pink-500" />
                    Versi Terakhir
                </h3>
                <div class="space-y-2 max-h-[150px] overflow-y-auto">
                    @forelse($recentVersions as $version)
                        <div class="flex items-center gap-2.5">
                            <div class="size-8 rounded bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center shrink-0">
                                <flux:icon name="document" class="size-4 text-zinc-400" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 truncate">{{ $version->content?->theme ?? 'Konten dihapus' }}</p>
                                <p class="text-[10px] text-zinc-400">v{{ $version->version }} · {{ $version->creator?->name ?? '—' }}</p>
                            </div>
                            <span class="text-[10px] text-zinc-400 shrink-0">{{ $version->created_at->format('d/m') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Belum ada versi</p>
                    @endforelse
                </div>
            </div>
        </flux:card>
    </div>
</div>
