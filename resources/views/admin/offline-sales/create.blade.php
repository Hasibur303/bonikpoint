<x-admin-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Store Counter</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Add Offline Sale</h1>
            <p class="mt-2 text-sm text-gray-500">Record a counter sale or create an offline customer order for Steadfast delivery.</p>
        </div>
        <a href="{{ route('admin.profit.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-[#d7e1df] bg-white px-4 text-xs font-black text-ink shadow-sm hover:border-primary hover:text-primary"><i class="fa-solid fa-chart-line"></i>View Profit Report</a>
    </div>

    <form method="POST" action="{{ route('admin.offline-sales.store') }}" class="max-w-3xl rounded-lg border border-[#dfe7e5] bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="offline-product-search" class="mb-1.5 block text-sm font-black text-ink">Find Product</label>
                <input id="offline-product-search" type="search" placeholder="Search by product name, SKU, or category" class="mb-2 h-11 w-full text-sm">
                <select id="offline-product" name="product_id" class="h-12 w-full text-sm" required>
                    <option value="">Select a product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}" data-search="{{ str($product->name.' '.$product->sku.' '.$product->category?->name)->lower() }}" @selected(old('product_id') == $product->id)>
                            {{ $product->name }} @if($product->sku) ({{ $product->sku }}) @endif - {{ $product->category?->name }} - {{ $product->stock }} in stock
                        </option>
                    @endforeach
                </select>
                @error('product_id')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="offline-quantity" class="mb-1.5 block text-sm font-black text-ink">Quantity</label>
                <input id="offline-quantity" name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" class="h-11 w-full text-sm" required>
                <p id="offline-stock" class="mt-1 text-xs font-semibold text-gray-500">Choose a product to see available stock.</p>
                @error('quantity')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="offline-selling-price" class="mb-1.5 block text-sm font-black text-ink">Selling Price Per Unit</label>
                <input id="offline-selling-price" name="selling_price" type="number" min="0" step="0.01" value="{{ old('selling_price') }}" placeholder="Product price" class="h-11 w-full text-sm" required>
                <p class="mt-1 text-xs font-semibold text-gray-500">You can change the price for this sale only.</p>
                @error('selling_price')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <input type="hidden" name="requires_courier" value="0">
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-[#cddfdb] bg-[#f2f8f6] p-4 transition hover:border-primary">
                    <input id="offline-requires-courier" name="requires_courier" type="checkbox" value="1" @checked(old('requires_courier')) class="mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                    <span>
                        <span class="flex items-center gap-2 font-black text-ink"><i class="fa-solid fa-truck-fast text-[#d71920]"></i> Send this order with Steadfast Courier</span>
                        <span class="mt-1 block text-xs leading-5 text-gray-500">After saving, review the order and use the Send to Steadfast button from the order page.</span>
                    </span>
                </label>
                @error('requires_courier')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="offline-courier-options" class="hidden sm:col-span-2">
                <div class="grid gap-4 rounded-lg border border-red-100 bg-red-50/50 p-4 sm:grid-cols-2">
                    <div>
                        <label for="offline-payment-status" class="mb-1.5 block text-sm font-black text-ink">Payment Collection</label>
                        <select id="offline-payment-status" name="offline_payment_status" class="h-11 w-full text-sm">
                            <option value="cod" @selected(old('offline_payment_status', 'cod') === 'cod')>Collect order total by COD</option>
                            <option value="paid" @selected(old('offline_payment_status') === 'paid')>Customer already paid</option>
                        </select>
                        @error('offline_payment_status')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="offline-delivery-charge" class="mb-1.5 block text-sm font-black text-ink">Delivery Charge</label>
                        <input id="offline-delivery-charge" name="delivery_charge" type="number" min="0" step="0.01" value="{{ old('delivery_charge', 0) }}" class="h-11 w-full text-sm">
                        <p class="mt-1 text-xs text-gray-500">This amount is added to the customer’s order total.</p>
                        @error('delivery_charge')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 sm:col-span-2">
                <p class="text-sm font-black text-ink">Customer Details <span id="offline-customer-requirement" class="font-medium text-gray-400">(optional for counter sale)</span></p>
                <p class="mt-1 text-xs text-gray-500">Name, valid mobile number, district, thana, and full address are required for Steadfast delivery.</p>
            </div>
            <div>
                <label for="offline-customer-name" class="mb-1.5 block text-sm font-black text-ink">Customer Name</label>
                <input id="offline-customer-name" name="customer_name" value="{{ old('customer_name') }}" maxlength="120" placeholder="Buyer name" class="h-11 w-full text-sm">
                @error('customer_name')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="offline-mobile" class="mb-1.5 block text-sm font-black text-ink">Phone Number</label>
                <input id="offline-mobile" name="mobile" value="{{ old('mobile') }}" maxlength="30" inputmode="tel" placeholder="01XXXXXXXXX" class="h-11 w-full text-sm">
                @error('mobile')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="offline-city" class="mb-1.5 block text-sm font-black text-ink">District / জেলা</label>
                <input id="offline-city" name="city" value="{{ old('city') }}" maxlength="100" placeholder="Dhaka" autocomplete="address-level2" class="h-11 w-full text-sm">
                @error('city')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="offline-thana" class="mb-1.5 block text-sm font-black text-ink">Thana / থানা</label>
                <input id="offline-thana" name="thana" value="{{ old('thana') }}" maxlength="120" placeholder="Dhanmondi" autocomplete="address-level3" class="h-11 w-full text-sm">
                @error('thana')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="offline-address" class="mb-1.5 block text-sm font-black text-ink">Full Address / সম্পূর্ণ ঠিকানা</label>
                <textarea id="offline-address" name="address" rows="2" maxlength="1000" placeholder="House, road, area, and delivery details" autocomplete="street-address" class="w-full text-sm">{{ old('address') }}</textarea>
                @error('address')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="offline-notes" class="mb-1.5 block text-sm font-black text-ink">Note <span class="font-medium text-gray-400">(optional)</span></label>
                <textarea id="offline-notes" name="notes" rows="3" maxlength="500" placeholder="Example: Sold from shop counter, paid in cash" class="w-full text-sm">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
            <p id="offline-save-hint" class="text-xs text-gray-500"><i class="fa-solid fa-circle-info mr-1 text-primary"></i>Counter sales are recorded immediately in the profit report.</p>
            <button class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-black text-white hover:bg-ink"><i class="fa-solid fa-cash-register"></i>Save Offline Sale</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('offline-product-search');
            const product = document.getElementById('offline-product');
            const price = document.getElementById('offline-selling-price');
            const quantity = document.getElementById('offline-quantity');
            const stock = document.getElementById('offline-stock');
            const requiresCourier = document.getElementById('offline-requires-courier');
            const courierOptions = document.getElementById('offline-courier-options');
            const customerRequirement = document.getElementById('offline-customer-requirement');
            const saveHint = document.getElementById('offline-save-hint');
            const courierRequiredFields = [
                document.getElementById('offline-payment-status'),
                document.getElementById('offline-delivery-charge'),
                document.getElementById('offline-customer-name'),
                document.getElementById('offline-mobile'),
                document.getElementById('offline-city'),
                document.getElementById('offline-thana'),
                document.getElementById('offline-address'),
            ];

            const refreshProduct = () => {
                const option = product.options[product.selectedIndex];
                const available = option?.dataset.stock;

                if (available === undefined) {
                    stock.textContent = 'Choose a product to see available stock.';
                    quantity.removeAttribute('max');
                    return;
                }

                price.value = option.dataset.price;
                quantity.max = available;
                stock.textContent = `${available} unit(s) currently in stock.`;
            };

            search.addEventListener('input', function () {
                const term = this.value.trim().toLowerCase();

                Array.from(product.options).forEach((option, index) => {
                    if (index === 0) return;
                    option.hidden = term !== '' && !option.dataset.search.includes(term);
                });
            });

            product.addEventListener('change', refreshProduct);

            const refreshCourier = () => {
                const enabled = requiresCourier.checked;
                courierOptions.classList.toggle('hidden', !enabled);
                courierRequiredFields.forEach((field) => field.required = enabled);
                customerRequirement.textContent = enabled ? '(required for courier)' : '(optional for counter sale)';
                saveHint.innerHTML = enabled
                    ? '<i class="fa-solid fa-circle-info mr-1 text-primary"></i>The order will be saved as Confirmed. Review it before sending to Steadfast.'
                    : '<i class="fa-solid fa-circle-info mr-1 text-primary"></i>Counter sales are recorded immediately in the profit report.';
            };

            requiresCourier.addEventListener('change', refreshCourier);
            refreshProduct();
            refreshCourier();
        });
    </script>
</x-admin-layout>
