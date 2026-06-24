<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>my date</title><link rel="icon" type="image/png" href="{{ asset('heart.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net"><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-rose-50">
    <nav class="bg-white border-b border-rose-100"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between"><a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-rose-600" wire:navigate><img src="{{ asset('heart.png') }}" class="w-8 h-8 rounded-full" alt="my date">my date</a><a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-rose-600" wire:navigate>Log in</a></div></nav>
    <main>{{ $slot }}</main>
</body>
</html>
