<x-app-layout>
    <section class="bg-white py-12">
        <div class="container grid gap-10 lg:grid-cols-2">
            <div class="overflow-hidden rounded-lg bg-gray-100">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full min-h-[420px] w-full object-cover">
            </div>
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-primary">{{ $product->category?->name }}</p>
                <h1 class="mt-2 text-4xl font-black text-ink">{{ $product->name }}</h1>
                <div class="mt-4 flex items-center gap-3">
                    <span class="text-3xl font-black text-primary">BDT {{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price)
                        <span class="text-lg text-gray-400 line-through">BDT {{ number_format($product->compare_price, 2) }}</span>
                    @endif
                </div>
                <p class="mt-4 text-sm font-semibold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $product->stock > 0 ? $product->stock.' in stock' : 'Out of stock' }}
                </p>
                <p class="mt-6 leading-8 text-gray-600">{{ $product->description ?: 'No product description yet.' }}</p>
                @if($product->stock > 0)
                    <form method="POST" action="{{ route('cart.store', $product) }}" class="js-add-to-cart mt-8 flex gap-3">
                        @csrf
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-24 rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                        <button class="rounded-lg bg-primary px-8 py-3 font-semibold text-white hover:bg-ink">Add to Cart</button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="container">
            <h2 class="mb-6 text-2xl font-black text-ink">Related Products</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @empty
                    <p class="text-gray-500">No related products yet.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-app-layout>
