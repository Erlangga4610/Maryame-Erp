<div class="space-y-8">
    <flux:heading size="lg">Panduan Role & Tugas</flux:heading>

    {{-- Pipeline Approval --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 bg-white dark:bg-zinc-800">
        <h3 class="text-base font-semibold text-zinc-800 dark:text-white mb-4">Pipeline Approval</h3>
        <div class="flex items-center gap-2 flex-wrap">
            @foreach($pipeline as $step)
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold {{ match($step['color']) { 'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'zinc' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300', 'pink' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300', 'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300', default => 'bg-zinc-100 text-zinc-600' } }}">
                        {{ $step['stage'] }}
                    </span>
                    @if(!$loop->last)
                        <flux:icon.arrow-right class="size-3.5 text-zinc-300 dark:text-zinc-600 shrink-0" />
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($pipeline as $step)
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $step['stage'] }}</span>
                    — {{ $step['desc'] }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- RBAC Tiers --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 bg-white dark:bg-zinc-800">
        <h3 class="text-base font-semibold text-zinc-800 dark:text-white mb-4">RBAC Tier System</h3>
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800">
                <span class="font-bold text-red-600 dark:text-red-400">Tier 0</span>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Super Admin — bypass semua aturan</p>
            </div>
            <div class="p-3 rounded-lg bg-pink-50 dark:bg-pink-900/10 border border-pink-200 dark:border-pink-800">
                <span class="font-bold text-pink-600 dark:text-pink-400">Tier 1</span>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Edit — bisa membuat & mengubah konten</p>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800">
                <span class="font-bold text-emerald-600 dark:text-emerald-400">Tier 2</span>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Comment — bisa melihat & berkomentar</p>
            </div>
        </div>
    </div>

    {{-- Daftar Role --}}
    <div class="space-y-3">
        @foreach($roles as $role)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-800">
                <div class="flex items-center justify-between px-5 py-3 {{ match($role['color']) {
                    'red' => 'bg-red-50 dark:bg-red-900/10 border-b border-red-200 dark:border-red-800',
                    'pink' => 'bg-pink-50 dark:bg-pink-900/10 border-b border-pink-200 dark:border-pink-800',
                    'purple' => 'bg-purple-50 dark:bg-purple-900/10 border-b border-purple-200 dark:border-purple-800',
                    'emerald' => 'bg-emerald-50 dark:bg-emerald-900/10 border-b border-emerald-200 dark:border-emerald-800',
                    default => 'bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700',
                } }}">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $role['badge'] }}">
                            {{ $role['name'] }}
                        </span>
                        <span class="text-xs font-mono text-zinc-400 dark:text-zinc-500">Tier {{ $role['tier'] }}</span>
                    </div>
                </div>
                <div class="px-5 py-3">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">{{ $role['description'] }}</p>
                    <ul class="space-y-1">
                        @foreach($role['tasks'] as $task)
                            <li class="flex items-start gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                <span class="size-1.5 rounded-full mt-1.5 shrink-0 {{ match($role['color']) {
                                    'red' => 'bg-red-400', 'pink' => 'bg-pink-400', 'purple' => 'bg-purple-400',
                                    'emerald' => 'bg-emerald-400', default => 'bg-zinc-400',
                                } }}"></span>
                                {{ $task }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>
