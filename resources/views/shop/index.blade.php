@php
    $currentPage = $products->currentPage();
    $hasFilterParameters = collect([$search, $minPrice, $maxPrice, $sort])->contains(fn ($value) => filled($value));
    $shopHeading = $selectedCategoryModel?->name
        ?? ($selectedBrand ? $selectedBrand.' Products' : 'Bonik Point Products');
    $categoryDescription = $selectedCategoryModel?->seo_description ?: $selectedCategoryModel?->description;
    $shopDescription = $categoryDescription
        ?: ($selectedCategoryModel
            ? 'Browse '.$selectedCategoryModel->name.' products at Bonik Point with simple ordering, customer support, and reliable delivery options in Bangladesh.'
            : ($selectedBrand
                ? 'Shop authentic '.$selectedBrand.' products at Bonik Point with delivery and customer support across Bangladesh.'
                : 'Shop Bonik Point products including vape items, gadgets, kitchen essentials, toys, fashion, and daily-use collections with simple ordering in Bangladesh.'));
    $shopTitle = $selectedCategoryModel
        ? ($selectedCategoryModel->seo_title ?: $selectedCategoryModel->name.' Products in Bangladesh | Bonik Point')
        : ($selectedBrand
            ? $selectedBrand.' Products in Bangladesh | Bonik Point'
            : (request()->routeIs('home.index') ? 'Bonik Point Store | Shop Products in Bangladesh' : 'Shop Products in Bangladesh | Bonik Point'));
    $shopCanonical = $selectedCategoryModel
        ? $selectedCategoryModel->public_url
        : ($selectedBrand
            ? route('brands.show', Str::slug($selectedBrand))
            : route(request()->routeIs('home.index') ? 'home.index' : 'shop.index'));

    if (! $hasFilterParameters && $currentPage > 1) {
        $shopTitle .= ' - Page '.$currentPage;
        $shopDescription = 'Page '.$currentPage.'. '.$shopDescription;
        $shopCanonical .= '?page='.$currentPage;
    }

    $shopRobots = $hasFilterParameters || ($currentPage > 1 && $products->isEmpty())
        ? 'noindex,follow'
        : 'index,follow';

    $listingBreadcrumbItems = collect([
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => route('home.index'),
        ],
    ]);

    if (! request()->routeIs('home.index')) {
        $listingBreadcrumbItems->push([
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $selectedCategoryModel?->parent?->name ?? ($selectedBrand ?: 'Shop'),
            'item' => $selectedCategoryModel?->parent?->public_url ?? $shopCanonical,
        ]);

        if ($selectedCategoryModel?->parent) {
            $listingBreadcrumbItems->push([
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $selectedCategoryModel->name,
                'item' => $selectedCategoryModel->public_url,
            ]);
        }
    }
@endphp

@section('title', $shopTitle)
@section('meta_description', Str::limit(strip_tags($shopDescription), 155, ''))
@section('canonical', $shopCanonical)
@section('meta_image', asset('assets/images/logo.webp'))
@section('robots', $shopRobots)

