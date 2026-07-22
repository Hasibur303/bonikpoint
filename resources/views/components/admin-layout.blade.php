<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Workspace | Bonik Point</title>
    <link href="{{ asset('favicon.jpg') }}" rel="shortcut icon" type="image/x-icon">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f2f5f4] font-sans text-[#405154] antialiased">
    @php
        $adminNavigation = [
            [
                'label' => 'Overview',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'admin.index', 'pattern' => 'admin.index', 'icon' => 'fa-table-columns'],
                ],
            ],
            [
                'label' => 'Commerce',
                'items' => [
                    ['label' => 'Products', 'route' => 'admin.products.index', 'pattern' => 'admin.products.*', 'icon' => 'fa-box-open'],
                    ['label' => 'Categories', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*', 'icon' => 'fa-layer-group'],
                    ['label' => 'Orders', 'route' => 'admin.orders.index', 'pattern' => 'admin.orders.*', 'icon' => 'fa-receipt'],
                    ['label' => 'Reviews', 'route' => 'admin.reviews.index', 'pattern' => 'admin.reviews.*', 'icon' => 'fa-star-half-stroke'],
                    ['label' => 'Profit Report', 'route' => 'admin.profit.index', 'pattern' => 'admin.profit.*', 'icon' => 'fa-chart-line'],
                ],
            ],
            [
                'label' => 'Growth',
                'items' => [
                    ['label' => 'Festival Offers', 'route' => 'admin.festivals.index', 'pattern' => 'admin.festivals.*', 'icon' => 'fa-tags'],
                    ['label' => 'Customers', 'route' => 'admin.users.index', 'pattern' => 'admin.users.index', 'icon' => 'fa-users'],
                    ['label' => 'Administrators', 'route' => 'admin.users.admins', 'pattern' => 'admin.users.admins*', 'icon' => 'fa-user-shield'],
                ],
            ],
            [
                'label' => 'System',
                'items' => [
                    ['label' => 'Store Settings', 'route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*', 'icon' => 'fa-sliders'],
                    ['label' => 'Account Settings', 'route' => 'profile.edit', 'pattern' => 'profile.*', 'icon' => 'fa-user-shield'],
                ],
            ],
        ];
    @endphp

    <div id="admin-sidebar-overlay" class="fixed inset-0 z-40 hidden bg-[#071f22]/55 backdrop-blur-sm lg:hidden"></div>

    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-[280px] -translate-x-full flex-col bg-[#0d3f43] text-white shadow-[18px_0_45px_rgba(12,48,51,0.16)] transition-transform duration-300 lg:translate-x-0">
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">
            <a href="{{ route('admin.index') }}" class="flex min-w-0 items-center gap-3" aria-label="Bonik Point admin dashboard">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-white text-xl font-black text-ink">B</span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-black uppercase text-white">Bonik Point</span>
                    <span class="block text-[10px] font-bold uppercase tracking-wide text-[#d5e77a]">Admin Workspace</span>
                </span>
            </a>
            <button id="admin-menu-close" type="button" class="grid h-9 w-9 place-items-center rounded-md text-white/70 transition hover:bg-white/10 hover:text-white lg:hidden" aria-label="Close admin menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="min-h-0 flex-1 overflow-y-auto px-4 py-5" aria-label="Admin navigation">
            @foreach($adminNavigation as $section)
                <div class="mb-5 last:mb-0">
                    <p class="mb-2 px-3 text-[10px] font-black uppercase tracking-wide text-white/40">{{ $section['label'] }}</p>
                    <div class="space-y-1">
                        @foreach($section['items'] as $item)
                            @php($isActive = request()->routeIs($item['pattern']))
                            <a href="{{ route($item['route']) }}" class="group flex h-11 items-center gap-3 rounded-md px-3 text-sm font-bold transition {{ $isActive ? 'bg-white text-ink shadow-[0_8px_22px_rgba(5,28,30,0.18)]' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md {{ $isActive ? 'bg-[#eaf2ad] text-ink' : 'bg-white/5 text-white/60 group-hover:bg-white/10 group-hover:text-white' }}">
                                    <i class="fa-solid {{ $item['icon'] }} text-xs"></i>
                                </span>
                                <span>{{ $item['label'] }}</span>
                                @if($isActive)
                                    <span class="ml-auto h-1.5 w-1.5 rounded-full bg-primary"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3 rounded-md bg-white/[0.06] p-3">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#d5e77a] text-sm font-black uppercase text-ink">
                    {{ str(auth()->user()->name ?? 'A')->substr(0, 1) }}
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-bold text-white">{{ auth()->user()->name ?? 'Administrator' }}</span>
                    <span class="block truncate text-[11px] text-white/45">Administrator</span>
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('profile.edit') }}" class="flex h-10 items-center justify-center gap-2 rounded-md border border-white/10 text-xs font-bold text-white/70 transition hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-user-shield"></i>
                    Account
                </a>
                <a href="{{ route('home.index') }}" target="_blank" rel="noopener noreferrer" class="flex h-10 items-center justify-center gap-2 rounded-md border border-white/10 text-xs font-bold text-white/70 transition hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    Store
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex h-10 w-full items-center justify-center gap-2 rounded-md border border-white/10 text-xs font-bold text-white/70 transition hover:border-red-300/30 hover:bg-red-400/10 hover:text-red-100">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-[280px]">
        <header class="sticky top-0 z-30 border-b border-[#dbe4e2] bg-white/90 backdrop-blur-xl">
            <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:h-20 lg:px-8 xl:px-10">
                <div class="flex min-w-0 items-center gap-3">
                    <button id="admin-menu-button" type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-md border border-[#dbe4e2] bg-white text-ink shadow-sm lg:hidden" aria-controls="admin-sidebar" aria-expanded="false">
                        <span class="sr-only">Open admin menu</span>
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-black text-ink sm:text-base">Store Operations</p>
                        <p class="hidden text-xs text-gray-500 sm:block">Manage catalog, customers, and fulfillment</p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <p class="hidden text-right lg:block">
                        <span class="block text-[10px] font-black uppercase tracking-wide text-gray-400">Today</span>
                        <span class="block text-xs font-bold text-ink">{{ now()->format('d M Y') }}</span>
                    </p>
                    <a href="{{ route('admin.products.create') }}" class="hidden h-10 items-center gap-2 rounded-md bg-ink px-4 text-xs font-black text-white shadow-[0_8px_20px_rgba(16,63,68,0.14)] transition hover:bg-primary sm:inline-flex">
                        <i class="fa-solid fa-plus"></i>
                        Add Product
                    </a>
                    <a href="{{ route('home.index') }}" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-md border border-[#dbe4e2] bg-white text-ink transition hover:border-primary hover:text-primary" aria-label="View storefront" title="View storefront">
                        <i class="fa-solid fa-store text-sm"></i>
                    </a>
                </div>
            </div>
        </header>

        <main class="admin-content px-4 py-5 sm:px-6 sm:py-7 lg:px-8 lg:py-8 xl:px-10">
            <div class="mx-auto w-full max-w-[1500px]">
                @if(session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm" role="status">
                        <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-600 text-[10px] text-white"><i class="fa-solid fa-check"></i></span>
                        <span class="pt-0.5 font-semibold">{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm" role="alert">
                        @foreach($errors->all() as $error)
                            <p class="font-semibold">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                {{ $slot }}
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('admin-menu-button');
            const closeButton = document.getElementById('admin-menu-close');
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('admin-sidebar-overlay');

            if (!button || !sidebar || !overlay) return;

            const setSidebarAccessibility = (isVisible) => {
                sidebar.toggleAttribute('inert', !isVisible);
                sidebar.setAttribute('aria-hidden', String(!isVisible));
            };

            const openMenu = () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');
                setSidebarAccessibility(true);
            };

            const closeMenu = () => {
                if (window.innerWidth >= 1024) return;
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');
                setSidebarAccessibility(false);
            };

            button.addEventListener('click', openMenu);
            closeButton?.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeMenu();
            });
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    button.setAttribute('aria-expanded', 'false');
                    setSidebarAccessibility(true);
                } else if (button.getAttribute('aria-expanded') !== 'true') {
                    setSidebarAccessibility(false);
                }
            });

            setSidebarAccessibility(window.innerWidth >= 1024);
        });
    </script>
</body>
</html>
