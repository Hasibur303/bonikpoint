@php
    $customer = auth()->user();
    $initials = collect(preg_split('/\s+/', trim($customer->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->join('');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>My Account | Bonik Point</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4f5f7] text-[#4b5563] antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[272px_minmax(0,1fr)]">
        <header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 lg:hidden">
            <a href="{{ route('shop.index') }}" class="flex items-center gap-2.5" aria-label="Bonik Point shop">
                <span class="grid h-9 w-9 place-items-center rounded-md bg-ink text-lg font-black text-white">B</span>
                <span class="text-base font-black uppercase text-ink">Bonik Point</span>
            </a>
            <button id="dashboard-menu-button" type="button" class="grid h-10 w-10 place-items-center rounded-md border border-gray-200 bg-[#f7f8f9] text-ink hover:border-primary hover:text-primary" aria-controls="dashboard-sidebar" aria-expanded="false">
                <span class="sr-only">Open account menu</span>
                <i class="fa-solid fa-bars"></i>
            </button>
        </header>

        <div id="dashboard-menu-backdrop" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

        <aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-[272px] -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
            <div class="flex h-20 items-center justify-between border-b border-gray-100 px-6">
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3" aria-label="Bonik Point shop">
                    <span class="grid h-10 w-10 place-items-center rounded-md bg-ink text-xl font-black text-white">B</span>
                    <span>
                        <span class="block text-base font-black uppercase leading-tight text-ink">Bonik Point</span>
                        <span class="block text-[10px] font-bold uppercase tracking-wide text-primary">Customer Account</span>
                    </span>
                </a>
                <button id="dashboard-menu-close" type="button" class="grid h-9 w-9 place-items-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-ink lg:hidden" aria-label="Close account menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="mx-4 mt-5 rounded-lg bg-ink p-4 text-white">
                <div class="flex items-center gap-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-accent text-sm font-black text-ink">{{ $initials ?: 'BP' }}</span>
                    <div class="min-w-0">
                        <p class="truncate font-black">{{ $customer->name }}</p>
                        <p class="truncate text-xs text-gray-300">{{ $customer->email }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-5 text-sm">
                <p class="px-3 text-[10px] font-black uppercase tracking-wide text-gray-400">Account</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('orders.index') }}" class="flex items-center gap-3 rounded-md border-l-2 px-3 py-2.5 font-bold transition {{ request()->routeIs('orders.*') ? 'border-primary bg-[#eaf5f3] text-primary' : 'border-transparent text-gray-600 hover:bg-gray-100 hover:text-ink' }}">
                        <i class="fa-solid fa-box-archive w-5 text-center"></i>
                        My Orders
                    </a>
                    <a href="{{ route('cart.index') }}" class="flex items-center gap-3 rounded-md border-l-2 px-3 py-2.5 font-bold transition {{ request()->routeIs('cart.*') ? 'border-primary bg-[#eaf5f3] text-primary' : 'border-transparent text-gray-600 hover:bg-gray-100 hover:text-ink' }}">
                        <i class="fa-solid fa-bag-shopping w-5 text-center"></i>
                        <span class="flex-1">My Cart</span>
                        @if(\App\Http\Controllers\CartController::count() > 0)
                            <span class="grid h-5 min-w-5 place-items-center rounded bg-ink px-1 text-[10px] font-black text-white">{{ \App\Http\Controllers\CartController::count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-md border-l-2 px-3 py-2.5 font-bold transition {{ request()->routeIs('profile.*') ? 'border-primary bg-[#eaf5f3] text-primary' : 'border-transparent text-gray-600 hover:bg-gray-100 hover:text-ink' }}">
                        <i class="fa-solid fa-user w-5 text-center"></i>
                        Profile & Security
                    </a>
                </div>

                <p class="mt-7 px-3 text-[10px] font-black uppercase tracking-wide text-gray-400">Store</p>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('shop.index') }}" class="flex items-center gap-3 rounded-md border-l-2 border-transparent px-3 py-2.5 font-bold text-gray-600 transition hover:bg-gray-100 hover:text-ink">
                        <i class="fa-solid fa-store w-5 text-center"></i>
                        Continue Shopping
                    </a>
                    <a href="{{ route('order-instructions') }}" class="flex items-center gap-3 rounded-md border-l-2 border-transparent px-3 py-2.5 font-bold text-gray-600 transition hover:bg-gray-100 hover:text-ink">
                        <i class="fa-solid fa-circle-info w-5 text-center"></i>
                        Order Instructions
                    </a>
                    <a href="tel:01540381020" class="flex items-center gap-3 rounded-md border-l-2 border-transparent px-3 py-2.5 font-bold text-gray-600 transition hover:bg-gray-100 hover:text-ink">
                        <i class="fa-solid fa-headset w-5 text-center"></i>
                        Customer Service
                    </a>
                </div>
            </nav>

            <div class="border-t border-gray-100 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm font-bold text-gray-600 hover:bg-red-50 hover:text-red-700">
                        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="hidden h-20 items-center justify-between border-b border-gray-200 bg-white px-8 lg:flex">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wide text-primary">Bonik Point</p>
                    <p class="text-sm font-bold text-ink">My account</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('shop.index') }}" class="inline-flex h-10 items-center gap-2 rounded-md border border-gray-200 bg-white px-4 text-sm font-bold text-ink hover:border-primary hover:text-primary">
                        <i class="fa-solid fa-store"></i>
                        Shop
                    </a>
                    <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-md bg-ink text-white hover:bg-primary" aria-label="Open cart">
                        <i class="fa-solid fa-bag-shopping"></i>
                        @if(\App\Http\Controllers\CartController::count() > 0)
                            <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded bg-accent px-1 text-[10px] font-black text-ink">{{ \App\Http\Controllers\CartController::count() }}</span>
                        @endif
                    </a>
                </div>
            </header>

            <main class="min-h-[calc(100vh-4rem)] bg-[#f4f5f7] p-4 sm:p-6 lg:min-h-[calc(100vh-5rem)] lg:p-8 xl:p-10">
                <div class="mx-auto max-w-6xl">
                    @if(session('success') || session('error'))
                        <div class="mb-6 flex items-start gap-3 rounded-lg border p-4 text-sm shadow-sm {{ session('success') ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' }}">
                            <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} mt-0.5"></i>
                            <p class="font-semibold">{{ session('success') ?? session('error') }}</p>
                        </div>
                    @endif

                    @if(!request()->routeIs('orders.delivery-payment') && ($unpaidDeliveryOrders ?? collect())->isNotEmpty())
                        <div class="mb-6 overflow-hidden rounded-lg border border-[#ecd59f] bg-[#fff9e9] shadow-sm">
                            <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                                <div class="flex gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-[#8a681b] text-white"><i class="fa-solid fa-clock"></i></span>
                                    <div>
                                        <p class="font-black text-[#4f3d13]">Delivery payment is waiting</p>
                                        <p class="mt-1 text-sm leading-5 text-[#745f2d]">You have {{ $unpaidDeliveryOrders->count() }} order{{ $unpaidDeliveryOrders->count() > 1 ? 's' : '' }} awaiting advance delivery charge.</p>
                                    </div>
                                </div>
                                <a href="{{ route('orders.delivery-payment', $unpaidDeliveryOrders->first()) }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-ink px-5 text-sm font-black text-white hover:bg-primary">
                                    Pay Now
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openButton = document.getElementById('dashboard-menu-button');
            const closeButton = document.getElementById('dashboard-menu-close');
            const sidebar = document.getElementById('dashboard-sidebar');
            const backdrop = document.getElementById('dashboard-menu-backdrop');

            if (!openButton || !sidebar || !backdrop) {
                return;
            }

            const openMenu = () => {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                openButton.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');
            };

            const closeMenu = () => {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                openButton.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');
            };

            openButton.addEventListener('click', openMenu);
            closeButton?.addEventListener('click', closeMenu);
            backdrop.addEventListener('click', closeMenu);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });
        });
    </script>
</body>
</html>
