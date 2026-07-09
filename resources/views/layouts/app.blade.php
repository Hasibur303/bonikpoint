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
                    <div class="group relative">
                        <button type="button" class="{{ request('category') ? 'text-primary' : 'hover:text-primary' }}">
                            Categories
                        </button>
                        <div class="invisible absolute left-0 top-full z-50 w-64 pt-3 opacity-0 transition group-hover:visible group-hover:opacity-100">
                            <div class="max-h-[420px] overflow-y-auto rounded bg-white py-2 shadow-xl ring-1 ring-gray-100">
                                <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-sm normal-case tracking-normal text-ink hover:bg-gray-50 hover:text-primary">All Categories</a>
                                @foreach($headerCategories ?? [] as $category)
                                    @php
                                        $isCategoryActive = request('category') === $category->slug;
                                        $hasActiveChild = $category->children->contains('slug', request('category'));
                                    @endphp

                                    @if($category->children->isNotEmpty())
                                        <details class="border-t border-gray-50 first:border-t-0" {{ $isCategoryActive || $hasActiveChild ? 'open' : '' }}>
                                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-2 text-sm normal-case tracking-normal text-ink hover:bg-gray-50 hover:text-primary">
                                                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="{{ $isCategoryActive ? 'text-primary' : '' }}">{{ $category->name }}</a>
                                                <span class="text-xs text-gray-400">&#9656;</span>
                                            </summary>
                                            <div class="pb-1">
                                                @foreach($category->children as $child)
                                                    <a href="{{ route('shop.index', ['category' => $child->slug]) }}" class="block px-7 py-2 text-sm normal-case tracking-normal {{ request('category') === $child->slug ? 'bg-gray-50 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                                        {{ $child->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </details>
                                    @else
                                        <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="block px-4 py-2 text-sm normal-case tracking-normal {{ $isCategoryActive ? 'bg-gray-50 text-primary' : 'text-ink hover:bg-gray-50 hover:text-primary' }}">
                                            {{ $category->name }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
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

                    <button id="open-cart-drawer" type="button" class="relative grid h-10 w-10 place-items-center rounded-full border border-gray-200 text-ink hover:border-primary hover:text-primary" title="Cart">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span id="cart-count-badge" class="absolute -right-2 -top-2 grid h-5 min-w-5 place-items-center rounded-full bg-accent px-1 text-xs font-bold text-white">{{ $drawerCart['count'] ?? 0 }}</span>
                    </button>

                    @auth
                        <div class="group relative">
                            <button class="grid h-10 w-10 place-items-center rounded-full bg-primary text-white" title="Account">
                                <i class="fa-regular fa-user"></i>
                            </button>
                            <div class="invisible absolute right-0 top-full z-50 w-48 pt-2 opacity-0 transition group-hover:visible group-hover:opacity-100">
                                <div class="rounded bg-white py-2 shadow-xl ring-1 ring-gray-100">
                                    <div class="px-4 py-2 text-xs font-semibold uppercase text-gray-400">{{ auth()->user()->name }}</div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Profile</a>
                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">My Orders</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50">Sign Out</button>
                                    </form>
                                </div>
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
                    <div class="rounded border border-gray-100 p-2">
                        <p class="px-1 pb-2 text-xs font-bold text-gray-400">Categories</p>
                        <div class="grid gap-1 normal-case tracking-normal">
                            <a href="{{ route('shop.index') }}" class="rounded px-3 py-2 text-sm {{ request('category') ? 'hover:bg-gray-50 hover:text-primary' : 'bg-primary text-white' }}">All Categories</a>
                            @foreach($headerCategories ?? [] as $category)
                                @php
                                    $isCategoryActive = request('category') === $category->slug;
                                    $hasActiveChild = $category->children->contains('slug', request('category'));
                                @endphp

                                @if($category->children->isNotEmpty())
                                    <details class="rounded border border-gray-100" {{ $isCategoryActive || $hasActiveChild ? 'open' : '' }}>
                                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 rounded px-3 py-2 text-sm {{ $isCategoryActive ? 'bg-primary text-white' : 'hover:bg-gray-50 hover:text-primary' }}">
                                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                                            <span class="text-xs {{ $isCategoryActive ? 'text-white' : 'text-gray-400' }}">&#9656;</span>
                                        </summary>
                                        <div class="grid gap-1 px-2 pb-2 pt-1">
                                            @foreach($category->children as $child)
                                                <a href="{{ route('shop.index', ['category' => $child->slug]) }}" class="rounded px-4 py-2 text-sm {{ request('category') === $child->slug ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                                    {{ $child->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="rounded px-3 py-2 text-sm {{ $isCategoryActive ? 'bg-primary text-white' : 'hover:bg-gray-50 hover:text-primary' }}">
                                        {{ $category->name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
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

    <div id="cart-drawer-overlay" class="fixed inset-0 z-[80] hidden bg-black/40"></div>
    <aside id="cart-drawer" class="fixed right-0 top-0 z-[90] flex h-full w-full max-w-md translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300">
        <div class="flex items-center justify-between border-b px-5 py-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-primary">Shopping Cart</p>
                <h2 class="text-xl font-black text-ink">Your Items</h2>
            </div>
            <button id="close-cart-drawer" type="button" class="grid h-10 w-10 place-items-center rounded-full border border-gray-200 text-xl text-ink hover:border-primary hover:text-primary">
                &times;
            </button>
        </div>

        <div id="cart-drawer-items" class="flex-1 overflow-y-auto p-5"></div>

        <div class="border-t p-5">
            <div class="mb-4 flex items-center justify-between text-lg font-black text-ink">
                <span>Subtotal</span>
                <span id="cart-drawer-subtotal">BDT 0.00</span>
            </div>
            <div class="grid gap-3">
                <a href="{{ route('checkout.create') }}" id="cart-drawer-checkout" class="rounded-lg bg-primary px-5 py-3 text-center font-semibold text-white hover:bg-ink">Place Order</a>
                <a href="{{ route('shop.index') }}" class="rounded-lg border border-gray-200 px-5 py-3 text-center font-semibold text-ink hover:border-primary hover:text-primary">Continue Shopping</a>
            </div>
        </div>
    </aside>

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
                <p class="text-sm text-gray-300">Shimrail Zero point, Siddirganj, Narayanganj</p>
                <p class="text-sm text-gray-300">Contact: 01540381020</p>
                <p class="text-sm text-gray-300">WhatsApp: 01540381020</p>
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

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const drawer = document.getElementById('cart-drawer');
            const overlay = document.getElementById('cart-drawer-overlay');
            const openCartButton = document.getElementById('open-cart-drawer');
            const closeCartButton = document.getElementById('close-cart-drawer');
            const cartItems = document.getElementById('cart-drawer-items');
            const cartSubtotal = document.getElementById('cart-drawer-subtotal');
            const cartBadge = document.getElementById('cart-count-badge');
            const checkoutButton = document.getElementById('cart-drawer-checkout');
            let cartState = @json($drawerCart ?? ['items' => [], 'count' => 0, 'subtotal' => 0]);

            const money = (value) => `BDT ${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            const escapeHtml = (value) => String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const openDrawer = () => {
                drawer.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeDrawer = () => {
                drawer.classList.add('translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            const renderCart = () => {
                cartBadge.textContent = cartState.count || 0;
                cartSubtotal.textContent = money(cartState.subtotal);
                checkoutButton.classList.toggle('pointer-events-none', !cartState.count);
                checkoutButton.classList.toggle('opacity-50', !cartState.count);

                if (!cartState.items || cartState.items.length === 0) {
                    cartItems.innerHTML = `
                        <div class="flex h-full flex-col items-center justify-center text-center">
                            <p class="text-lg font-bold text-ink">Your cart is empty</p>
                            <p class="mt-2 text-sm text-gray-500">Add products and they will appear here.</p>
                        </div>
                    `;
                    return;
                }

                cartItems.innerHTML = cartState.items.map((item) => `
                    <div class="mb-4 grid grid-cols-[72px_1fr] gap-4 rounded-lg border border-gray-100 p-3" data-cart-item="${escapeHtml(item.key)}">
                        <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" class="h-20 w-20 rounded object-cover">
                        <div>
                            <div class="flex gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-ink">${escapeHtml(item.name)}</p>
                                    ${item.festival_title ? `<p class="text-xs font-bold uppercase tracking-wide text-accent">${escapeHtml(item.festival_title)}</p>` : ''}
                                    <p class="text-sm text-gray-500">${money(item.price)}</p>
                                </div>
                                <button type="button" class="js-cart-remove text-sm font-semibold text-red-500 hover:text-red-700" data-product-id="${item.id}" data-cart-key="${escapeHtml(item.key)}">Remove</button>
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <div class="flex items-center overflow-hidden rounded border border-gray-200">
                                    <button type="button" class="js-cart-decrease px-3 py-1 text-lg" data-product-id="${item.id}" data-cart-key="${escapeHtml(item.key)}" data-quantity="${item.quantity}">-</button>
                                    <span class="min-w-10 px-3 text-center text-sm font-semibold">${item.quantity}</span>
                                    <button type="button" class="js-cart-increase px-3 py-1 text-lg" data-product-id="${item.id}" data-cart-key="${escapeHtml(item.key)}" data-quantity="${item.quantity}" data-stock="${item.stock}">+</button>
                                </div>
                                <p class="font-bold text-primary">${money(item.total)}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
            };

            const requestCart = async (url, options = {}) => {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        ...(options.headers || {}),
                    },
                    ...options,
                });

                if (!response.ok) {
                    throw new Error('Cart request failed');
                }

                const data = await response.json();
                cartState = data.cart;
                renderCart();
                openDrawer();
            };

            document.querySelectorAll('.js-add-to-cart').forEach((form) => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    requestCart(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                    });
                });
            });

            cartItems.addEventListener('click', function (event) {
                const button = event.target.closest('button[data-product-id]');

                if (!button) {
                    return;
                }

                const productId = button.dataset.productId;
                const cartKey = button.dataset.cartKey;
                const keyedBody = new URLSearchParams();
                keyedBody.append('cart_key', cartKey);

                if (button.classList.contains('js-cart-remove')) {
                    requestCart(`{{ url('/cart') }}/${productId}`, { method: 'DELETE', body: keyedBody });
                    return;
                }

                const currentQuantity = Number(button.dataset.quantity);
                const stock = Number(button.dataset.stock || currentQuantity);
                const nextQuantity = button.classList.contains('js-cart-increase')
                    ? Math.min(currentQuantity + 1, stock)
                    : currentQuantity - 1;

                if (nextQuantity < 1) {
                    requestCart(`{{ url('/cart') }}/${productId}`, { method: 'DELETE', body: keyedBody });
                    return;
                }

                const body = new URLSearchParams();
                body.append('quantity', nextQuantity);
                body.append('cart_key', cartKey);
                requestCart(`{{ url('/cart') }}/${productId}`, { method: 'PATCH', body });
            });

            openCartButton?.addEventListener('click', openDrawer);
            closeCartButton?.addEventListener('click', closeDrawer);
            overlay?.addEventListener('click', closeDrawer);
            renderCart();
        });
    </script>
</body>
</html>
