<x-app-layout>
    <section class="container py-12">
        <h1 class="mb-8 text-4xl font-black text-ink">Shopping Cart</h1>
        @if(count($cartItems))
            <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
                <div class="space-y-4">
                    @foreach($cartItems as $item)
                        <div class="grid gap-4 rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100 md:grid-cols-[96px_1fr_auto] md:items-center">
                            <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" class="h-24 w-24 rounded object-cover">
                            <div>
                                <a href="{{ route('shop.show', $item['product']) }}" class="font-bold text-ink hover:text-primary">{{ $item['product']->name }}</a>
                                <p class="text-sm text-gray-500">৳{{ number_format($item['product']->price, 2) }} each</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}" class="w-20 rounded border-gray-200 text-sm">
                                    <button class="rounded bg-gray-100 px-3 text-sm font-semibold hover:bg-gray-200">Update</button>
                                </form>
                                <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700" title="Remove"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <aside class="h-fit rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-xl font-black text-ink">Order Summary</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>৳{{ number_format(\App\Http\Controllers\CartController::subtotal(), 2) }}</span></div>
                        <div class="flex justify-between"><span>Shipping</span><span>৳0.00</span></div>
                        <div class="border-t pt-3 flex justify-between text-lg font-black text-ink"><span>Total</span><span>৳{{ number_format(\App\Http\Controllers\CartController::subtotal(), 2) }}</span></div>
                    </div>
                    <a href="{{ route('checkout.create') }}" class="mt-6 block rounded-lg bg-primary px-6 py-3 text-center font-semibold text-white hover:bg-ink">Proceed to Checkout</a>
                </aside>
            </div>
        @else
            <div class="rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-gray-100">
                <p class="text-gray-500">Your cart is empty.</p>
                <a href="{{ route('shop.index') }}" class="mt-5 inline-block rounded-lg bg-primary px-6 py-3 font-semibold text-white hover:bg-ink">Start Shopping</a>
            </div>
        @endif
    </section>
</x-app-layout>
