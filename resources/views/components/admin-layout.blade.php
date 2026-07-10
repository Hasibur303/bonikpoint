<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Bonik Point</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#087c7f', accent: '#9fbb18', ink: '#103f44' } } } }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-700">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <header class="flex items-center justify-between bg-ink p-4 text-white lg:hidden">
            <a href="{{ route('admin.index') }}" class="text-xl font-black">Bonik Point</a>
            <button id="admin-menu-button" type="button" class="grid h-10 w-10 place-items-center rounded border border-white/20" aria-controls="admin-sidebar" aria-expanded="false">
                <span class="sr-only">Open menu</span>
                <span class="text-2xl leading-none">&#9776;</span>
            </button>
        </header>

        <aside id="admin-sidebar" class="hidden bg-ink p-6 text-white lg:block">
            <a href="{{ route('admin.index') }}" class="block text-2xl font-black">Bonik Point</a>
            <nav class="mt-8 space-y-2 text-sm font-semibold">
                <a href="{{ route('admin.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Categories</a>
                <a href="{{ route('admin.festivals.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Festivals</a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Orders</a>
                <a href="{{ route('admin.profit.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Profit</a>
                <a href="{{ route('admin.users.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Users</a>
                <a href="{{ route('admin.settings.edit') }}" class="block rounded px-3 py-2 hover:bg-white/10">Settings</a>
                <a href="{{ route('home.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">View Store</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full rounded px-3 py-2 text-left hover:bg-white/10">Logout</button>
                </form>
            </nav>
        </aside>
        <main class="p-6 lg:p-10">
            @if(session('success'))
                <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('admin-menu-button');
            const sidebar = document.getElementById('admin-sidebar');

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
