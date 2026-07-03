<x-app-layout>
    <section class="bg-white">
        <div class="container grid min-h-[520px] items-center gap-10 py-12 lg:grid-cols-2">
            <div>
                <p class="mb-4 text-sm font-bold uppercase tracking-[0.3em] text-accent">Bonik Point Store</p>
                <h1 class="text-4xl font-black leading-tight text-ink md:text-6xl">Shop fresh products with simple ordering.</h1>
                <p class="mt-5 max-w-xl text-lg leading-8 text-gray-600">Browse products, add to cart, place orders without payment setup, and let the admin manage products, stock, customers, and order status.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('shop.index') }}" class="rounded-full bg-primary px-7 py-3 font-semibold text-white hover:bg-ink">Shop Now</a>
                    @auth
                        <a href="{{ route('orders.index') }}" class="rounded-full border border-gray-200 px-7 py-3 font-semibold text-ink hover:border-primary hover:text-primary">My Orders</a>
                    @else
                        <a href="{{ route('register') }}" class="rounded-full border border-gray-200 px-7 py-3 font-semibold text-ink hover:border-primary hover:text-primary">Create Account</a>
                    @endauth
                </div>
            </div>
            <div class="relative">
                <img src="{{ asset('assets/images/slider/slider-item-1.png') }}" alt="Bonik Point products" class="mx-auto max-h-[460px] object-contain">
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
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md">
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
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($newProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="text-gray-500">No products added yet.</p>
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
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="text-gray-500">Mark products as featured from the admin panel.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-app-layout>
