<x-app-layout>
    <section class="container py-12">
        <h1 class="mb-8 text-4xl font-black text-ink">Checkout</h1>
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <form method="POST" action="{{ route('checkout.store') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                @csrf
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Name</label>
                        <input name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Email</label>
                        <input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Mobile</label>
                        <input name="mobile" value="{{ old('mobile', auth()->user()->mobile) }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('mobile')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">City</label>
                        <input name="city" value="{{ old('city') }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-semibold">Address</label>
                        <textarea name="address" rows="4" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>{{ old('address') }}</textarea>
                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-semibold">Order Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <button class="mt-6 rounded-lg bg-primary px-8 py-3 font-semibold text-white hover:bg-ink">Place Order</button>
            </form>
            <aside class="h-fit rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="text-xl font-black text-ink">Your Order</h2>
                <div class="mt-5 space-y-3">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between gap-4 text-sm">
                            <span>{{ $item['product']->name }} x {{ $item['quantity'] }}</span>
                            <span>৳{{ number_format($item['total'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="border-t pt-3 flex justify-between text-lg font-black text-ink"><span>Total</span><span>৳{{ number_format($subtotal + $shipping, 2) }}</span></div>
                </div>
            </aside>
        </div>
    </section>
</x-app-layout>
