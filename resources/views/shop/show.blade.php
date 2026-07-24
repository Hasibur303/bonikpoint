@php
    $isVapeProduct = $product->isAgeRestricted();

    $averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1);
    $primaryImageAlt = $product->image_alt ?: $product->name.' main product image';
    $galleryImages = collect([
        [
            'url' => $product->image_url,
            'label' => $primaryImageAlt,
        ],
    ])->merge($product->images->map(fn ($image, $index) => [
        'url' => $image->image_url,
        'label' => $product->name.' product image '.($index + 2),
    ]));
    $firstColor = $product->colors->first();
    $productDescription = $product->seo_description
        ?: ($product->description
        ? Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($product->description))), 155, '')
        : 'Buy '.$product->name.' from Bonik Point with simple ordering and customer support in Bangladesh.');
    $productTitle = $product->seo_title ?: $product->name.' | Bonik Point';
    $productCategoryTrail = collect([$product->category?->parent, $product->category])->filter();
    $safeSchemaOptions = JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_APOS
        | JSON_HEX_AMP
        | JSON_HEX_QUOT;
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'image' => $galleryImages->pluck('url')->values()->all(),
        'description' => $productDescription,
        'sku' => $product->sku ?: $product->slug,
        'category' => $product->category?->name,
        'brand' => [
            '@type' => 'Brand',
            'name' => $product->brand ?: 'Bonik Point',
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => route('shop.show', $product),
            'priceCurrency' => 'BDT',
            'price' => (string) $product->price,
            'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ],
    ];

    if ($product->reviews_count && $averageRating > 0) {
        $productSchema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $averageRating,
            'reviewCount' => $product->reviews_count,
            'bestRating' => 5,
            'worstRating' => 1,
        ];

        $productSchema['review'] = $reviews->map(function ($review) {
            return array_filter([
                '@type' => 'Review',
                'author' => [
                    '@type' => 'Person',
                    'name' => $review->user?->name ?? 'Verified Customer',
                ],
                'datePublished' => $review->created_at->toDateString(),
                'reviewBody' => $review->comment,
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => $review->rating,
                    'bestRating' => 5,
                    'worstRating' => 1,
                ],
            ], fn ($value) => filled($value));
        })->values()->all();
    }

    $faqSchema = $product->faqs->isNotEmpty()
        ? [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $product->faqs->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq->answer,
                ],
            ])->values()->all(),
        ]
        : null;

    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => route('home.index'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Shop',
            'item' => route('shop.index'),
        ],
    ];

    foreach ($productCategoryTrail as $category) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => count($breadcrumbItems) + 1,
            'name' => $category->name,
            'item' => $category->public_url,
        ];
    }

    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => count($breadcrumbItems) + 1,
        'name' => $product->name,
        'item' => route('shop.show', $product),
    ];
@endphp

@section('title', $productTitle)
@section('meta_description', $productDescription)
@section('canonical', route('shop.show', $product))
@section('meta_image', $product->image_url)
@section('og_type', 'product')

@push('schema')
    <script type="application/ld+json">
        {!! json_encode($productSchema, $safeSchemaOptions) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ], $safeSchemaOptions) !!}
    </script>
    @if($faqSchema)
        <script type="application/ld+json">
            {!! json_encode($faqSchema, $safeSchemaOptions) !!}
        </script>
    @endif
@endpush

