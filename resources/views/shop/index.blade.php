<x-app-layout>
    @if($festivals->isNotEmpty())
        <section class="bg-white pt-6">
            <div class="container">
                <div class="-mx-2 overflow-hidden rounded-lg">
                    <div id="festival-banner-track" class="flex transition-transform duration-700 ease-in-out">
                        @foreach($festivals as $festival)
                            <a href="{{ route('festivals.show', $festival) }}" class="block min-w-full px-2 md:min-w-[50%] lg:min-w-[33.333333%]">
                                <div class="relative overflow-hidden rounded-lg shadow-sm ring-1 ring-gray-100">
                                    <img src="{{ $festival->banner_url }}" alt="{{ $festival->title }}" class="h-48 w-full object-cover md:h-64">
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="bg-white py-10">
        <div class="container">
            <h1 class="text-4xl font-black text-ink">Shop Products</h1>

            <form action="{{ route('shop.index') }}" class="mt-6 grid gap-3 rounded-lg bg-gray-50 p-4 ring-1 ring-gray-100 md:grid-cols-[1fr_130px_130px_160px_auto]">
                <input name="search" value="{{ $search }}" placeholder="Search products" class="rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                <input name="min_price" type="number" min="0" value="{{ $minPrice }}" placeholder="Min price" class="rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                <input name="max_price" type="number" min="0" value="{{ $maxPrice }}" placeholder="Max price" class="rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                <select name="sort" class="rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                    <option value="">Newest</option>
                    <option value="price_low" @selected($sort === 'price_low')>Price low to high</option>
                    <option value="price_high" @selected($sort === 'price_high')>Price high to low</option>
                </select>
                @if($selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif
                <button class="rounded-lg bg-primary px-6 py-2 font-semibold text-white hover:bg-ink">Filter</button>
            </form>

            <div class="mt-6 rounded-lg border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-gray-100">
                <div class="mb-3 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-primary">Browse By</p>
                        <h2 class="text-xl font-black text-ink">Categories</h2>
                    </div>
                    <a href="{{ route('shop.index', array_filter(['search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) }}" class="rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-gray-100 {{ $selectedCategory ? 'bg-gray-50 text-primary hover:text-ink' : 'bg-gray-50 text-gray-400' }}">
                        All Categories
                    </a>
                </div>

                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
                    @foreach($categories as $category)
                        @php
                            $isMainActive = $selectedCategory === $category->slug;
                            $hasActiveChild = $category->children->contains(fn ($child) => $selectedCategory === $child->slug);
                        @endphp

                        <details class="group overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-0.5 hover:shadow-md {{ $isMainActive || $hasActiveChild ? 'ring-primary/30 shadow-md' : '' }}" {{ $isMainActive || $hasActiveChild ? 'open' : '' }}>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-3">
                                <a href="{{ route('shop.index', array_filter(['category' => $category->slug, 'search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) }}" class="flex min-w-0 items-center gap-3">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-14 w-14 shrink-0 rounded-md object-cover shadow-sm ring-1 ring-gray-100">
                                    <span class="min-w-0">
                                        <span class="block truncate font-semibold {{ $isMainActive ? 'text-primary' : 'text-ink group-hover:text-primary' }}">{{ $category->name }}</span>
                                        @if($category->children->count())
                                            <span class="mt-1 block text-xs font-medium text-gray-400">{{ $category->children->count() }} subcategories</span>
                                        @endif
                                    </span>
                                </a>
                                @if($category->children->count())
                                    <span class="shrink-0 text-gray-400 transition group-open:rotate-90">&#9656;</span>
                                @endif
                            </summary>

                            @if($category->children->count())
                                <div class="border-t border-gray-100 bg-gray-50/70 px-4 py-2">
                                    @foreach($category->children->sortBy('name') as $child)
                                        <a href="{{ route('shop.index', array_filter(['category' => $child->slug, 'search' => $search, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort])) }}" class="block rounded px-3 py-2 text-sm {{ $selectedCategory === $child->slug ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="container">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full rounded-lg bg-white p-10 text-center text-gray-500">No products found.</div>
                @endforelse
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </section>

    @if($festivals->count() > 1)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const track = document.getElementById('festival-banner-track');
                const total = {{ $festivals->count() }};
                let index = 0;

                const visibleCount = () => {
                    if (window.innerWidth >= 1024) {
                        return 3;
                    }

                    if (window.innerWidth >= 768) {
                        return 2;
                    }

                    return 1;
                };

                const moveBanner = () => {
                    const visible = visibleCount();
                    const maxIndex = Math.max(0, total - visible);

                    if (index > maxIndex) {
                        index = 0;
                    }

                    track.style.transform = `translateX(-${index * (100 / visible)}%)`;
                };

                if (!track || total < 2) {
                    return;
                }

                setInterval(function () {
                    const maxIndex = Math.max(0, total - visibleCount());

                    if (maxIndex === 0) {
                        moveBanner();
                        return;
                    }

                    index = index >= maxIndex ? 0 : index + 1;
                    moveBanner();
                }, 3500);

                window.addEventListener('resize', moveBanner);
            });
        </script>
    @endif
</x-app-layout>
