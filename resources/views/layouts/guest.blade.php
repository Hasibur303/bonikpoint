@props(['wide' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Bonik Point') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-slate-100 px-4 py-6 sm:px-6 lg:px-8">
            @unless($wide)
                <a href="{{ route('home.index') }}" class="mb-6" aria-label="Bonik Point home">
                    <x-application-logo class="h-24 w-auto" />
                </a>
            @endunless

            <div @class([
                'w-full overflow-hidden bg-white',
                'max-w-5xl rounded-lg shadow-2xl shadow-slate-300/70' => $wide,
                'max-w-md rounded-lg px-6 py-5 shadow-lg shadow-slate-300/60' => ! $wide,
            ])>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
