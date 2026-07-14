<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,follow">
    <title>Page Not Found | Bonik Point</title>
    <meta name="description" content="The requested Bonik Point page could not be found. Continue to the shop or return home.">
    <link href="{{ asset('favicon.jpg') }}" rel="shortcut icon" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-[#f4f7f6] px-4 text-gray-700">
    <main class="w-full max-w-xl rounded-lg border border-gray-100 bg-white p-8 text-center shadow-xl sm:p-12">
        <a href="{{ route('home.index') }}" aria-label="Bonik Point home" class="inline-block">
            <img src="{{ asset('assets/images/logo.webp') }}" alt="Bonik Point" width="160" height="100" class="mx-auto h-24 w-auto object-contain">
        </a>
        <p class="mt-6 text-sm font-black uppercase tracking-wide text-primary">Error 404</p>
        <h1 class="mt-2 text-3xl font-black text-ink sm:text-4xl">Page not found</h1>
        <p class="mx-auto mt-4 max-w-md leading-7 text-gray-600">The page may have moved or the address may be incorrect. You can continue shopping safely.</p>
        <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('shop.index') }}" class="rounded-md bg-primary px-6 py-3 font-bold text-white hover:bg-ink">Go to Shop</a>
            <a href="{{ route('home.index') }}" class="rounded-md border border-gray-200 px-6 py-3 font-bold text-ink hover:border-primary hover:text-primary">Return Home</a>
        </div>
    </main>
</body>
</html>
