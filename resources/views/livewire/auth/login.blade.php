<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-zinc-50 via-pink-50/30 to-white dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900">
    <div class="w-full max-w-sm mx-auto space-y-6">
        <div class="flex flex-col items-center gap-3">
            <div class="size-14 rounded-xl bg-gradient-to-br from-pink-500 to-pink-700 flex items-center justify-center shadow-lg shadow-pink-500/20">
                <span class="text-2xl font-bold text-white">M</span>
            </div>
            <div class="text-center">
                <h1 class="text-xl font-bold tracking-tight text-zinc-800 dark:text-white">Maryamé ERP</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Content & Creative</p>
            </div>
        </div>

        <flux:card class="space-y-6 !shadow-xl !shadow-pink-500/5">
            <p class="text-sm font-medium text-zinc-800 dark:text-white">Sign in to your account</p>

            <form wire:submit="login" class="space-y-5" autocomplete="off">
                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input
                        type="email"
                        wire:model="email"
                        placeholder="admin@maryame.com"
                        autocomplete="off"
                    />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Password</flux:label>
                    <flux:input
                        type="password"
                        wire:model="password"
                        placeholder="Masukkan password"
                        autocomplete="off"
                    />
                    <flux:error name="password" />
                </flux:field>

                <div class="flex items-center justify-between">
                    <flux:checkbox wire:model="remember" label="Remember me" />
                </div>

                <flux:button
                    type="submit"
                    variant="primary"
                    color="pink"
                    class="w-full !shadow-lg !shadow-pink-500/20"
                    wire:loading.attr="disabled"
                    wire:target="login"
                >
                    <span wire:loading.remove wire:target="login">Sign in</span>
                    <span wire:loading wire:target="login" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Signing in...
                    </span>
                </flux:button>

                <div class="text-center">
                    <a href="#" class="text-sm text-pink-600 hover:text-pink-700 dark:text-pink-400 dark:hover:text-pink-300 hover:underline transition-colors">
                        Lupa password?
                    </a>
                </div>
            </form>
        </flux:card>

        <div class="flex justify-center">
            <div class="flex items-center gap-2">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Dark mode</span>
                <flux:switch x-data x-model="$flux.dark" />
            </div>
        </div>
    </div>
</div>
