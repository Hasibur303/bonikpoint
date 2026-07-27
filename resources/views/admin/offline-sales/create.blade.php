<x-admin-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Store Counter</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Add Offline Sale</h1>
            <p class="mt-2 text-sm text-gray-500">Record an in-person sale to update stock and profit immediately.</p>
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
                <label for="offline-notes" class="mb-1.5 block text-sm font-black text-ink">Note <span class="font-medium text-gray-400">(optional)</span></label>
                <textarea id="offline-notes" name="notes" rows="3" maxlength="500" placeholder="Example: Sold from shop counter, paid in cash" class="w-full text-sm">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-gray-500"><i class="fa-solid fa-circle-info mr-1 text-primary"></i>This sale will be marked delivered and included in profit reporting.</p>
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
            refreshProduct();
        });
    </script>
</x-admin-layout>
