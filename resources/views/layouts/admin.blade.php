@props(['title' => 'Dashboard'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Maryamé ERP - {{ $title }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=satoshi:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @fluxAppearance
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />

        <flux:brand href="#" class="max-lg:hidden">
            <x-slot:logo>
                <div class="size-7 rounded-lg bg-pink-600 flex items-center justify-center">
                    <span class="text-sm font-bold text-white">M</span>
                </div>
            </x-slot:logo>
            Maryamé ERP
        </flux:brand>

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="home" href="/dashboard" :current="request()->routeIs('dashboard')">Dashboard</flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <flux:navbar class="me-4">
            <flux:navbar.item icon="magnifying-glass" href="#" label="Search" />
        </flux:navbar>

        <flux:dropdown position="top" align="start">
            <flux:profile avatar="{{ Auth::user()->avatarUrl() ?? Auth::user()->name ?? 'Admin' }}" />

            <flux:menu>
                <flux:menu.item icon="user" href="/profile">Profile</flux:menu.item>
                <flux:menu.separator />
                <form method="POST" action="/logout">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                        Logout
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.brand href="#">
                <x-slot:logo>
                    <div class="size-7 rounded-lg bg-pink-600 flex items-center justify-center">
                        <span class="text-sm font-bold text-white">M</span>
                    </div>
                </x-slot:logo>
                <x-slot:name>
                    Maryamé ERP
                </x-slot:name>
            </flux:sidebar.brand>

            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="/dashboard" :current="request()->routeIs('dashboard')">Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="calendar" href="/contents" :current="request()->routeIs('contents.*')">Content Calendar</flux:sidebar.item>
            <flux:sidebar.item icon="clipboard-document-list" href="/my-tasks" :current="request()->routeIs('my-tasks')">My Tasks</flux:sidebar.item>
            <flux:sidebar.item icon="calendar-days" href="/calendar" :current="request()->routeIs('calendar')">Calendar</flux:sidebar.item>
            <flux:sidebar.item icon="chart-bar-square" href="/mix-tracker" :current="request()->routeIs('mix-tracker')">Mix Tracker</flux:sidebar.item>
            <flux:sidebar.item icon="inbox" href="/approval-inbox" :current="request()->routeIs('approval.*')">Approval Inbox</flux:sidebar.item>
            <flux:sidebar.item icon="clock" href="/production-schedule" :current="request()->routeIs('production.*')">Production Schedule</flux:sidebar.item>
            <flux:sidebar.item icon="chart-bar" href="/capacity" :current="request()->routeIs('capacity')">Capacity</flux:sidebar.item>

            <flux:sidebar.group expandable heading="Master Data">
                <flux:sidebar.item href="/master-data/platforms">Platforms</flux:sidebar.item>
                <flux:sidebar.item href="/master-data/products">Products</flux:sidebar.item>
                <flux:sidebar.item href="/master-data/campaigns">Campaigns</flux:sidebar.item>
                <flux:sidebar.item href="/master-data/users">Users</flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
            <flux:sidebar.item icon="user-circle" href="/profile" :current="request()->routeIs('profile')">Profile</flux:sidebar.item>
            <flux:sidebar.item icon="information-circle" href="/role-guide" :current="request()->routeIs('role-guide')">Role Guide</flux:sidebar.item>
        </flux:sidebar.nav>
    </flux:sidebar>

    <flux:main container>
        <div class="flex max-md:flex-col items-start">
            <div class="w-full md:w-[220px] pb-4 me-10 max-lg:hidden">
                <flux:heading size="lg" level="2" class="mb-4">{{ $title }}</flux:heading>

                <flux:navlist>
                    <flux:navlist.item href="/dashboard" icon="home" :current="request()->routeIs('dashboard')">Dashboard</flux:navlist.item>
                    <flux:navlist.item href="/contents" icon="calendar" :current="request()->routeIs('contents.*')">Content Calendar</flux:navlist.item>
                    <flux:navlist.item href="/my-tasks" icon="clipboard-document-list" :current="request()->routeIs('my-tasks')">My Tasks</flux:navlist.item>
                    <flux:navlist.item href="/calendar" icon="calendar-days" :current="request()->routeIs('calendar')">Calendar</flux:navlist.item>
                    <flux:navlist.item href="/mix-tracker" icon="chart-bar-square" :current="request()->routeIs('mix-tracker')">Mix Tracker</flux:navlist.item>
                    <flux:navlist.item href="/approval-inbox" icon="inbox" :current="request()->routeIs('approval.*')">Approval Inbox</flux:navlist.item>
                    <flux:navlist.item href="/production-schedule" icon="clock" :current="request()->routeIs('production.*')">Production Schedule</flux:navlist.item>
                    <flux:navlist.item href="/capacity" icon="chart-bar" :current="request()->routeIs('capacity')">Capacity</flux:navlist.item>

                    <flux:separator />

                    <flux:navlist.item icon="user-circle" href="/profile" :current="request()->routeIs('profile')">Profile</flux:navlist.item>
                    <flux:navlist.item icon="circle-stack">Master Data</flux:navlist.item>
                    <div class="pl-10 space-y-1">
                        <flux:navlist.item href="/master-data/platforms">Platforms</flux:navlist.item>
                        <flux:navlist.item href="/master-data/products">Products</flux:navlist.item>
                        <flux:navlist.item href="/master-data/campaigns">Campaigns</flux:navlist.item>
                        <flux:navlist.item href="/master-data/users">Users</flux:navlist.item>
                    </div>
                </flux:navlist>

                <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Dark mode</span>
                        <flux:switch x-data x-model="$flux.dark" />
                    </div>
                </div>
            </div>

            <flux:separator class="md:hidden" />

            <div class="flex-1 max-md:pt-6 self-stretch">
                {{ $slot }}
            </div>
        </div>
    </flux:main>

    <x-flasher />

    @livewireScripts
    @fluxScripts
    @vite(['resources/js/app.js'])
</body>
</html>
