@section('title', $festival->title.' Offers | Bonik Point')
@section('meta_description', Str::limit(strip_tags($festival->description ?: 'Shop selected '.$festival->title.' products and festival discounts from Bonik Point in Bangladesh.'), 155, ''))
@section('canonical', route('festivals.show', $festival))
@section('meta_image', $festival->banner_url)

<x-app-layout>
    <section class="bg-white py-8">
        <div class="container">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    @if($festival->discount_percentage > 0)
                        <p class="text-sm font-bold uppercase tracking-wide text-primary">{{ number_format($festival->discount_percentage, 0) }}% Festival Discount</p>
                    @endif
                    <h1 class="mt-2 text-3xl font-black text-ink md:text-4xl">{{ $festival->title }}</h1>
                    @if($festival->description)
                        <p class="mt-3 max-w-3xl leading-8 text-gray-600">{{ $festival->description }}</p>
                    @endif
                </div>
                <a href="{{ route('shop.index') }}" class="rounded-full border border-gray-200 px-5 py-2 font-semibold text-ink hover:border-primary hover:text-primary">Back to Shop</a>
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="container">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-primary">Festival Products</p>
                    <h2 class="text-3xl font-black text-ink">Available Products</h2>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                @forelse($offerProducts as $product)
                    @php($offerPrice = $festival->discountedPrice($product))
                    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:shadow-lg">
                        <a href="{{ route('shop.show', $product) }}" class="block aspect-square overflow-hidden bg-gray-100">
                            <img src="{{ $product->image_url }}" alt="{{ $product->image_alt ?: $product->name.' product image' }}" width="640" height="640" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                        </a>
                        <div class="p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-primary">{{ $product->category?->name }}</p>
                            <a href="{{ route('shop.show', $product) }}" class="mt-1 block font-semibold text-ink hover:text-primary">{{ $product->name }}</a>
                            <div class="mt-3">
                                <span class="font-bold text-primary">BDT {{ number_format($offerPrice, 2) }}</span>
                                <span class="ml-2 text-sm text-gray-400 line-through">BDT {{ number_format($product->price, 2) }}</span>
                            </div>
                            <form method="POST" action="{{ route('cart.store', $product) }}" class="js-add-to-cart mt-4">
                                @csrf
                                <input type="hidden" name="festival_id" value="{{ $festival->id }}">
                                <button class="w-full rounded bg-accent px-4 py-2 font-semibold text-white hover:bg-primary">Add Festival Offer</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-lg bg-white p-10 text-center text-gray-500">No products available for this festival yet.</div>
                @endforelse
            </div>
        </div>
    </section>
</x-app-layout>
