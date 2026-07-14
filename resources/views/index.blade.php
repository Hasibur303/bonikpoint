<x-app-layout>
    <section class="bg-white py-8">
        <div class="container">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-primary">Recent & Top Selling</p>
                    <h1 class="text-3xl font-black text-ink md:text-4xl">Bonik Point Products</h1>
                </div>
                <a href="{{ route('shop.index') }}" class="rounded-full bg-primary px-6 py-3 text-sm font-semibold text-white hover:bg-ink">Shop All</a>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @forelse($spotlightProducts as $product)
                    <div class="flex gap-4 rounded-lg border border-gray-100 bg-white p-3 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <a href="{{ route('shop.show', $product) }}" class="h-28 w-28 shrink-0 overflow-hidden rounded bg-gray-100">
                            <img src="{{ $product->image_url }}" alt="{{ $product->image_alt ?: $product->name }}" class="h-full w-full object-cover">
                        </a>
                        <div class="flex min-w-0 flex-1 flex-col">
                            <p class="text-xs font-semibold uppercase tracking-wide text-primary">{{ $product->category?->name }}</p>
                            <a href="{{ route('shop.show', $product) }}" class="mt-1 line-clamp-2 font-semibold text-ink hover:text-primary">{{ $product->name }}</a>
                            <div class="mt-auto pt-3">
                                <p class="font-bold text-primary">BDT {{ number_format($product->price, 2) }}</p>
                                @if($product->compare_price)
                                    <p class="text-sm text-gray-400 line-through">BDT {{ number_format($product->compare_price, 2) }}</p>
                                @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('cart.store', $product) }}" class="js-add-to-cart self-end">
                            @csrf
                            <button class="grid h-9 w-9 place-items-center rounded-full bg-accent text-white hover:bg-primary" title="Add to cart">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-500">No products added yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-14">
        <div class="container">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-primary">Categories</p>
                    <h2 class="text-3xl font-black text-ink">Popular Departments</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="font-semibold text-primary hover:text-ink">View all</a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($categories as $category)
                    <a href="{{ $category->public_url }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md">
                        <p class="font-bold text-ink">{{ $category->name }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ $category->products_count }} products</p>
                    </a>
                @empty
                    <p class="text-gray-500">No categories yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-white py-14">
        <div class="container">
            <div class="mb-8">
                <p class="text-sm font-bold uppercase tracking-wide text-primary">New Arrivals</p>
                <h2 class="text-3xl font-black text-ink">Latest Products</h2>
            </div>
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
                @forelse($newProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="col-span-full text-gray-500">No products added yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-14">
        <div class="container">
            <div class="mb-8">
                <p class="text-sm font-bold uppercase tracking-wide text-primary">Featured</p>
                <h2 class="text-3xl font-black text-ink">Admin Picks</h2>
            </div>
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
                @forelse($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="col-span-full text-gray-500">Mark products as featured from the admin panel.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-app-layout>
