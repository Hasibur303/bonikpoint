@php
    $currentPage = $products->currentPage();
    $hasFilterParameters = collect([$search, $minPrice, $maxPrice, $sort])->contains(fn ($value) => filled($value));
    $shopHeading = $selectedCategoryModel?->name ?? 'Bonik Point Products';
    $categoryDescription = $selectedCategoryModel?->seo_description ?: $selectedCategoryModel?->description;
    $shopDescription = $categoryDescription
        ?: ($selectedCategoryModel
            ? 'Browse '.$selectedCategoryModel->name.' products at Bonik Point with simple ordering, customer support, and reliable delivery options in Bangladesh.'
            : 'Shop Bonik Point products including vape items, gadgets, kitchen essentials, toys, fashion, and daily-use collections with simple ordering in Bangladesh.');
    $shopTitle = $selectedCategoryModel
        ? ($selectedCategoryModel->seo_title ?: $selectedCategoryModel->name.' Products in Bangladesh | Bonik Point')
        : (request()->routeIs('home.index') ? 'Bonik Point Store | Shop Products in Bangladesh' : 'Shop Products in Bangladesh | Bonik Point');
    $shopCanonical = $selectedCategoryModel
        ? $selectedCategoryModel->public_url
        : route(request()->routeIs('home.index') ? 'home.index' : 'shop.index');

    if (! $hasFilterParameters && $currentPage > 1) {
        $shopTitle .= ' - Page '.$currentPage;
        $shopDescription = 'Page '.$currentPage.'. '.$shopDescription;
        $shopCanonical .= '?page='.$currentPage;
    }

    $shopRobots = $hasFilterParameters || ($currentPage > 1 && $products->isEmpty())
        ? 'noindex,follow'
        : 'index,follow';
@endphp

@section('title', $shopTitle)
@section('meta_description', Str::limit(strip_tags($shopDescription), 155, ''))
@section('canonical', $shopCanonical)
@section('meta_image', asset('assets/images/logo.jpg'))
@section('robots', $shopRobots)

