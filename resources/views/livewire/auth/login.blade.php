<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-pink-100 via-purple-50 to-white dark:from-zinc-900 dark:via-pink-950/10 dark:to-zinc-900">
    <div class="fixed top-5 right-5 z-50 flex items-center gap-2">
        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Dark</span>
        <flux:switch x-data x-model="$flux.dark" />
    </div>

    <div class="w-[420px]">
        <div class="flex flex-col items-center gap-4 mb-8">
            <div class="size-14 rounded-xl bg-gradient-to-br from-pink-500 to-pink-700 flex items-center justify-center shadow-lg shadow-pink-500/30">
                <span class="text-2xl font-bold text-white">M</span>
            </div>
            <div class="text-center">
                <h1 class="text-xl font-bold tracking-tight text-zinc-800 dark:text-white">Maryamé</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">ERP Content & Creative</p>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800/90 p-8 rounded-2xl shadow-xl shadow-pink-500/5 border border-pink-100/50 dark:border-zinc-700/50 space-y-6">
            <div>
                <h2 class="text-sm font-semibold text-zinc-800 dark:text-white">Selamat datang</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Masuk ke akun Anda</p>
            </div>

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
                    <flux:label>Kata Sandi</flux:label>
                    <flux:input
                        type="password"
                        wire:model="password"
                        placeholder="Masukkan kata sandi"
                        autocomplete="off"
                    />
                    <flux:error name="password" />
                </flux:field>

                <div class="flex items-center justify-between">
                    <flux:checkbox wire:model="remember" label="Ingat saya" />
                </div>

                <flux:button
                    type="submit"
                    variant="primary"
                    color="pink"
                    class="w-full !shadow-lg !shadow-pink-500/20"
                    wire:loading.attr="disabled"
                    wire:target="login"
                >
                    <span wire:loading.remove wire:target="login">Masuk</span>
                    <span wire:loading wire:target="login" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memproses...
                    </span>
                </flux:button>
            </form>
        </div>

        <p class="text-center text-xs text-zinc-400 dark:text-zinc-500 mt-6">
            &copy; {{ date('Y') }} Maryamé. All rights reserved.
        </p>
    </div>

    <div class="fixed bottom-5 right-5 z-50 flex items-center gap-3 text-xs text-zinc-400 dark:text-zinc-500">
        <span id="login-time">{{ now()->format('H:i') }}</span>
        <span class="text-zinc-300 dark:text-zinc-600">|</span>
        <span>ID</span>
    </div>

    <script>
        var el = document.getElementById('login-time');
        if (el) {
            function update() {
                var now = new Date();
                el.textContent = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            }
            setInterval(update, 10000);
        }
    </script>
</div>
