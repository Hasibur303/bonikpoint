<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard - Bonik Point</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#087c7f', accent: '#9fbb18', ink: '#103f44' } } } }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-700">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <header class="flex items-center justify-between bg-ink p-4 text-white lg:hidden">
            <a href="{{ route('dashboard') }}" class="text-xl font-black">Bonik Point</a>
            <button id="dashboard-menu-button" type="button" class="grid h-10 w-10 place-items-center rounded border border-white/20" aria-controls="dashboard-sidebar" aria-expanded="false">
                <span class="sr-only">Open menu</span>
                <span class="text-2xl leading-none">&#9776;</span>
            </button>
        </header>

        <aside id="dashboard-sidebar" class="hidden bg-ink p-6 text-white lg:block">
            <a href="{{ route('dashboard') }}" class="block text-2xl font-black">Bonik Point</a>
            <p class="mt-2 text-sm text-gray-300">{{ auth()->user()->name }}</p>

            <nav class="mt-8 space-y-2 text-sm font-semibold">
                <a href="{{ route('dashboard') }}" class="block rounded px-3 py-2 hover:bg-white/10">Dashboard</a>
                <a href="{{ route('orders.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">My Orders</a>
                <a href="{{ route('cart.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">My Cart</a>
                <a href="{{ route('profile.edit') }}" class="block rounded px-3 py-2 hover:bg-white/10">Profile</a>
                <a href="{{ route('shop.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Continue Shopping</a>
                <a href="{{ route('home.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Home</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full rounded px-3 py-2 text-left hover:bg-white/10">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="p-6 lg:p-10">
            @if(session('success') || session('error'))
                <div class="mb-6 rounded border px-4 py-3 text-sm {{ session('success') ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                    {{ session('success') ?? session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('dashboard-menu-button');
            const sidebar = document.getElementById('dashboard-sidebar');

            if (!button || !sidebar) {
                return;
            }

            button.addEventListener('click', function () {
                const isOpen = !sidebar.classList.contains('hidden');
                sidebar.classList.toggle('hidden', isOpen);
                button.setAttribute('aria-expanded', String(!isOpen));
            });
        });
    </script>
</body>
</html>
