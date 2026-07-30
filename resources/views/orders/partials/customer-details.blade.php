<div class="space-y-2 text-sm">
    <p class="font-black text-ink">{{ $order->customer_name }}</p>
    <p class="flex items-center gap-2 text-gray-600">
        <i class="fa-solid fa-phone w-4 text-center text-primary"></i>
        <span>{{ $order->mobile }}</span>
    </p>
    <p class="flex items-start gap-2 text-gray-600">
        <i class="fa-solid fa-envelope mt-0.5 w-4 text-center text-primary"></i>
        <span class="min-w-0 break-all">{{ $order->email }}</span>
    </p>
    <p class="flex items-start gap-2 text-gray-600">
        <i class="fa-solid fa-location-dot mt-0.5 w-4 text-center text-primary"></i>
        <span>{{ $order->address }}, {{ $order->city }}</span>
    </p>
</div>

@if(($canEdit ?? true) && $order->customerCanEditDetails())
    <details class="group mt-4 border-y border-[#dce8e5] py-3" @if($errors->hasAny(['customer_name', 'email', 'mobile', 'address', 'order_details'])) open @endif>
        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-black text-primary">
            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-pen-to-square"></i> Edit order details</span>
            <i class="fa-solid fa-chevron-down text-xs transition-transform group-open:rotate-180"></i>
        </summary>

        <form method="POST" action="{{ $updateRoute }}" class="mt-4 grid gap-3">
            @csrf
            @method('PATCH')

            @error('order_details')
                <p class="rounded-md bg-red-50 px-3 py-2 text-xs font-bold text-red-700">{{ $message }}</p>
            @enderror

            <label>
                <span class="mb-1 block text-xs font-black text-ink">Name</span>
                <input name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" autocomplete="name" maxlength="255" required class="h-10 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary">
                @error('customer_name')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label>
                <span class="mb-1 block text-xs font-black text-ink">Phone number</span>
                <input name="mobile" value="{{ old('mobile', $order->mobile) }}" inputmode="tel" autocomplete="tel" maxlength="30" required class="h-10 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary">
                @error('mobile')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label>
                <span class="mb-1 block text-xs font-black text-ink">Email address</span>
                <input name="email" type="email" value="{{ old('email', $order->email) }}" autocomplete="email" maxlength="255" required class="h-10 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary">
                @error('email')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label>
                <span class="mb-1 block text-xs font-black text-ink">Delivery address</span>
                <textarea name="address" rows="3" autocomplete="street-address" maxlength="1000" required class="w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary">{{ old('address', $order->address) }}</textarea>
                @error('address')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
            </label>

            <div class="rounded-md bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-900">
                <span class="font-black">District: {{ $order->city }}</span>
                <span class="block">Contact customer service to change the district because the delivery charge may also change.</span>
            </div>

            <button class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-primary px-4 text-sm font-black text-white shadow-sm hover:bg-ink">
                <i class="fa-solid fa-floppy-disk"></i>
                Save details
            </button>
        </form>
    </details>
@endif