@if($listingBreadcrumbItems->count() > 1)
    @push('schema')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $listingBreadcrumbItems->values()->all(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush
@endif

@if($festivals->isNotEmpty())
    @push('head')
        <link
            rel="preload"
            as="image"
            href="{{ $festivals->first()->banner_url }}"
            @if($festivals->first()->banner_srcset) imagesrcset="{{ $festivals->first()->banner_srcset }}" @endif
            imagesizes="(min-width: 1440px) 360px, (min-width: 768px) 25vw, 50vw"
            fetchpriority="high"
        >
    @endpush
@endif

<x-app-layout>
    @if($festivals->isNotEmpty())
        <section class="border-b border-[#d6e0dd] bg-[#e8eeec] py-3 md:py-4">
            <div class="container">
                <div id="festival-mosaic-viewport" class="festival-mosaic-viewport" aria-label="Festival offers">
                    <div id="festival-mosaic-track" class="festival-mosaic-track">
                        @for($copy = 0; $copy < 2; $copy++)
                            <div class="festival-mosaic" aria-hidden="{{ $copy === 1 ? 'true' : 'false' }}">
                                @foreach($festivals as $festival)
                                    @php
                                        $isPrimaryFestivalImage = $copy === 0 && $loop->first;
                                    @endphp
                                    <a href="{{ route('festivals.show', $festival) }}" class="festival-mosaic-card group" @if($copy === 1) tabindex="-1" @endif>
                                        <img
                                            src="{{ $festival->banner_url }}"
                                            @if($festival->banner_srcset) srcset="{{ $festival->banner_srcset }}" @endif
                                            sizes="(min-width: 1440px) 360px, (min-width: 768px) 25vw, 50vw"
                                            alt="{{ $copy === 0 ? $festival->title : '' }}"
                                            width="1200"
                                            height="1200"
                                            loading="{{ $isPrimaryFestivalImage ? 'eager' : 'lazy' }}"
                                            fetchpriority="{{ $isPrimaryFestivalImage ? 'high' : 'low' }}"
                                            decoding="async"
                                            draggable="false"
                                            onload="if (this.naturalWidth / Math.max(this.naturalHeight, 1) > 2.1) this.closest('.festival-mosaic-card')?.classList.add('is-wide')"
                                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.035]"
                                        >
                                    </a>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <style>
                .festival-mosaic-viewport {
                    overflow: hidden;
                    cursor: grab;
                    touch-action: pan-y;
                    user-select: none;
                    perspective: 1400px;
                    padding: 0.45rem 0.2rem 0.85rem;
                }

                .festival-mosaic-viewport.is-dragging {
                    cursor: grabbing;
                }

                .festival-mosaic-track {
                    display: flex;
                    width: max-content;
                    will-change: transform;
                }

                .festival-mosaic {
                    flex: 0 0 auto;
                    display: flex;
                    gap: 0.5rem;
                    padding-right: 0.5rem;
                }

                .festival-mosaic-card {
                    flex: 0 0 calc((100vw - 2.65rem) / 2);
                    position: relative;
                    display: block;
                    aspect-ratio: 1 / 1;
                    overflow: hidden;
                    border-radius: 0.45rem;
                    background: #071b1f;
                    border: 1px solid rgba(255, 255, 255, 0.65);
                    box-shadow: 0 16px 25px rgba(7, 27, 31, 0.18), 0 5px 10px rgba(7, 27, 31, 0.08);
                    transform: translate3d(0, 0, 0) scale(1);
                    transform-style: preserve-3d;
                    transition: transform 620ms cubic-bezier(0.22, 1, 0.36, 1);
                }

                .festival-mosaic-card::before {
                    content: '';
                    position: absolute;
                    inset: 0.4rem;
                    z-index: 1;
                    border: 1px solid rgba(255, 255, 255, 0.58);
                    border-radius: calc(0.45rem - 0.16rem);
                    box-shadow: inset 0 0 0 1px rgba(6, 46, 49, 0.12);
                    opacity: 0.72;
                    pointer-events: none;
                    transform: translateZ(18px);
                }

                .festival-mosaic-card img {
                    transform: translateZ(14px) scale(1.04);
                    transition: transform 620ms cubic-bezier(0.22, 1, 0.36, 1);
                }

                .festival-mosaic-card::after {
                    content: '';
                    position: absolute;
                    inset: -45%;
                    pointer-events: none;
                    background: linear-gradient(105deg, transparent 38%, rgba(255, 255, 255, 0.2) 50%, transparent 62%);
                    opacity: 0;
                    transform: translate3d(-55%, 0, 0) rotate(8deg);
                }

                .trending-products-viewport {
                    max-width: 100%;
                    overflow: hidden;
                    cursor: grab;
                    touch-action: pan-y;
                    user-select: none;
                }

                .trending-products-viewport.is-dragging {
                    cursor: grabbing;
                }

                .trending-products-track {
                    display: flex;
                    max-width: none;
                    width: max-content;
                    will-change: transform;
                }

                .trending-products-panel {
                    display: flex;
                    flex: 0 0 auto;
                    gap: 0.65rem;
                    padding-right: 0.65rem;
                }

                .trending-product-card {
                    flex: 0 0 clamp(8.7rem, 42vw, 10.5rem);
                }

                .trending-hot-icon {
                    position: relative;
                    display: inline-block;
                    width: 1.3rem;
                    height: 1.55rem;
                    isolation: isolate;
                }

                .trending-hot-icon::before {
                    content: '';
                    position: absolute;
                    z-index: -1;
                    inset: 24% 14% 3%;
                    border-radius: 50%;
                    background: rgba(255, 91, 16, 0.55);
                    filter: blur(0.32rem);
                    transform-origin: 50% 100%;
                    animation: trending-fire-glow 700ms ease-in-out infinite alternate;
                }

                .trending-fire-svg {
                    display: block;
                    width: 100%;
                    height: 100%;
                    overflow: visible;
                    filter: drop-shadow(0 2px 2px rgba(203, 45, 9, 0.35));
                }

                .trending-fire-outer,
                .trending-fire-middle,
                .trending-fire-core {
                    transform-box: fill-box;
                    transform-origin: 50% 100%;
                }

                .trending-fire-outer {
                    fill: url(#trending-fire-outer-gradient);
                    animation: trending-fire-outer-burn 760ms cubic-bezier(0.45, 0, 0.55, 1) infinite alternate;
                }

                .trending-fire-middle {
                    fill: url(#trending-fire-middle-gradient);
                    animation: trending-fire-middle-burn 540ms cubic-bezier(0.45, 0, 0.55, 1) 90ms infinite alternate;
                }

                .trending-fire-core {
                    fill: url(#trending-fire-core-gradient);
                    animation: trending-fire-core-burn 390ms ease-in-out 40ms infinite alternate;
                }

                .trending-fire-spark {
                    transform-box: fill-box;
                    transform-origin: center;
                    fill: #ffd83d;
                    opacity: 0;
                    animation: trending-fire-spark-rise 1.35s ease-out infinite;
                }

                .trending-fire-spark-two {
                    animation-delay: 0.52s;
                }

                @keyframes trending-fire-outer-burn {
                    0% { transform: skewX(-3deg) scaleX(0.96) scaleY(0.98); }
                    52% { transform: skewX(3.5deg) scaleX(1.035) scaleY(1.04); }
                    100% { transform: skewX(-1deg) scaleX(0.985) scaleY(1.015); }
                }

                @keyframes trending-fire-middle-burn {
                    0% { transform: translateX(-1px) skewX(-5deg) scale(0.93, 0.96); }
                    100% { transform: translateX(1px) skewX(5deg) scale(1.04, 1.055); }
                }

                @keyframes trending-fire-core-burn {
                    0% { transform: translateX(0.5px) skewX(4deg) scale(0.88, 0.94); opacity: 0.82; }
                    100% { transform: translateX(-0.5px) skewX(-4deg) scale(1.04, 1.08); opacity: 1; }
                }

                @keyframes trending-fire-glow {
                    0% {
                        transform: scale(0.8, 0.84);
                        opacity: 0.3;
                    }
                    100% {
                        transform: scale(1.08, 1.06);
                        opacity: 0.72;
                    }
                }

                @keyframes trending-fire-spark-rise {
                    0%, 25% { opacity: 0; transform: translate(0, 4px) scale(0.45); }
                    42% { opacity: 0.95; }
                    100% { opacity: 0; transform: translate(5px, -21px) scale(0.12); }
                }

                .festival-mosaic-viewport.is-animating .festival-mosaic-card {
                    transform: translate3d(0, 0, 0) scale(0.982);
                }

                .festival-mosaic-viewport.is-animating .festival-mosaic-card img {
                    transform: translateZ(10px) scale(1.025);
                }

                .festival-mosaic-viewport.is-animating .festival-mosaic-card::after {
                    animation: festival-card-sheen 680ms cubic-bezier(0.22, 1, 0.36, 1);
                }

                .festival-mosaic-viewport.is-dragging .festival-mosaic-card {
                    transform: translate3d(0, 0, 0) scale(0.975);
                    transition-duration: 160ms;
                }

                @keyframes festival-card-sheen {
                    0% {
                        opacity: 0;
                        transform: translate3d(-55%, 0, 0) rotate(8deg);
                    }
                    35% {
                        opacity: 0.65;
                    }
                    100% {
                        opacity: 0;
                        transform: translate3d(55%, 0, 0) rotate(8deg);
                    }
                }

                @media (min-width: 768px) {
                    .festival-mosaic-track {
                        width: max-content;
                    }

                    .festival-mosaic {
                        flex: 0 0 auto;
                        display: flex;
                        gap: 0.5rem;
                        padding-right: 0.5rem;
                    }

                    .festival-mosaic-card {
                        flex: 0 0 calc((min(100vw, 1440px) - 1.5rem) / 4);
                        border-radius: 0.5rem;
                    }

                    .trending-products-panel {
                        gap: 1rem;
                        padding-right: 1rem;
                    }

                    .trending-product-card {
                        flex-basis: 12.5rem;
                    }
                }

                @media (min-width: 1024px) {
                    .festival-mosaic-card {
                        transform: rotateX(1.5deg) rotateY(-2.5deg) translateZ(0);
                    }

                    .festival-mosaic-card:nth-child(even) {
                        transform: rotateX(1.5deg) rotateY(2.5deg) translateZ(0);
                    }

                    .festival-mosaic-card:hover {
                        transform: translateY(-9px) rotateX(-2deg) rotateY(5deg) translateZ(28px) scale(1.025);
                        box-shadow: 0 25px 34px rgba(7, 27, 31, 0.26), 0 8px 14px rgba(7, 27, 31, 0.11);
                        filter: saturate(1.05) brightness(1.02);
                        z-index: 2;
                    }

                    .festival-mosaic-card:nth-child(even):hover {
                        transform: translateY(-9px) rotateX(-2deg) rotateY(-5deg) translateZ(28px) scale(1.025);
                    }

                    .festival-mosaic-card:hover img {
                        transform: translateZ(32px) scale(1.095);
                    }

                    .trending-product-card {
                        flex-basis: 14.5rem;
                    }
                }

                @media (prefers-reduced-motion: reduce) {
                    .festival-mosaic-track,
                    .festival-mosaic-card {
                        transition: none !important;
                    }

                    .festival-mosaic-card::after {
                        animation: none !important;
                    }

                    .trending-hot-icon {
                        animation: none;
                    }

                    .trending-hot-icon::before,
                    .trending-fire-outer,
                    .trending-fire-middle,
                    .trending-fire-core,
                    .trending-fire-spark {
                        animation: none;
                    }
                }
            </style>
        </section>
    @endif

    <section class="border-b border-[#d6e0dd] bg-white py-4 lg:hidden">
        <div class="container">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="text-base font-black text-ink">Shop by Category</h2>
                <a href="{{ route('shop.index') }}" class="text-xs font-black text-primary">All Products</a>
            </div>
            <div class="grid grid-cols-2 gap-2">
                @foreach($categories as $category)
                    @php
                        $hasActiveChild = $category->children->contains(fn ($child) => $selectedCategory === $child->slug);
                        [$categoryIcon, $categoryTone] = match ($category->slug) {
                            'vape-accessories' => ['fa-solid fa-wind', 'bg-[#103f44] text-white'],
                            'electronics-gadgets' => ['fa-solid fa-microchip', 'bg-[#dceced] text-[#087c7f]'],
                            'fashion-accessories' => ['fa-solid fa-bag-shopping', 'bg-[#eef2d7] text-[#667711]'],
                            default => ['fa-solid fa-shapes', 'bg-gray-100 text-gray-600'],
                        };
                    @endphp

                    @if($category->children->isNotEmpty())
                        <details class="group/category rounded-md border border-[#dce5e2] bg-[#f9fbfa] shadow-sm transition open:border-primary open:bg-[#eef6f3]" {{ $hasActiveChild ? 'open' : '' }}>
                            <summary class="flex min-w-0 cursor-pointer list-none items-center gap-2.5 p-2.5">
                                @if($category->image)
                                    <img src="{{ $category->image_url }}" @if($category->image_srcset) srcset="{{ $category->image_srcset }}" sizes="44px" @endif alt="{{ $category->image_alt ?: $category->name.' category' }}" width="80" height="80" loading="lazy" decoding="async" class="h-11 w-11 shrink-0 rounded-md object-cover ring-1 ring-black/5">
                                @else
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md text-sm {{ $categoryTone }}"><i class="{{ $categoryIcon }}"></i></span>
                                @endif
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-black text-ink">{{ $category->name }}</span>
                                    <span class="mt-0.5 block text-[11px] font-semibold text-gray-500">{{ $category->children->count() }} {{ Str::plural('subcategory', $category->children->count()) }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition group-open/category:rotate-180"></i>
                            </summary>
                            <div class="space-y-1 border-t border-[#dce5e2] px-2.5 py-2">
                                @foreach($category->children as $child)
                                    <a href="{{ $child->public_url }}" class="block rounded px-2 py-1.5 text-xs font-bold {{ $selectedCategory === $child->slug ? 'bg-primary text-white' : 'text-gray-600 hover:bg-white hover:text-primary' }}">{{ $child->name }}</a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        <a href="{{ $category->public_url }}" class="flex min-w-0 items-center gap-2.5 rounded-md border border-[#dce5e2] bg-[#f9fbfa] p-2.5 shadow-sm transition hover:border-primary hover:bg-[#eef6f3]">
                            @if($category->image)
                                <img src="{{ $category->image_url }}" @if($category->image_srcset) srcset="{{ $category->image_srcset }}" sizes="44px" @endif alt="{{ $category->image_alt ?: $category->name.' category' }}" width="80" height="80" loading="lazy" decoding="async" class="h-11 w-11 shrink-0 rounded-md object-cover ring-1 ring-black/5">
                            @else
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md text-sm {{ $categoryTone }}"><i class="{{ $categoryIcon }}"></i></span>
                            @endif
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-black text-ink">{{ $category->name }}</span>
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    @if($featuredProducts->isNotEmpty())
        <section class="border-b border-[#d6e0dd] bg-[#f7faf9] py-5 sm:py-7">
            <div class="container">
                <div class="mb-4 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wide text-primary">Handpicked</p>
                        <h2 class="mt-1 flex items-center gap-2 text-xl font-black text-ink sm:text-2xl">
                            <span>Trending Products</span>
                            <span class="trending-hot-icon" aria-hidden="true">
                                <svg class="trending-fire-svg" viewBox="0 0 64 82" role="presentation">
                                    <defs>
                                        <linearGradient id="trending-fire-outer-gradient" x1="22" y1="78" x2="44" y2="5" gradientUnits="userSpaceOnUse">
                                            <stop offset="0" stop-color="#ff9b16"/>
                                            <stop offset="0.56" stop-color="#f4511e"/>
                                            <stop offset="1" stop-color="#c81d16"/>
                                        </linearGradient>
                                        <linearGradient id="trending-fire-middle-gradient" x1="32" y1="75" x2="32" y2="22" gradientUnits="userSpaceOnUse">
                                            <stop offset="0" stop-color="#ffd51f"/>
                                            <stop offset="0.62" stop-color="#ff8a16"/>
                                            <stop offset="1" stop-color="#ff5b15"/>
                                        </linearGradient>
                                        <linearGradient id="trending-fire-core-gradient" x1="31" y1="76" x2="33" y2="42" gradientUnits="userSpaceOnUse">
                                            <stop offset="0" stop-color="#fffde9"/>
                                            <stop offset="0.48" stop-color="#fff17a"/>
                                            <stop offset="1" stop-color="#ffd52d"/>
                                        </linearGradient>
                                    </defs>
                                    <path class="trending-fire-outer" d="M33 2C43 14 52 23 49 38C56 32 63 43 60 57C57 72 46 80 31 80C15 80 4 70 4 56C4 46 10 38 16 30C15 40 21 43 25 45C21 28 27 17 33 2Z"/>
                                    <path class="trending-fire-middle" d="M35 19C43 29 47 38 43 48C48 44 54 50 52 60C50 70 42 77 31 77C19 77 12 69 13 59C14 51 19 45 24 38C23 49 29 53 32 55C29 41 31 29 35 19Z"/>
                                    <path class="trending-fire-core" d="M33 40C39 49 43 56 40 64C39 72 35 77 29 77C23 77 19 72 20 65C21 58 26 53 29 47C29 55 32 57 34 59C32 52 32 46 33 40Z"/>
                                    <circle class="trending-fire-spark trending-fire-spark-one" cx="14" cy="34" r="2.2"/>
                                    <circle class="trending-fire-spark trending-fire-spark-two" cx="52" cy="29" r="1.8"/>
                                </svg>
                            </span>
                            <span class="sr-only">Hot products</span>
                        </h2>
                    </div>
                    <a href="#shop-products" class="text-xs font-black text-primary hover:text-ink">View all</a>
                </div>
                <div class="trending-products-viewport" data-product-marquee aria-label="Trending products">
                    <div class="trending-products-track" data-product-marquee-track>
                        @for($copy = 0; $copy < 2; $copy++)
                            <div class="trending-products-panel" aria-hidden="{{ $copy === 1 ? 'true' : 'false' }}">
                                @foreach($featuredProducts as $featuredProduct)
                                    <div class="trending-product-card" @if($copy === 1) inert @endif>
                                        <x-product-card :product="$featuredProduct" />
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section id="shop-products" class="min-h-[60vh] bg-[#f3f5f4] py-5 md:py-8">
        <div class="container grid items-start gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
            <aside class="lg:sticky lg:top-24">
                <details id="shop-sidebar" class="group overflow-hidden rounded-lg border border-[#d4ddda] bg-white shadow-[0_12px_32px_rgba(20,49,51,0.08)]">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-ink p-4 font-black text-white lg:hidden">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-sliders text-[#c8dc62]"></i> Filter products</span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-300 transition group-open:rotate-180"></i>
                    </summary>

                    <div class="hidden group-open:block lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto">
                        <div class="border-b border-[#dce4e1] bg-[#edf3f1] p-4">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="flex items-center gap-2 text-xs font-black uppercase tracking-wide text-ink"><span class="grid h-8 w-8 place-items-center rounded-md bg-ink text-xs text-white"><i class="fa-solid fa-sliders"></i></span> Filter products</h2>
                                @if($search || $minPrice || $maxPrice || $sort || $selectedCategory)
                                    <a href="{{ route('shop.index') }}" class="text-xs font-bold text-primary hover:text-ink">Clear all</a>
                                @endif
                            </div>

                            <form action="{{ $categoryActionUrl }}" class="mt-4 space-y-3">
                                <label class="relative block">
                                    <span class="sr-only">Search products</span>
                                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                                    <input name="search" value="{{ $search }}" placeholder="Search products" aria-label="Search products" class="h-11 w-full rounded-md border-[#ccd8d4] bg-white pl-10 text-sm font-medium shadow-sm placeholder:font-medium placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label>
                                        <span class="sr-only">Minimum price</span>
                                        <input name="min_price" type="number" min="0" value="{{ $minPrice }}" placeholder="Min price" class="h-10 w-full min-w-0 rounded-md border-[#ccd8d4] bg-white text-sm shadow-sm focus:border-primary focus:ring-primary">
                                    </label>
                                    <label>
                                        <span class="sr-only">Maximum price</span>
                                        <input name="max_price" type="number" min="0" value="{{ $maxPrice }}" placeholder="Max price" class="h-10 w-full min-w-0 rounded-md border-[#ccd8d4] bg-white text-sm shadow-sm focus:border-primary focus:ring-primary">
                                    </label>
                                </div>
                                <label>
                                    <span class="sr-only">Sort products</span>
                                    <select name="sort" class="h-10 w-full rounded-md border-[#ccd8d4] bg-white py-0 text-sm shadow-sm focus:border-primary focus:ring-primary">
                                        <option value="">Newest arrivals</option>
                                        <option value="price_low" @selected($sort === 'price_low')>Price: low to high</option>
                                        <option value="price_high" @selected($sort === 'price_high')>Price: high to low</option>
                                    </select>
                                </label>
                                @if($selectedCategory && ! $selectedCategoryModel)
                                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                                @endif
                                <button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-md bg-ink text-xs font-black uppercase tracking-wide text-white shadow-[0_6px_16px_rgba(16,63,68,0.16)] hover:bg-primary">
                                    <i class="fa-solid fa-sliders"></i>
                                    Apply filters
                                </button>
                            </form>
                        </div>

                        <div class="hidden bg-white p-4 lg:block">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="font-black text-ink">Categories</h2>
                                <a href="{{ route('shop.index', array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) }}" class="text-xs font-bold {{ $selectedCategory ? 'text-primary hover:text-ink' : 'text-gray-400' }}">All products</a>
                            </div>

                            <div class="mt-3 space-y-1">
                                @foreach($categories as $category)
                                    @php
                                        $isMainActive = $selectedCategory === $category->slug;
                                        $hasActiveChild = $category->children->contains(fn ($child) => $selectedCategory === $child->slug);
                                        [$categoryIcon, $categoryTone] = match ($category->slug) {
                                            'vape-accessories' => ['fa-solid fa-wind', 'bg-[#103f44] text-white'],
                                            'electronics-gadgets' => ['fa-solid fa-microchip', 'bg-[#dceced] text-[#087c7f]'],
                                            'fashion-accessories' => ['fa-solid fa-bag-shopping', 'bg-[#eef2d7] text-[#667711]'],
                                            default => ['fa-solid fa-shapes', 'bg-gray-100 text-gray-600'],
                                        };
                                    @endphp

                                    @if($category->children->isNotEmpty())
                                        <details class="group/category rounded-md {{ $isMainActive || $hasActiveChild ? 'bg-[#edf5f3]' : '' }}" {{ $isMainActive || $hasActiveChild ? 'open' : '' }}>
                                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2 hover:bg-[#f2f5f4]">
                                                @if($category->image)
                                                    <img src="{{ $category->image_url }}" @if($category->image_srcset) srcset="{{ $category->image_srcset }}" sizes="40px" @endif alt="{{ $category->image_alt ?: $category->name.' category' }}" width="80" height="80" loading="lazy" decoding="async" class="h-10 w-10 shrink-0 rounded-md object-cover ring-1 ring-black/5">
                                                @else
                                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md text-sm {{ $categoryTone }}"><i class="{{ $categoryIcon }}"></i></span>
                                                @endif
                                                <span class="min-w-0 flex-1">
                                                    <span class="block text-sm font-bold {{ $isMainActive ? 'text-primary' : 'text-ink' }}">{{ $category->name }}</span>
                                                    <span class="block text-xs text-gray-500">{{ $category->children->count() }} {{ Str::plural('subcategory', $category->children->count()) }}</span>
                                                </span>
                                                <i class="fa-solid fa-chevron-right text-xs text-gray-400 transition group-open/category:rotate-90"></i>
                                            </summary>
                                            <div class="space-y-1 px-3 pb-2 pl-14">
                                                @foreach($category->children as $child)
                                                    <a href="{{ $child->public_url }}{{ http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) ? '?'.http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) : '' }}" class="block rounded px-2 py-1.5 text-xs font-semibold {{ $selectedCategory === $child->slug ? 'bg-primary text-white' : 'text-gray-600 hover:bg-white hover:text-primary' }}">{{ $child->name }}</a>
                                                @endforeach
                                            </div>
                                        </details>
                                    @else
                                        <a href="{{ $category->public_url }}{{ http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) ? '?'.http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) : '' }}" class="flex items-center gap-3 rounded-md px-2 py-2 hover:bg-[#f2f5f4] {{ $isMainActive ? 'bg-[#edf5f3]' : '' }}">
                                            @if($category->image)
                                                <img src="{{ $category->image_url }}" @if($category->image_srcset) srcset="{{ $category->image_srcset }}" sizes="40px" @endif alt="{{ $category->image_alt ?: $category->name.' category' }}" width="80" height="80" loading="lazy" decoding="async" class="h-10 w-10 shrink-0 rounded-md object-cover ring-1 ring-black/5">
                                            @else
                                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md text-sm {{ $categoryTone }}"><i class="{{ $categoryIcon }}"></i></span>
                                            @endif
                                            <span class="text-sm font-bold {{ $isMainActive ? 'text-primary' : 'text-ink' }}">{{ $category->name }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </details>
            </aside>

            <div class="min-w-0">
                @if($selectedCategoryModel || $selectedBrand)
                    <div class="flex items-end justify-between gap-4 border-b border-[#d8e0dd] pb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-primary">Available now</p>
                            <h1 class="mt-1 text-2xl font-black text-ink">{{ $shopHeading }}</h1>
                        </div>
                        <p class="rounded-md border border-[#d6dfdc] bg-white px-3 py-1.5 text-sm font-bold text-gray-500 shadow-sm">{{ $products->total() }} results</p>
                    </div>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-600">{{ $shopDescription }}</p>
                @else
                    <h1 class="sr-only">{{ $shopHeading }}</h1>
                    @if($categoryProductRows->isNotEmpty())
                        <div class="flex items-end justify-between gap-4 border-b border-[#d8e0dd] pb-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-primary">Browse by category</p>
                                <h2 class="mt-1 text-2xl font-black text-ink">Shop Products</h2>
                            </div>
                            <p class="rounded-md border border-[#d6dfdc] bg-white px-3 py-1.5 text-sm font-bold text-gray-500 shadow-sm">{{ $products->total() }} products</p>
                        </div>
                    @else
                        <div class="flex justify-end border-b border-[#d8e0dd] pb-3">
                            <p class="rounded-md border border-[#d6dfdc] bg-white px-3 py-1.5 text-sm font-bold text-gray-500 shadow-sm">{{ $products->total() }} results</p>
                        </div>
                    @endif
                @endif

                @if($categoryProductRows->isNotEmpty())
                    <div class="mt-5 space-y-8">
                        @foreach($categoryProductRows as $category)
                            <section aria-labelledby="category-row-{{ $category->id }}">
                                <div class="mb-3 flex items-end justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black uppercase tracking-wide text-primary">Category</p>
                                        <h2 id="category-row-{{ $category->id }}" class="mt-0.5 truncate text-xl font-black text-ink">{{ $category->name }}</h2>
                                    </div>
                                    <a href="{{ $category->public_url }}" class="shrink-0 text-xs font-black text-primary hover:text-ink">View all <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i></a>
                                </div>
                                @if($category->shopProducts->count() === 1)
                                    <div class="grid grid-cols-3 gap-2 sm:grid-cols-2 sm:gap-4 xl:grid-cols-3">
                                        <x-product-card :product="$category->shopProducts->first()" />
                                    </div>
                                @else
                                    <div class="trending-products-viewport" data-product-marquee aria-label="{{ $category->name }} products">
                                        <div class="trending-products-track" data-product-marquee-track>
                                            @for($copy = 0; $copy < 2; $copy++)
                                                <div class="trending-products-panel" aria-hidden="{{ $copy === 1 ? 'true' : 'false' }}">
                                                    @foreach($category->shopProducts as $categoryProduct)
                                                        <div class="trending-product-card" @if($copy === 1) inert @endif>
                                                            <x-product-card :product="$categoryProduct" />
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            </section>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 grid grid-cols-3 gap-2 sm:grid-cols-2 sm:gap-4 xl:grid-cols-3">
                        @forelse($products as $product)
                            <x-product-card :product="$product" />
                        @empty
                            <div class="col-span-full border border-gray-200 bg-white p-10 text-center shadow-sm">
                                <span class="mx-auto grid h-12 w-12 place-items-center rounded-md bg-gray-100 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <p class="mt-4 font-bold text-ink">No products found</p>
                                <p class="mt-1 text-sm text-gray-500">Try another category or adjust your filters.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-8">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('shop-sidebar');

            if (sidebar) {
                const syncSidebar = () => {
                    if (window.innerWidth >= 1024) {
                        sidebar.open = true;
                    }
                };

                syncSidebar();
                window.addEventListener('resize', syncSidebar);
            }

            const festivalViewport = document.getElementById('festival-mosaic-viewport');
            const festivalTrack = document.getElementById('festival-mosaic-track');

            if (!festivalViewport || !festivalTrack) return;

            let offset = 0;
            let isDragging = false;
            let startX = 0;
            let startOffset = 0;
            let didDrag = false;
            let suppressClick = false;
            let slideTimer = null;
            let resetTimer = null;
            let motionTimer = null;
            const motionDuration = 680;
            const slideDelay = 3200;

            const panelWidth = () => Math.max(1, festivalTrack.scrollWidth / 2);

            const stepWidth = () => {
                const firstCard = festivalTrack.querySelector('.festival-mosaic-card');
                const firstPanel = festivalTrack.querySelector('.festival-mosaic');

                if (!firstCard || !firstPanel) {
                    return Math.max(1, festivalViewport.clientWidth);
                }

                const cardWidth = firstCard.getBoundingClientRect().width;
                const panelStyle = window.getComputedStyle(firstPanel);
                const gap = parseFloat(panelStyle.columnGap || panelStyle.gap || 0) || 0;

                return Math.max(1, cardWidth + gap);
            };

            const normalizeOffset = (value) => {
                const width = panelWidth();

                while (value <= -width) value += width;
                while (value > 0) value -= width;

                return value;
            };

            const renderFestivalTrack = (animate = false) => {
                window.clearTimeout(motionTimer);
                festivalViewport.classList.toggle('is-animating', animate);
                festivalTrack.style.transition = animate ? `transform ${motionDuration}ms cubic-bezier(0.22, 1, 0.36, 1)` : 'none';
                festivalTrack.style.transform = `translate3d(${offset}px, 0, 0)`;

                if (animate) {
                    motionTimer = window.setTimeout(() => {
                        festivalViewport.classList.remove('is-animating');
                    }, motionDuration);
                }
            };

            const scheduleFestivalLoopReset = () => {
                window.clearTimeout(resetTimer);
                resetTimer = window.setTimeout(() => {
                    if (isDragging) return;

                    offset = normalizeOffset(offset);
                    renderFestivalTrack();
                }, motionDuration + 40);
            };

            const moveFestivalStep = () => {
                if (isDragging) return;

                offset -= stepWidth();
                renderFestivalTrack(true);
                scheduleFestivalLoopReset();
            };

            const restartFestivalTimer = () => {
                window.clearInterval(slideTimer);
                slideTimer = window.setInterval(moveFestivalStep, slideDelay);
            };

            festivalViewport.addEventListener('pointerdown', (event) => {
                window.clearInterval(slideTimer);
                window.clearTimeout(resetTimer);
                window.clearTimeout(motionTimer);
                festivalViewport.classList.remove('is-animating');
                isDragging = true;
                didDrag = false;
                startX = event.clientX;
                offset = normalizeOffset(offset);
                startOffset = offset;
                renderFestivalTrack();
            });

            festivalViewport.addEventListener('pointermove', (event) => {
                if (!isDragging) return;

                const delta = event.clientX - startX;

                if (!didDrag && Math.abs(delta) <= 12) {
                    return;
                }

                didDrag = true;
                festivalViewport.classList.add('is-dragging');
                festivalViewport.setPointerCapture?.(event.pointerId);
                offset = normalizeOffset(startOffset + delta);
                renderFestivalTrack();
                event.preventDefault();
            });

            const stopFestivalDrag = (event) => {
                if (!isDragging) return;

                const wasDragged = didDrag;
                const posterLink = wasDragged ? null : event.target.closest('.festival-mosaic-card');
                isDragging = false;
                festivalViewport.classList.remove('is-dragging');
                if (festivalViewport.hasPointerCapture?.(event.pointerId)) {
                    festivalViewport.releasePointerCapture(event.pointerId);
                }

                if (wasDragged) {
                    suppressClick = true;
                    window.setTimeout(() => suppressClick = false, 250);
                    offset = Math.round(offset / stepWidth()) * stepWidth();
                    renderFestivalTrack(true);
                    scheduleFestivalLoopReset();
                }

                restartFestivalTimer();

                if (posterLink) {
                    event.preventDefault();
                    window.location.assign(posterLink.href);
                }
            };

            festivalViewport.addEventListener('pointerup', stopFestivalDrag);
            festivalViewport.addEventListener('pointercancel', stopFestivalDrag);
            festivalViewport.addEventListener('pointerleave', stopFestivalDrag);
            festivalViewport.addEventListener('click', (event) => {
                if (!suppressClick) return;

                event.preventDefault();
                event.stopPropagation();
                suppressClick = false;
            }, true);

            window.addEventListener('resize', () => {
                offset = normalizeOffset(offset);
                renderFestivalTrack();
                restartFestivalTimer();
            });

            renderFestivalTrack();
            restartFestivalTimer();
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            const marquees = [];

            document.querySelectorAll('[data-product-marquee]').forEach((viewport) => {
                const track = viewport.querySelector('[data-product-marquee-track]');

                if (!track) return;

                const marquee = {
                    viewport,
                    track,
                    firstPanel: track.querySelector('.trending-products-panel'),
                    offset: 0,
                    isDragging: false,
                    didDrag: false,
                    isHoverPaused: false,
                    isFocusPaused: false,
                    startX: 0,
                    startOffset: 0,
                    pixelsPerSecond: 24,
                };

                marquee.panelWidth = () => Math.max(1, marquee.firstPanel?.getBoundingClientRect().width || 1);
                marquee.normalize = (value) => {
                    const width = marquee.panelWidth();
                    while (value <= -width) value += width;
                    while (value > 0) value -= width;
                    return value;
                };
                marquee.render = () => {
                    marquee.track.style.transition = 'none';
                    marquee.track.style.transform = `translate3d(${marquee.offset}px, 0, 0)`;
                };
                marquee.ensureTrackCoverage = () => {
                    if (!marquee.firstPanel) return;

                    while (marquee.track.scrollWidth < marquee.viewport.clientWidth + (marquee.panelWidth() * 2)) {
                        const clone = marquee.firstPanel.cloneNode(true);
                        clone.setAttribute('aria-hidden', 'true');
                        clone.setAttribute('inert', '');
                        marquee.track.appendChild(clone);
                    }
                };
                marquee.isPaused = () => marquee.isDragging || marquee.isHoverPaused || marquee.isFocusPaused || document.hidden;

                viewport.addEventListener('pointerdown', (event) => {
                    marquee.isDragging = true;
                    marquee.didDrag = false;
                    marquee.startX = event.clientX;
                    marquee.offset = marquee.normalize(marquee.offset);
                    marquee.startOffset = marquee.offset;
                    marquee.render();
                });
                viewport.addEventListener('pointermove', (event) => {
                    if (!marquee.isDragging) return;
                    const delta = event.clientX - marquee.startX;
                    if (!marquee.didDrag && Math.abs(delta) < 10) return;
                    marquee.didDrag = true;
                    viewport.classList.add('is-dragging');
                    viewport.setPointerCapture?.(event.pointerId);
                    marquee.offset = marquee.normalize(marquee.startOffset + delta);
                    marquee.render();
                    event.preventDefault();
                });
                const stopDrag = (event) => {
                    if (!marquee.isDragging) return;
                    marquee.isDragging = false;
                    viewport.classList.remove('is-dragging');
                    if (viewport.hasPointerCapture?.(event.pointerId)) viewport.releasePointerCapture(event.pointerId);
                };
                viewport.addEventListener('pointerup', stopDrag);
                viewport.addEventListener('pointercancel', stopDrag);
                viewport.addEventListener('pointerenter', (event) => {
                    if (event.pointerType === 'mouse') marquee.isHoverPaused = true;
                });
                viewport.addEventListener('pointerleave', (event) => {
                    if (marquee.isDragging) stopDrag(event);
                    marquee.isHoverPaused = false;
                });
                viewport.addEventListener('focusin', () => {
                    marquee.isFocusPaused = true;
                });
                viewport.addEventListener('focusout', () => {
                    marquee.isFocusPaused = false;
                });
                viewport.addEventListener('click', (event) => {
                    if (!marquee.didDrag) return;

                    event.preventDefault();
                    event.stopPropagation();
                    marquee.didDrag = false;
                }, true);

                marquee.ensureTrackCoverage();
                marquee.render();
                marquees.push(marquee);
            });

            let lastFrameTime = null;
            const animate = (time) => {
                if (lastFrameTime === null) lastFrameTime = time;
                const elapsed = Math.min(50, time - lastFrameTime);
                lastFrameTime = time;

                marquees.forEach((marquee) => {
                    if (marquee.isPaused()) return;

                    marquee.offset = marquee.normalize(marquee.offset - ((marquee.pixelsPerSecond * elapsed) / 1000));
                    marquee.render();
                });

                window.requestAnimationFrame(animate);
            };

            document.addEventListener('visibilitychange', () => {
                lastFrameTime = null;
            });
            window.addEventListener('resize', () => {
                marquees.forEach((marquee) => {
                    marquee.ensureTrackCoverage();
                    marquee.offset = marquee.normalize(marquee.offset);
                    marquee.render();
                });
            });

            if (marquees.length > 0) {
                window.requestAnimationFrame(animate);
            }
        });
    </script>

</x-app-layout>
