<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Bonik Point</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-700">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <aside class="bg-ink p-6 text-white">
            <a href="{{ route('admin.index') }}" class="block text-2xl font-black">Bonik Point</a>
            <nav class="mt-8 space-y-2 text-sm font-semibold">
                <a href="{{ route('admin.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Categories</a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Orders</a>
                <a href="{{ route('admin.users.index') }}" class="block rounded px-3 py-2 hover:bg-white/10">Users</a>
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
</body>
</html>
