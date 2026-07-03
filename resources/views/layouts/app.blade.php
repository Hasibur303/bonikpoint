<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bonik Point') }}</title>
    <link href="{{ asset('favicon.jpg') }}" rel="shortcut icon" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#087c7f',
                        accent: '#9fbb18',
                        ink: '#103f44',
                    },
                    container: {
                        center: true,
                        padding: '1rem',
                        screens: { xl: '1200px' },
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-700 antialiased">
    <header class="sticky top-0 z-50 border-b border-gray-100 bg-white/95 backdrop-blur">
        <div class="container">
            <div class="flex h-20 items-center justify-between gap-6">
                <a href="{{ route('home.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Bonik Point" class="h-14 w-auto object-contain">
                </a>

                <nav class="hidden items-center gap-8 text-sm font-semibold uppercase tracking-wide text-ink md:flex">
                    <a href="{{ route('home.index') }}" class="{{ request()->routeIs('home.index') ? 'text-primary' : 'hover:text-primary' }}">Home</a>
                    <a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.*') ? 'text-primary' : 'hover:text-primary' }}">Shop</a>
                    <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'text-primary' : 'hover:text-primary' }}">Cart</a>
                    @auth
                        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'text-primary' : 'hover:text-primary' }}">Orders</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.index') }}" class="hover:text-primary">Admin</a>
                        @endif
                    @endauth
                </nav>

                <div class="flex items-center gap-4">
                    <form action="{{ route('shop.index') }}" class="hidden xl:block">
                        <input name="search" value="{{ request('search') }}" placeholder="Search products" class="w-56 rounded-full border-gray-200 text-sm focus:border-primary focus:ring-primary">
                    </form>

                    <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-full border border-gray-200 text-ink hover:border-primary hover:text-primary" title="Cart">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="absolute -right-2 -top-2 grid h-5 min-w-5 place-items-center rounded-full bg-accent px-1 text-xs font-bold text-white">{{ \App\Http\Controllers\CartController::count() }}</span>
                    </a>

                    @auth
                        <div class="group relative">
                            <button class="grid h-10 w-10 place-items-center rounded-full bg-primary text-white" title="Account">
                                <i class="fa-regular fa-user"></i>
                            </button>
                            <div class="invisible absolute right-0 mt-3 w-48 rounded bg-white py-2 opacity-0 shadow-xl ring-1 ring-gray-100 transition group-hover:visible group-hover:opacity-100">
                                <div class="px-4 py-2 text-xs font-semibold uppercase text-gray-400">{{ auth()->user()->name }}</div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Profile</a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">My Orders</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50">Sign Out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full bg-ink px-5 py-2 text-sm font-semibold text-white hover:bg-primary">Sign In</a>
                    @endauth

                    <button id="mobile-menu-button" type="button" class="grid h-10 w-10 place-items-center rounded border border-gray-200 text-ink hover:border-primary hover:text-primary md:hidden" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open menu</span>
                        <span class="text-2xl leading-none">&#9776;</span>
                    </button>
                </div>
            </div>

            <div id="mobile-menu" class="hidden border-t border-gray-100 py-4 md:hidden">
                <form action="{{ route('shop.index') }}" class="mb-4">
                    <input name="search" value="{{ request('search') }}" placeholder="Search products" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary focus:ring-primary">
                </form>

                <nav class="grid gap-2 text-sm font-semibold uppercase tracking-wide text-ink">
                    <a href="{{ route('home.index') }}" class="rounded px-3 py-2 {{ request()->routeIs('home.index') ? 'bg-primary text-white' : 'hover:bg-gray-50 hover:text-primary' }}">Home</a>
                    <a href="{{ route('shop.index') }}" class="rounded px-3 py-2 {{ request()->routeIs('shop.*') ? 'bg-primary text-white' : 'hover:bg-gray-50 hover:text-primary' }}">Shop</a>
                    <a href="{{ route('cart.index') }}" class="rounded px-3 py-2 {{ request()->routeIs('cart.*') ? 'bg-primary text-white' : 'hover:bg-gray-50 hover:text-primary' }}">Cart</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'hover:bg-gray-50 hover:text-primary' }}">Dashboard</a>
                        <a href="{{ route('orders.index') }}" class="rounded px-3 py-2 {{ request()->routeIs('orders.*') ? 'bg-primary text-white' : 'hover:bg-gray-50 hover:text-primary' }}">Orders</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.index') }}" class="rounded px-3 py-2 hover:bg-gray-50 hover:text-primary">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full rounded px-3 py-2 text-left hover:bg-gray-50 hover:text-primary">Sign Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded px-3 py-2 hover:bg-gray-50 hover:text-primary">Sign In</a>
                        <a href="{{ route('register') }}" class="rounded px-3 py-2 hover:bg-gray-50 hover:text-primary">Register</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    @if(session('success') || session('error'))
        <div class="container mt-4">
            <div class="rounded border px-4 py-3 text-sm {{ session('success') ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                {{ session('success') ?? session('error') }}
            </div>
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-16 bg-ink py-12 text-gray-100">
        <div class="container grid gap-8 md:grid-cols-4">
            <div class="md:col-span-2">
                <img src="{{ asset('assets/images/logo.jpg') }}" alt="Bonik Point" class="mb-5 h-20 w-auto rounded bg-white p-2">
                <p class="max-w-lg text-sm leading-6 text-gray-300">Bonik Point brings stylish products, simple ordering, and admin-managed stock in one Laravel ecommerce platform.</p>
            </div>
            <div>
                <h3 class="mb-4 font-bold text-white">Shop</h3>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('shop.index') }}" class="block hover:text-accent">All Products</a>
                    <a href="{{ route('cart.index') }}" class="block hover:text-accent">Cart</a>
                    <a href="{{ route('orders.index') }}" class="block hover:text-accent">Orders</a>
                </div>
            </div>
            <div>
                <h3 class="mb-4 font-bold text-white">Contact</h3>
                <p class="text-sm text-gray-300">Gulshan, Bangladesh</p>
                <p class="text-sm text-gray-300">+8801580491525</p>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');

            if (!button || !menu) {
                return;
            }

            button.addEventListener('click', function () {
                const isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden', isOpen);
                button.setAttribute('aria-expanded', String(!isOpen));
            });
        });
    </script>
</body>
</html>