<x-app-layout>

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

    <nav aria-label="Breadcrumb" class="border-b border-gray-100 bg-white">
        <ol class="container flex items-center gap-2 overflow-x-auto py-3 text-xs text-gray-500">
            <li><a href="{{ route('home.index') }}" class="hover:text-primary">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('shop.index') }}" class="hover:text-primary">Shop</a></li>
            @foreach($productCategoryTrail as $category)
                <li aria-hidden="true">/</li>
                <li><a href="{{ $category->public_url }}" class="whitespace-nowrap hover:text-primary">{{ $category->name }}</a></li>
            @endforeach
            <li aria-hidden="true">/</li>
            <li class="max-w-48 truncate font-semibold text-ink" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <section class="bg-[#f4f7f6] py-5 md:py-12">
        <div class="container">
            <div class="grid gap-5 md:gap-8 lg:grid-cols-[minmax(0,1.08fr)_minmax(360px,0.72fr)]">
                <div>
                    <div class="overflow-hidden rounded-lg bg-white p-2 shadow-[0_18px_45px_rgba(8,28,31,0.10)] ring-1 ring-gray-100 md:p-3">
                        <div class="overflow-hidden rounded-md bg-gray-100">
                            <img id="product-gallery-main" src="{{ $galleryImages->first()['url'] }}" alt="{{ $galleryImages->first()['label'] }}" width="1000" height="1000" fetchpriority="high" class="aspect-square w-full object-contain">
                        </div>
                    </div>

                    @if($galleryImages->count() > 1)
                        <div class="mt-3 grid grid-cols-5 gap-2 md:mt-4 md:gap-3">
                            @foreach($galleryImages as $index => $galleryImage)
                                <button type="button" aria-label="View product image {{ $index + 1 }}" data-gallery-src="{{ $galleryImage['url'] }}" data-gallery-alt="{{ $galleryImage['label'] }}" class="product-gallery-thumb overflow-hidden rounded-md border bg-white p-1 shadow-sm transition hover:border-primary md:p-1.5 {{ $index === 0 ? 'border-primary ring-2 ring-primary/20' : 'border-gray-200' }}">
                                    <img src="{{ $galleryImage['url'] }}" alt="{{ $galleryImage['label'] }}" width="200" height="200" loading="lazy" decoding="async" class="aspect-square w-full rounded object-contain">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside class="lg:sticky lg:top-24">
                    <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-[0_18px_45px_rgba(8,28,31,0.10)] md:p-5">
                        <a href="{{ $product->category?->public_url ?? route('shop.index') }}" class="text-[10px] font-bold uppercase tracking-wide text-primary hover:text-ink md:text-xs">{{ $product->category?->name }}</a>
                        <h1 class="mt-1.5 text-[1.35rem] font-black leading-[1.18] text-ink md:mt-2 md:text-4xl md:leading-tight">{{ $product->name }}</h1>

                        <div class="mt-3 flex flex-wrap items-center gap-2 md:mt-4">
                            <div class="text-base text-accent md:text-lg" aria-label="{{ $averageRating }} out of 5">
                                @for($star = 1; $star <= 5; $star++)
                                    <span>{!! $averageRating >= $star ? '&#9733;' : '&#9734;' !!}</span>
                                @endfor
                            </div>
                            <p class="text-xs text-gray-500 md:text-sm">
                                {{ $product->reviews_count ? $averageRating.' / 5 from '.$product->reviews_count.' review'.($product->reviews_count > 1 ? 's' : '') : 'No ratings yet' }}
                            </p>
                        </div>

                        <div class="mt-4 flex flex-wrap items-end gap-2 border-y border-gray-100 py-4 md:mt-5 md:gap-3 md:py-5">
                            <span class="text-2xl font-black leading-none text-primary md:text-3xl">BDT {{ number_format($product->price, 2) }}</span>
                            @if($product->compare_price)
                                <span class="pb-0.5 text-sm text-gray-400 line-through md:pb-1 md:text-lg">BDT {{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>

                        <div class="mt-4 grid gap-2.5 rounded-lg bg-[#f8faf9] p-3 text-xs md:mt-5 md:gap-3 md:p-4 md:text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <span class="font-semibold text-gray-500">Availability</span>
                                <span class="font-black {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $product->stock > 0 ? $product->stock.' in stock' : 'Out of stock' }}</span>
                            </div>
                            @if($product->sku)
                                <div class="flex items-center justify-between gap-4">
                                    <span class="font-semibold text-gray-500">SKU</span>
                                    <span class="font-bold text-ink">{{ $product->sku }}</span>
                                </div>
                            @endif
                            @if($product->brand)
                                <div class="flex items-center justify-between gap-4">
                                    <span class="font-semibold text-gray-500">Brand</span>
                                    <a href="{{ route('brands.show', Str::slug($product->brand)) }}" class="font-bold text-primary hover:text-ink">{{ $product->brand }}</a>
                                </div>
                            @endif
                            @if($product->advance_delivery_charge)
                                <div class="flex items-center justify-between gap-4">
                                    <span class="font-semibold text-gray-500">Delivery Charge</span>
                                    <span class="font-bold text-ink">Advance required</span>
                                </div>
                            @endif
                        </div>

                        @if($product->stock > 0)
                            @if($product->colors->isNotEmpty())
                                <div class="mt-5 md:mt-6">
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-bold text-ink md:text-sm">Color</label>
                                        <span id="selected-color-label" class="text-xs font-bold uppercase tracking-wide text-primary">{{ $firstColor?->name }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5 md:gap-2">
                                        @foreach($product->colors as $color)
                                            <button type="button" data-color-id="{{ $color->id }}" data-color-name="{{ $color->name }}" class="product-color-option flex items-center gap-1.5 rounded-full border px-2.5 py-1.5 text-xs font-bold transition md:gap-2 md:px-3 md:py-2 md:text-sm {{ $loop->first ? 'border-primary bg-primary/5 text-primary ring-2 ring-primary/20' : 'border-gray-200 bg-white text-gray-600 hover:border-primary hover:text-primary' }}">
                                                <span class="h-4 w-4 rounded-full border border-black/10 md:h-5 md:w-5" style="background-color: {{ $color->hex_code ?: '#E5E7EB' }}"></span>
                                                {{ $color->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-5 md:mt-6">
                                <label for="product-quantity" class="mb-2 block text-xs font-bold text-ink md:text-sm">Quantity</label>
                                <input id="product-quantity" type="number" value="1" min="1" max="{{ $product->stock }}" class="h-10 w-24 rounded-lg border-gray-200 bg-[#f8faf9] text-center text-sm font-bold focus:border-primary focus:ring-primary md:h-12 md:w-28 md:text-base">
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <form method="POST" action="{{ route('cart.store', $product) }}" class="js-add-to-cart product-action-form">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1" class="product-action-quantity">
                                    @if($product->colors->isNotEmpty())
                                        <input type="hidden" name="product_color_id" value="{{ $firstColor?->id }}" class="product-action-color">
                                    @endif
                                    <button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-lg border border-primary bg-white px-4 text-sm font-black text-primary shadow-sm hover:bg-primary hover:text-white md:h-12 md:px-5 md:text-base">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                        Add to Cart
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('cart.store', $product) }}" class="product-action-form">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1" class="product-action-quantity">
                                    @if($product->colors->isNotEmpty())
                                        <input type="hidden" name="product_color_id" value="{{ $firstColor?->id }}" class="product-action-color">
                                    @endif
                                    <input type="hidden" name="buy_now" value="1">
                                    <button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 text-sm font-black text-white shadow-lg shadow-primary/20 hover:bg-ink md:h-12 md:px-5 md:text-base">
                                        <i class="fa-solid fa-bolt"></i>
                                        Buy Now
                                    </button>
                                </form>
                            </div>
                        @else
                            <button disabled class="mt-6 h-11 w-full rounded-lg bg-gray-200 text-sm font-black text-gray-500 md:h-12 md:text-base">Out of Stock</button>
                        @endif

                        @if($product->hasWarranty())
                            <div class="mt-5 rounded-lg border border-primary/20 bg-primary/5 p-3 md:mt-6 md:p-4">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-primary md:text-xs">Warranty / Guarantee</p>
                                <h2 class="mt-1 text-sm font-black text-ink md:text-base">
                                    {{ $product->warranty_label }}
                                    @if($product->warranty_duration)
                                        <span class="font-semibold text-gray-600">- {{ $product->warranty_duration }}</span>
                                    @endif
                                </h2>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>

            <div class="mt-6 overflow-hidden rounded-lg border border-gray-100 bg-white shadow-sm md:mt-8">
                <div class="flex gap-2 overflow-x-auto border-b border-gray-100 bg-white p-2.5 md:p-3">
                    <button type="button" data-product-tab="description" class="product-tab-button shrink-0 rounded-md bg-primary px-3 py-2 text-xs font-black text-white md:px-4 md:text-sm">Description</button>
                    <button type="button" data-product-tab="reviews" class="product-tab-button shrink-0 rounded-md px-3 py-2 text-xs font-black text-gray-500 hover:bg-gray-50 hover:text-primary md:px-4 md:text-sm">Reviews & Rating</button>
                    <button type="button" data-product-tab="faq" class="product-tab-button shrink-0 rounded-md px-3 py-2 text-xs font-black text-gray-500 hover:bg-gray-50 hover:text-primary md:px-4 md:text-sm">FAQ</button>
                </div>

                <div class="p-4 md:p-7">
                    <div data-product-tab-panel="description" class="product-tab-panel">
                        <div class="grid gap-5 md:gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                            <div>
                                <p class="whitespace-pre-line text-sm leading-7 text-gray-700 md:text-base md:leading-8">{{ $product->description ?: 'No product description yet.' }}</p>
                            </div>
                            <div class="space-y-4">
                                @if($product->hasWarranty())
                                    <div class="rounded-lg border border-gray-100 bg-[#f8faf9] p-4">
                                        <p class="text-sm font-black text-ink">Warranty / Guarantee</p>
                                        @if($product->warranty_details)
                                            <p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-600">{{ $product->warranty_details }}</p>
                                        @else
                                            <p class="mt-2 text-sm leading-7 text-gray-600">{{ $product->warranty_label }}{{ $product->warranty_duration ? ' for '.$product->warranty_duration : '' }}.</p>
                                        @endif
                                    </div>
                                @endif
                                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                                    <p class="text-sm font-black text-ink">Return Check</p>
                                    <p class="mt-2 text-sm leading-7 text-gray-700">Please open and check the product in front of the delivery person. If anything is wrong, take photo/video proof and request return immediately.</p>
                                    <a href="{{ route('return-policy') }}" class="mt-3 inline-block text-sm font-bold text-primary hover:text-ink">View return policy</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div data-product-tab-panel="reviews" class="product-tab-panel hidden">
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
                                                <span>{!! $review->rating >= $star ? '&#9733;' : '&#9734;' !!}</span>
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

                    <div data-product-tab-panel="faq" class="product-tab-panel hidden">
                        <div class="mb-6">
                            <p class="text-sm font-bold uppercase tracking-wide text-primary">Product FAQ</p>
                            <h2 class="mt-1 text-2xl font-black text-ink">Common Questions</h2>
                        </div>

                        <div class="space-y-3">
                            @forelse($product->faqs as $faq)
                                <details class="group rounded-lg border border-gray-100 bg-gray-50">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-bold text-ink">
                                        <span>{{ $faq->question }}</span>
                                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary/10 text-primary transition group-open:rotate-45">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </span>
                                    </summary>
                                    <div class="border-t border-gray-100 px-5 py-4">
                                        <p class="whitespace-pre-line leading-7 text-gray-600">{{ $faq->answer }}</p>
                                    </div>
                                </details>
                            @empty
                                <div class="rounded-lg border border-gray-100 bg-gray-50 p-8 text-center text-gray-500">No FAQ added for this product yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-12">
        <div class="container">
            <h2 class="mb-6 text-2xl font-black text-ink">Related Products</h2>
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4 xl:grid-cols-5">
                @forelse($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @empty
                    <p class="col-span-full text-gray-500">No related products yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quantityInput = document.getElementById('product-quantity');
            const actionForms = document.querySelectorAll('.product-action-form');
            const tabButtons = document.querySelectorAll('.product-tab-button');
            const tabPanels = document.querySelectorAll('.product-tab-panel');
            const mainImage = document.getElementById('product-gallery-main');
            const thumbs = document.querySelectorAll('.product-gallery-thumb');
            const colorButtons = document.querySelectorAll('.product-color-option');
            const colorInputs = document.querySelectorAll('.product-action-color');
            const selectedColorLabel = document.getElementById('selected-color-label');

            colorButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    colorButtons.forEach(function (item) {
                        item.classList.remove('border-primary', 'bg-primary/5', 'text-primary', 'ring-2', 'ring-primary/20');
                        item.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
                    });

                    button.classList.add('border-primary', 'bg-primary/5', 'text-primary', 'ring-2', 'ring-primary/20');
                    button.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
                    colorInputs.forEach(function (input) {
                        input.value = button.dataset.colorId;
                    });

                    if (selectedColorLabel) {
                        selectedColorLabel.textContent = button.dataset.colorName;
                    }
                });
            });

            actionForms.forEach(function (form) {
                form.addEventListener('submit', function () {
                    const quantity = quantityInput ? quantityInput.value : 1;
                    form.querySelectorAll('.product-action-quantity').forEach(function (input) {
                        input.value = quantity;
                    });
                });
            });

            tabButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const selected = button.dataset.productTab;

                    tabButtons.forEach(function (item) {
                        item.classList.toggle('bg-primary', item === button);
                        item.classList.toggle('text-white', item === button);
                        item.classList.toggle('text-gray-500', item !== button);
                    });

                    tabPanels.forEach(function (panel) {
                        panel.classList.toggle('hidden', panel.dataset.productTabPanel !== selected);
                    });
                });
            });

            thumbs.forEach(function (thumb) {
                thumb.addEventListener('click', function () {
                    if (!mainImage) {
                        return;
                    }

                    mainImage.src = thumb.dataset.gallerySrc;
                    mainImage.alt = thumb.dataset.galleryAlt;

                    thumbs.forEach(function (item) {
                        item.classList.remove('border-primary', 'ring-2', 'ring-primary/20');
                        item.classList.add('border-gray-200');
                    });

                    thumb.classList.add('border-primary', 'ring-2', 'ring-primary/20');
                    thumb.classList.remove('border-gray-200');
                });
            });
        });
    </script>

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

                confirmButton.addEventListener('click', async function () {
                    await fetch('{{ route('cart.confirm-age') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        },
                    });

                    sessionStorage.setItem(storageKey, 'yes');
                    warning.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            });
        </script>
    @endif
</x-app-layout>
