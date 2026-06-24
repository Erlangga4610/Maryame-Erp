@props(['title' => 'Login'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Maryamé ERP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @fluxAppearance
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased">
    {{ $slot }}

    <x-flasher />

    @livewireScripts
    @fluxScripts
    @vite(['resources/js/app.js'])
</body>
</html>
