<x-app-layout>
    @php
        $isVapeProduct = in_array($product->category?->slug, ['vape-accessories'], true)
            || in_array($product->category?->parent?->slug, ['vape-accessories'], true);
    @endphp

    @if($isVapeProduct)
        <div id="vape-age-warning" class="fixed inset-0 z-[120] hidden bg-ink/90 px-4 py-6 backdrop-blur">
            <div class="flex min-h-full items-center justify-center">
                <div class="w-full max-w-lg rounded-lg bg-white p-6 text-center shadow-2xl">
                    <p class="text-sm font-bold uppercase tracking-wide text-primary">Age Restricted Product</p>
                    <h2 class="mt-2 text-3xl font-black text-ink">Are you 18 or older?</h2>
                    <p class="mt-4 leading-7 text-gray-600">This product is intended for adult users only. Please confirm your age before viewing vape products on Bonik Point.</p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <button id="confirm-vape-age" type="button" class="rounded bg-primary px-5 py-3 font-semibold text-white hover:bg-ink">Yes, I am 18+</button>
                        <a href="{{ route('shop.index') }}" class="rounded border border-gray-200 px-5 py-3 font-semibold text-ink hover:border-primary hover:text-primary">No, go back</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <section class="bg-white py-12">
        <div class="container grid gap-10 lg:grid-cols-2">
            <div class="overflow-hidden rounded-lg bg-gray-100">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full min-h-[420px] w-full object-cover">
            </div>
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-primary">{{ $product->category?->name }}</p>
                <h1 class="mt-2 text-4xl font-black text-ink">{{ $product->name }}</h1>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <div class="text-lg text-accent">
                        @php($averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1))
                        @for($star = 1; $star <= 5; $star++)
                            <span>{{ $averageRating >= $star ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-500">
                        {{ $product->reviews_count ? $averageRating.' / 5 from '.$product->reviews_count.' review'.($product->reviews_count > 1 ? 's' : '') : 'No ratings yet' }}
                    </p>
                </div>
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

    <section class="bg-white py-12">
        <div class="container">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-primary">Customer Reviews</p>
                    <h2 class="text-2xl font-black text-ink">Ratings & Comments</h2>
                </div>
                <p class="text-sm text-gray-500">{{ $product->reviews_count }} review{{ $product->reviews_count === 1 ? '' : 's' }}</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @forelse($reviews as $review)
                    <article class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-ink">{{ $review->user?->name ?? 'Customer' }}</p>
                                <p class="text-xs text-gray-500">{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-accent">
                                @for($star = 1; $star <= 5; $star++)
                                    <span>{{ $review->rating >= $star ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="mt-4 leading-7 text-gray-600">{{ $review->comment }}</p>
                        @endif
                    </article>
                @empty
                    <div class="col-span-full rounded-lg border border-gray-100 bg-gray-50 p-8 text-center text-gray-500">No customer reviews yet.</div>
                @endforelse
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

    @if($isVapeProduct)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const warning = document.getElementById('vape-age-warning');
                const confirmButton = document.getElementById('confirm-vape-age');
                const storageKey = 'bonikpoint_vape_age_confirmed';

                if (!warning || !confirmButton) {
                    return;
                }

                if (sessionStorage.getItem(storageKey) !== 'yes') {
                    warning.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }

                confirmButton.addEventListener('click', function () {
                    sessionStorage.setItem(storageKey, 'yes');
                    warning.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            });
        </script>
    @endif
</x-app-layout>
