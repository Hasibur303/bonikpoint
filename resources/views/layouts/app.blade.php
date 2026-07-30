<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $seoTitle = html_entity_decode(trim($__env->yieldContent('title', config('app.name', 'Bonik Point'))), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $seoDescription = html_entity_decode(trim($__env->yieldContent('meta_description', 'Shop authentic lifestyle, vape, gadget, home, and daily-use products from Bonik Point with simple ordering and customer support.')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $seoCanonical = trim($__env->yieldContent('canonical', url()->current()));
        $seoImage = trim($__env->yieldContent('meta_image', asset('assets/images/logo.webp')));
        $seoRobots = trim($__env->yieldContent('robots', 'index,follow'));
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $seoCanonical }}">
    <meta property="og:site_name" content="Bonik Point">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <link href="{{ asset('favicon.jpg') }}" rel="shortcut icon" type="image/x-icon">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous"></noscript>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(request()->routeIs('home.index'))
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Bonik Point',
                'url' => route('home.index'),
                'logo' => asset('assets/images/logo.webp'),
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'telephone' => '01540381020',
                    'contactType' => 'customer service',
                    'areaServed' => 'BD',
                    'availableLanguage' => ['Bangla', 'English'],
                ],
                'sameAs' => [
                    'https://www.facebook.com/share/1EGD8CxUS9/',
                    'https://youtube.com/@dailyvlogsbynayeem',
                    'https://youtube.com/@nayeemrahmanvlogs',
                    'https://youtube.com/@budgetkoto',
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif
    @stack('schema')
</head>
<body class="bg-[#f3f5f4] font-sans text-gray-700 antialiased">
    <header class="sticky top-0 z-50 bg-[#f3f5f4]/95 py-3 backdrop-blur">
        <div class="container relative">
            <div class="flex h-16 items-center gap-2 rounded-lg bg-[#0f5555] px-3 text-white shadow-[0_12px_30px_rgba(9,50,51,0.16)] max-[420px]:gap-1.5 max-[420px]:px-2 sm:gap-3 sm:px-4">
                <button id="mobile-menu-button" type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-md text-white transition hover:bg-white/10 max-[420px]:h-9 max-[420px]:w-9 lg:hidden" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open menu</span>
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <a href="{{ route('home.index') }}" class="flex shrink-0 items-center gap-2.5 max-[420px]:gap-1.5" aria-label="Bonik Point home">
                    <span class="grid h-9 w-9 place-items-center rounded-md bg-white text-lg font-black text-ink shadow-sm max-[420px]:h-8 max-[420px]:w-8 max-[420px]:text-base sm:h-10 sm:w-10 sm:text-xl">B</span>
                    <span class="block">
                        <span class="block text-xs font-black uppercase leading-tight text-white max-[420px]:text-[11px] sm:text-base">Bonik Point</span>
                        <span class="hidden text-[9px] font-bold uppercase tracking-wide text-[#d5e77a] lg:block">Shop with confidence</span>
                    </span>
                </a>

                @php
                    $activeCategorySlug = request('category') ?: request()->route('category')?->slug;
                @endphp

                <nav class="hidden shrink-0 items-center gap-4 text-xs font-semibold uppercase tracking-wide text-gray-200 lg:flex xl:gap-5 xl:text-sm">
                    <a href="{{ route('shop.index') }}" class="border-b-2 py-2 transition {{ request()->routeIs('shop.*', 'home.index') ? 'border-[#c8dc62] text-white' : 'border-transparent hover:border-white/30 hover:text-white' }}">Shop</a>
                    <div class="group relative">
                        <button type="button" aria-haspopup="true" class="border-b-2 py-2 uppercase transition {{ $activeCategorySlug ? 'border-[#c8dc62] text-white' : 'border-transparent hover:border-white/30 hover:text-white' }}">
                            Categories
                        </button>
                        <div class="invisible absolute left-0 top-full z-50 w-64 pt-3 opacity-0 transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            <div class="max-h-[420px] overflow-y-auto rounded bg-white py-2 shadow-xl ring-1 ring-gray-100">
                                <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-sm normal-case tracking-normal text-ink hover:bg-gray-50 hover:text-primary">All Categories</a>
                                @foreach($headerCategories ?? [] as $category)
                                    @php
                                        $isCategoryActive = $activeCategorySlug === $category->slug;
                                        $hasActiveChild = $category->children->contains('slug', $activeCategorySlug);
                                    @endphp

                                    @if($category->children->isNotEmpty())
                                        <details class="border-t border-gray-50 first:border-t-0" {{ $isCategoryActive || $hasActiveChild ? 'open' : '' }}>
                                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-2 text-sm normal-case tracking-normal text-ink hover:bg-gray-50 hover:text-primary">
                                                <a href="{{ $category->public_url }}" class="{{ $isCategoryActive ? 'text-primary' : '' }}">{{ $category->name }}</a>
                                                <span class="text-xs text-gray-400">&#9656;</span>
                                            </summary>
                                            <div class="pb-1">
                                                @foreach($category->children as $child)
                                                    <a href="{{ $child->public_url }}" class="block px-7 py-2 text-sm normal-case tracking-normal {{ $activeCategorySlug === $child->slug ? 'bg-gray-50 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                                        {{ $child->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </details>
                                    @else
                                        <a href="{{ $category->public_url }}" class="block px-4 py-2 text-sm normal-case tracking-normal {{ $isCategoryActive ? 'bg-gray-50 text-primary' : 'text-ink hover:bg-gray-50 hover:text-primary' }}">
                                            {{ $category->name }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('videos.index') }}" class="border-b-2 py-2 transition {{ request()->routeIs('videos.*') ? 'border-[#c8dc62] text-white' : 'border-transparent hover:border-white/30 hover:text-white' }}">Videos</a>
                    <a href="{{ route('cart.index') }}" class="border-b-2 py-2 transition {{ request()->routeIs('cart.*') ? 'border-[#c8dc62] text-white' : 'border-transparent hover:border-white/30 hover:text-white' }}">Cart</a>
                    @guest
                        <a href="{{ route('guest.orders.track') }}" class="border-b-2 py-2 transition {{ request()->routeIs('guest.orders.track*') ? 'border-[#c8dc62] text-white' : 'border-transparent hover:border-white/30 hover:text-white' }}">Track</a>
                    @endguest
                    @auth
                        <a href="{{ route('orders.index') }}" class="border-b-2 py-2 transition {{ request()->routeIs('orders.*') ? 'border-[#c8dc62] text-white' : 'border-transparent hover:border-white/30 hover:text-white' }}">Orders</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.index') }}" class="border-b-2 border-transparent py-2 transition hover:border-white/30 hover:text-white">Admin</a>
                        @endif
                    @endauth
                </nav>

                <div class="ml-auto flex min-w-0 flex-1 items-center justify-end gap-2 sm:gap-3">
                    <form action="{{ route('shop.index') }}" class="hidden min-w-0 flex-1 md:block md:max-w-md">
                        <label class="relative block">
                            <span class="sr-only">Search products</span>
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                            <input name="search" value="{{ request('search') }}" placeholder="Search products, categories or brands" aria-label="Search products" class="h-10 w-full rounded-full border-white/10 bg-white pl-10 pr-4 text-sm text-ink shadow-sm placeholder:text-gray-400 focus:border-[#d5e77a] focus:ring-[#d5e77a]">
                        </label>
                    </form>

                    <button id="open-cart-drawer" type="button" aria-label="Open shopping cart" class="relative grid h-10 w-10 shrink-0 place-items-center rounded-full bg-white text-ink shadow-sm transition hover:bg-[#edf3f1]" title="Cart">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span id="cart-count-badge" class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-[#d5e77a] px-1 text-[10px] font-black text-ink ring-2 ring-[#0f5555]">{{ $drawerCart['count'] ?? 0 }}</span>
                    </button>

                    <a href="tel:01540381020" aria-label="Call Bonik Point customer service" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition hover:bg-white hover:text-ink" title="Call customer service">
                        <i class="fa-solid fa-phone"></i>
                    </a>

                    @auth
                        <div class="group relative">
                            <button type="button" aria-label="Open customer account menu" aria-haspopup="true" class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#d5e77a] text-ink transition hover:bg-white" title="Account">
                                <i class="fa-regular fa-user"></i>
                            </button>
                            <div class="invisible absolute right-0 top-full z-50 w-48 pt-2 text-ink opacity-0 transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                                <div class="rounded bg-white py-2 text-ink shadow-xl ring-1 ring-gray-100">
                                    <div class="px-4 py-2 text-xs font-semibold uppercase text-gray-400">{{ auth()->user()->name }}</div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-semibold text-ink hover:bg-[#edf3f1] hover:text-primary">Profile</a>
                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm font-semibold text-ink hover:bg-[#edf3f1] hover:text-primary">My Orders</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm font-semibold text-ink hover:bg-red-50 hover:text-red-700">Sign Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex h-10 shrink-0 items-center justify-center whitespace-nowrap rounded-full bg-[#d5e77a] px-3.5 text-xs font-black text-ink shadow-[0_4px_0_rgba(7,27,31,0.16)] transition hover:bg-white sm:px-5 sm:text-sm">Sign In</a>
                    @endauth
                </div>
            </div>

            <div id="mobile-menu" class="absolute left-4 right-4 top-full mt-2 hidden max-h-[calc(100vh-7rem)] overflow-y-auto rounded-lg border border-gray-200 bg-white p-4 text-ink shadow-[0_20px_45px_rgba(15,45,47,0.18)]">
                <form action="{{ route('shop.index') }}" class="mb-4 md:hidden">
                    <input name="search" value="{{ request('search') }}" placeholder="Search products, categories or brands" aria-label="Search products" class="w-full rounded-full border-gray-200 bg-[#f7f9f8] text-sm text-ink focus:border-primary focus:ring-primary">
                </form>

                <nav class="grid gap-2 text-sm font-semibold uppercase tracking-wide text-ink sm:grid-cols-2 lg:grid-cols-4">
                    <a href="{{ route('shop.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('shop.*', 'home.index') ? 'bg-ink text-white' : 'hover:bg-[#edf3f1] hover:text-primary' }}">Shop</a>
                    <div class="rounded-md border border-gray-200 p-2 sm:col-span-2 lg:col-span-4">
                        <p class="px-1 pb-2 text-xs font-bold text-gray-400">Categories</p>
                        <div class="grid gap-1 normal-case tracking-normal">
                            <a href="{{ route('shop.index') }}" class="rounded-md px-3 py-2 text-sm {{ $activeCategorySlug ? 'hover:bg-[#edf3f1] hover:text-primary' : 'bg-primary text-white' }}">All Categories</a>
                            @foreach($headerCategories ?? [] as $category)
                                @php
                                    $isCategoryActive = $activeCategorySlug === $category->slug;
                                    $hasActiveChild = $category->children->contains('slug', $activeCategorySlug);
                                @endphp

                                @if($category->children->isNotEmpty())
                                    <details class="rounded-md border border-gray-100" {{ $isCategoryActive || $hasActiveChild ? 'open' : '' }}>
                                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 rounded-md px-3 py-2 text-sm {{ $isCategoryActive ? 'bg-primary text-white' : 'hover:bg-[#edf3f1] hover:text-primary' }}">
                                            <a href="{{ $category->public_url }}">{{ $category->name }}</a>
                                            <span class="text-xs {{ $isCategoryActive ? 'text-white' : 'text-gray-400' }}">&#9656;</span>
                                        </summary>
                                        <div class="grid gap-1 px-2 pb-2 pt-1">
                                            @foreach($category->children as $child)
                                                <a href="{{ $child->public_url }}" class="rounded-md px-4 py-2 text-sm {{ $activeCategorySlug === $child->slug ? 'bg-primary text-white' : 'text-gray-600 hover:bg-[#edf3f1] hover:text-primary' }}">
                                                    {{ $child->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    <a href="{{ $category->public_url }}" class="rounded-md px-3 py-2 text-sm {{ $isCategoryActive ? 'bg-primary text-white' : 'hover:bg-[#edf3f1] hover:text-primary' }}">
                                        {{ $category->name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('videos.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('videos.*') ? 'bg-ink text-white' : 'hover:bg-[#edf3f1] hover:text-primary' }}">Videos</a>
                    <a href="{{ route('cart.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('cart.*') ? 'bg-ink text-white' : 'hover:bg-[#edf3f1] hover:text-primary' }}">Cart</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-ink text-white' : 'hover:bg-[#edf3f1] hover:text-primary' }}">Dashboard</a>
                        <a href="{{ route('orders.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('orders.*') ? 'bg-ink text-white' : 'hover:bg-[#edf3f1] hover:text-primary' }}">Orders</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.index') }}" class="rounded-md px-3 py-2 hover:bg-[#edf3f1] hover:text-primary">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md px-3 py-2 text-left hover:bg-red-50 hover:text-red-700">Sign Out</button>
                        </form>
                    @else
                        <a href="{{ route('guest.orders.track') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('guest.orders.track*') ? 'bg-ink text-white' : 'hover:bg-[#edf3f1] hover:text-primary' }}">Track Order</a>
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 hover:bg-[#edf3f1] hover:text-primary">Sign In</a>
                        <a href="{{ route('register') }}" class="rounded-md px-3 py-2 hover:bg-[#edf3f1] hover:text-primary">Register</a>
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
                <a href="{{ route(auth()->check() ? 'checkout.create' : 'guest.checkout.create') }}" id="cart-drawer-checkout" class="rounded-lg bg-primary px-5 py-3 text-center font-semibold text-white hover:bg-ink">Place Order</a>
                <a href="{{ route('shop.index') }}" class="rounded-lg border border-gray-200 px-5 py-3 text-center font-semibold text-ink hover:border-primary hover:text-primary">Continue Shopping</a>
            </div>
        </div>
    </aside>

    <footer class="mt-6 bg-ink py-4 text-gray-100 md:mt-16 md:py-12">
        <div class="container grid grid-cols-2 gap-3 md:grid-cols-5 md:gap-8">
            <div class="col-span-2 md:col-span-2">
                <img src="{{ asset('assets/images/logo.webp') }}" alt="Bonik Point" width="160" height="80" loading="lazy" decoding="async" class="mb-2 h-10 w-auto rounded bg-white p-1 md:mb-5 md:h-20 md:p-2">
                <p class="hidden max-w-lg text-sm leading-6 text-gray-300 md:block">Bonik Point brings stylish products and simple ordering in one place.</p>
            </div>
            <div>
                <h3 class="mb-2 text-sm font-bold text-white md:mb-4 md:text-base">Shop</h3>
                <div class="space-y-1 text-[11px] md:space-y-2 md:text-sm">
                    <a href="{{ route('shop.index') }}" class="block hover:text-accent">All Products</a>
                    <a href="{{ route('cart.index') }}" class="block hover:text-accent">Cart</a>
                    <a href="{{ route('videos.index') }}" class="block hover:text-accent">Videos</a>
                    @auth
                        <a href="{{ route('orders.index') }}" class="block hover:text-accent">Orders</a>
                    @else
                        <a href="{{ route('guest.orders.track') }}" class="block hover:text-accent">Track Order</a>
                    @endauth
                    <a href="{{ route('order-instructions') }}" class="block hover:text-accent">Order Instructions</a>
                    <a href="{{ route('return-policy') }}" class="block hover:text-accent">Return & Refund Policy</a>
                </div>
            </div>
            <div>
                <h3 class="mb-2 text-sm font-bold text-white md:mb-4 md:text-base">Contact</h3>
                <p class="text-[11px] leading-4 text-gray-300 md:text-sm md:leading-5">Shimrail Zero point, Siddirganj, Narayanganj</p>
                <p class="mt-1 text-[11px] text-gray-300 md:text-sm">WhatsApp: 01540381020</p>
                <p class="mt-1 text-[11px] text-gray-300 md:text-sm"><span class="md:hidden">Service:</span><span class="hidden md:inline">24 Hours Customer Service:</span> 01540381020</p>
            </div>
            <div class="col-span-2 md:col-span-1">
                <h3 class="mb-2 text-sm font-bold text-white md:mb-4 md:text-base">Follow</h3>
                <div class="grid grid-cols-3 gap-2 text-[11px] md:block md:space-y-3 md:text-sm">
                    <a href="https://www.facebook.com/share/1EGD8CxUS9/" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-gray-300 hover:text-accent" title="Facebook">
                        <i class="fa-brands fa-facebook-f w-4 text-sm md:text-base"></i>
                        <span class="truncate">Facebook</span>
                    </a>
                    <a href="https://youtube.com/@dailyvlogsbynayeem?si=FjjidXNIp0SrnIfI" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-gray-300 hover:text-accent" title="Daily Vlogs by Nayeem on YouTube">
                        <i class="fa-brands fa-youtube w-4 text-sm md:text-base"></i>
                        <span class="truncate">Daily Vlogs</span>
                    </a>
                    <a href="https://youtube.com/@nayeemrahmanvlogs?si=Usa-_Z8h8J_p32JP" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-gray-300 hover:text-accent" title="Nayeem Rahman Vlogs on YouTube">
                        <i class="fa-brands fa-youtube w-4 text-sm md:text-base"></i>
                        <span class="truncate">Nayeem Vlogs</span>
                    </a>
                    <a href="https://youtube.com/@budgetkoto?si=cO3IVqaCdoG3aNJa" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-gray-300 hover:text-accent" title="Budget Koto on YouTube">
                        <i class="fa-brands fa-youtube w-4 text-sm md:text-base"></i>
                        <span class="truncate">Budget Koto</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="container mt-4 border-t border-white/15 pt-3 text-center text-[10px] text-gray-400 md:mt-10 md:pt-5 md:text-xs">
            &copy; {{ now()->year }} Bonik Point. All rights reserved.
        </div>
    </footer>
    <div id="global-age-warning" class="fixed inset-0 z-[130] hidden bg-ink/90 px-4 py-6 backdrop-blur">
        <div class="flex min-h-full items-center justify-center">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 text-center shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-wide text-primary">Age Restricted Product</p>
                <h2 class="mt-2 text-3xl font-black text-ink">Are you 18 or older?</h2>
                <p class="mt-4 leading-7 text-gray-600">This product is intended for adult users only. Please confirm your age before adding vape products to cart.</p>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <button id="global-confirm-age" type="button" class="rounded bg-primary px-5 py-3 font-semibold text-white hover:bg-ink">Yes, I am 18+</button>
                    <button id="global-cancel-age" type="button" class="rounded border border-gray-200 px-5 py-3 font-semibold text-ink hover:border-primary hover:text-primary">No, cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');

            if (!button || !menu) {
                return;
            }

            const closeMenu = () => {
                menu.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
            };

            button.addEventListener('click', function (event) {
                event.stopPropagation();
                const isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden', isOpen);
                button.setAttribute('aria-expanded', String(!isOpen));
            });

            menu.addEventListener('click', (event) => event.stopPropagation());
            document.addEventListener('click', closeMenu);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMenu();
                }
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
            const ageWarning = document.getElementById('global-age-warning');
            const confirmAgeButton = document.getElementById('global-confirm-age');
            const cancelAgeButton = document.getElementById('global-cancel-age');
            let pendingAgeForm = null;
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

            const openAgeWarning = (form) => {
                pendingAgeForm = form;
                ageWarning?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeAgeWarning = () => {
                pendingAgeForm = null;
                ageWarning?.classList.add('hidden');
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
                    <div class="mb-4 grid grid-cols-[64px_minmax(0,1fr)] gap-3 rounded-lg border border-gray-100 p-3 sm:grid-cols-[72px_minmax(0,1fr)] sm:gap-4" data-cart-item="${escapeHtml(item.key)}">
                        <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" width="80" height="80" loading="lazy" decoding="async" class="h-16 w-16 rounded-md object-cover sm:h-20 sm:w-20">
                        <div class="min-w-0">
                            <div class="flex items-start gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-black leading-5 text-ink sm:truncate sm:text-base sm:font-semibold">${escapeHtml(item.name)}</p>
                                    ${item.festival_title ? `<p class="text-xs font-bold uppercase tracking-wide text-accent">${escapeHtml(item.festival_title)}</p>` : ''}
                                    ${item.product_color_name ? `<p class="mt-1 flex items-center gap-1.5 text-xs font-semibold text-gray-500"><span class="h-3 w-3 rounded-full border border-black/10" style="background-color: ${escapeHtml(item.product_color_hex || '#E5E7EB')}"></span>Color: ${escapeHtml(item.product_color_name)}</p>` : ''}
                                    ${item.product_flavor_name ? `<p class="mt-1 text-xs font-semibold text-gray-500">Flavor: ${escapeHtml(item.product_flavor_name)}</p>` : ''}
                                    <p class="text-xs font-semibold text-gray-500 sm:text-sm">${money(item.price)}</p>
                                </div>
                                <button type="button" class="js-cart-remove grid h-8 w-8 shrink-0 place-items-center rounded-md bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-700 sm:w-auto sm:bg-transparent sm:text-sm sm:font-semibold" data-product-id="${item.id}" data-cart-key="${escapeHtml(item.key)}" title="Remove" aria-label="Remove ${escapeHtml(item.name)}">
                                    <span class="sr-only sm:not-sr-only">Remove</span>
                                    <i class="fa-solid fa-trash text-xs sm:hidden"></i>
                                </button>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center overflow-hidden rounded border border-gray-200">
                                    <button type="button" class="js-cart-decrease px-2.5 py-1 text-lg sm:px-3" data-product-id="${item.id}" data-cart-key="${escapeHtml(item.key)}" data-quantity="${item.quantity}">-</button>
                                    <span class="min-w-8 px-2 text-center text-sm font-semibold sm:min-w-10 sm:px-3">${item.quantity}</span>
                                    <button type="button" class="js-cart-increase px-2.5 py-1 text-lg sm:px-3" data-product-id="${item.id}" data-cart-key="${escapeHtml(item.key)}" data-quantity="${item.quantity}" data-stock="${item.stock}">+</button>
                                </div>
                                <p class="text-sm font-black text-primary sm:text-base">${money(item.total)}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
            };

            const requestCart = async (url, options = {}) => {
                try {
                    const response = await fetch(url, {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            ...(options.headers || {}),
                        },
                        ...options,
                    });
                    const contentType = response.headers.get('content-type') || '';
                    const data = contentType.includes('application/json') ? await response.json() : {};

                    if (response.status === 419) {
                        alert('Your session expired. The page will refresh, then please add the product again.');
                        window.location.reload();
                        return;
                    }

                    if (response.status === 428 && data.requires_age_verification) {
                        openAgeWarning(options.form || null);
                        return;
                    }

                    if (!response.ok) {
                        alert(data.message || 'Could not add this product. Please refresh the page and try again.');
                        return;
                    }

                    cartState = data.cart;
                    renderCart();
                    openDrawer();
                } catch (error) {
                    alert('Could not connect to the cart. Please refresh the page and try again.');
                }
            };

            document.querySelectorAll('.js-add-to-cart').forEach((form) => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    requestCart(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        form,
                    });
                });
            });

            confirmAgeButton?.addEventListener('click', async function () {
                const form = pendingAgeForm;

                await fetch('{{ route('cart.confirm-age') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                closeAgeWarning();

                if (form) {
                    requestCart(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                    });
                }
            });

            cancelAgeButton?.addEventListener('click', closeAgeWarning);

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