<x-app-layout>
    @if($festivals->isNotEmpty())
        <section class="border-b border-gray-100 bg-[#f4f7f6] py-3 md:py-4">
            <div class="container">
                <div id="festival-mosaic-viewport" class="festival-mosaic-viewport" aria-label="Festival offers">
                    <div id="festival-mosaic-track" class="festival-mosaic-track">
                        @for($copy = 0; $copy < 2; $copy++)
                            <div class="festival-mosaic" aria-hidden="{{ $copy === 1 ? 'true' : 'false' }}">
                                @foreach($festivals as $festival)
                                    <a href="{{ route('festivals.show', $festival) }}" class="festival-mosaic-card group">
                                        <img src="{{ $festival->banner_url }}" alt="{{ $festival->title }}" draggable="false" onload="if (this.naturalWidth / Math.max(this.naturalHeight, 1) > 2.1) this.closest('.festival-mosaic-card')?.classList.add('is-wide')" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.035]">
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
                    gap: 0.35rem;
                    padding-right: 0.35rem;
                }

                .festival-mosaic-card {
                    flex: 0 0 clamp(6.25rem, calc((100vw - 2.7rem) / 3), 8rem);
                    display: block;
                    aspect-ratio: 1 / 1;
                    overflow: hidden;
                    border-radius: 0.45rem;
                    background: #071b1f;
                    box-shadow: 0 10px 28px rgba(8, 28, 31, 0.10);
                    transition: transform 180ms ease, box-shadow 180ms ease;
                }

                @media (min-width: 768px) {
                    .festival-mosaic-track {
                        width: 200%;
                    }

                    .festival-mosaic {
                        flex: 0 0 50%;
                        display: grid;
                        grid-template-columns: repeat(4, minmax(0, 1fr));
                        gap: 0.5rem;
                        padding-right: 0.5rem;
                    }

                    .festival-mosaic-card {
                        flex: initial;
                        border-radius: 0.5rem;
                    }

                    .festival-mosaic-card.is-wide,
                    .festival-mosaic-card:nth-child(5n) {
                        grid-column: 1 / -1;
                        justify-self: center;
                        width: 74%;
                        aspect-ratio: 4 / 0.68;
                    }
                }

                @media (min-width: 1024px) {
                    .festival-mosaic-card:hover {
                        transform: translateY(-2px);
                    }
                }
            </style>
        </section>
    @endif

    <section class="bg-[#f4f7f6] py-6 md:py-8">
        <div class="container grid items-start gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
            <aside class="lg:sticky lg:top-24">
                <details id="shop-sidebar" class="group overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 font-black text-ink lg:hidden">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-sliders text-primary"></i> Filters and categories</span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition group-open:rotate-180"></i>
                    </summary>

                    <div class="hidden group-open:block lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto">
                        <div class="p-4">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="font-black text-ink">Filter products</h2>
                                @if($search || $minPrice || $maxPrice || $sort || $selectedCategory)
                                    <a href="{{ route('shop.index') }}" class="text-xs font-bold text-primary hover:text-ink">Clear all</a>
                                @endif
                            </div>

                            <form action="{{ $categoryActionUrl }}" class="mt-4 space-y-3">
                                <label class="relative block">
                                    <span class="sr-only">Search products</span>
                                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                                    <input name="search" value="{{ $search }}" placeholder="Search products" class="h-11 w-full rounded-md border-gray-200 bg-[#f8faf9] pl-10 text-sm focus:border-primary focus:ring-primary">
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input name="min_price" type="number" min="0" value="{{ $minPrice }}" placeholder="Min price" class="h-10 min-w-0 rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary">
                                    <input name="max_price" type="number" min="0" value="{{ $maxPrice }}" placeholder="Max price" class="h-10 min-w-0 rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary">
                                </div>
                                <select name="sort" class="h-10 w-full rounded-md border-gray-200 bg-[#f8faf9] py-0 text-sm focus:border-primary focus:ring-primary">
                                    <option value="">Newest arrivals</option>
                                    <option value="price_low" @selected($sort === 'price_low')>Price: low to high</option>
                                    <option value="price_high" @selected($sort === 'price_high')>Price: high to low</option>
                                </select>
                                @if($selectedCategory && ! $selectedCategoryModel)
                                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                                @endif
                                <button class="flex h-11 w-full items-center justify-center gap-2 rounded-md bg-primary text-sm font-black text-white hover:bg-ink">
                                    <i class="fa-solid fa-sliders"></i>
                                    Apply filters
                                </button>
                            </form>
                        </div>

                        <div class="border-t border-gray-100 p-4">
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
                                        <details class="group/category rounded-md {{ $isMainActive || $hasActiveChild ? 'bg-primary/5' : '' }}" {{ $isMainActive || $hasActiveChild ? 'open' : '' }}>
                                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2 hover:bg-[#f7f9f8]">
                                                @if($category->image)
                                                    <img src="{{ $category->image_url }}" alt="{{ $category->image_alt ?: $category->name.' category' }}" loading="lazy" decoding="async" class="h-10 w-10 shrink-0 rounded-md object-cover ring-1 ring-black/5">
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
                                                <a href="{{ $category->public_url }}{{ http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) ? '?'.http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) : '' }}" class="block rounded px-2 py-1.5 text-xs font-bold {{ $isMainActive ? 'bg-primary text-white' : 'text-primary hover:bg-white' }}">All {{ $category->name }}</a>
                                                @foreach($category->children->sortBy('name') as $child)
                                                    <a href="{{ $child->public_url }}{{ http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) ? '?'.http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) : '' }}" class="block rounded px-2 py-1.5 text-xs font-semibold {{ $selectedCategory === $child->slug ? 'bg-primary text-white' : 'text-gray-600 hover:bg-white hover:text-primary' }}">{{ $child->name }}</a>
                                                @endforeach
                                            </div>
                                        </details>
                                    @else
                                        <a href="{{ $category->public_url }}{{ http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) ? '?'.http_build_query(array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) : '' }}" class="flex items-center gap-3 rounded-md px-2 py-2 hover:bg-[#f7f9f8] {{ $isMainActive ? 'bg-primary/5' : '' }}">
                                            @if($category->image)
                                                <img src="{{ $category->image_url }}" alt="{{ $category->image_alt ?: $category->name.' category' }}" loading="lazy" decoding="async" class="h-10 w-10 shrink-0 rounded-md object-cover ring-1 ring-black/5">
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

            <div>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-primary">Available now</p>
                        <h1 class="mt-1 text-2xl font-black text-ink">{{ $shopHeading }}</h1>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">{{ $products->total() }} results</p>
                </div>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-600">{{ $shopDescription }}</p>

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
            let slideTimer = null;
            let resetTimer = null;
            const slideDelay = 2600;

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
                festivalTrack.style.transition = animate ? 'transform 520ms cubic-bezier(0.22, 1, 0.36, 1)' : 'none';
                festivalTrack.style.transform = `translate3d(${offset}px, 0, 0)`;
            };

            const moveFestivalStep = () => {
                if (isDragging) return;

                window.clearTimeout(resetTimer);
                offset -= stepWidth();
                renderFestivalTrack(true);

                resetTimer = window.setTimeout(() => {
                    if (isDragging || offset > -panelWidth()) return;

                    offset += panelWidth();
                    renderFestivalTrack();
                }, 560);
            };

            const restartFestivalTimer = () => {
                window.clearInterval(slideTimer);
                slideTimer = window.setInterval(moveFestivalStep, slideDelay);
            };

            festivalViewport.addEventListener('pointerdown', (event) => {
                window.clearInterval(slideTimer);
                window.clearTimeout(resetTimer);
                isDragging = true;
                didDrag = false;
                startX = event.clientX;
                offset = normalizeOffset(offset);
                startOffset = offset;
                renderFestivalTrack();
                festivalViewport.classList.add('is-dragging');
                festivalViewport.setPointerCapture?.(event.pointerId);
            });

            festivalViewport.addEventListener('pointermove', (event) => {
                if (!isDragging) return;

                const delta = event.clientX - startX;

                if (Math.abs(delta) > 6) {
                    didDrag = true;
                }

                offset = normalizeOffset(startOffset + delta);
                renderFestivalTrack();
                event.preventDefault();
            });

            const stopFestivalDrag = (event) => {
                if (!isDragging) return;

                isDragging = false;
                festivalViewport.classList.remove('is-dragging');
                festivalViewport.releasePointerCapture?.(event.pointerId);
                restartFestivalTimer();
            };

            festivalViewport.addEventListener('pointerup', stopFestivalDrag);
            festivalViewport.addEventListener('pointercancel', stopFestivalDrag);
            festivalViewport.addEventListener('pointerleave', stopFestivalDrag);
            festivalViewport.addEventListener('click', (event) => {
                if (!didDrag) return;

                event.preventDefault();
                event.stopPropagation();
                didDrag = false;
            }, true);

            window.addEventListener('resize', () => {
                offset = normalizeOffset(offset);
                renderFestivalTrack();
                restartFestivalTimer();
            });

            renderFestivalTrack();
            restartFestivalTimer();
        });
    </script>

</x-app-layout>
