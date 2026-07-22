<x-admin-layout>
    <div class="mb-6">
        <p class="text-xs font-black uppercase tracking-wide text-primary">System</p>
        <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Store Settings</h1>
        <p class="mt-2 text-sm text-gray-500">Control delivery charges and customer payment instructions.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-5xl space-y-5">
        @csrf
        @method('PATCH')

        <section class="rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-[#e7edeb] px-5 py-4"><span class="grid h-9 w-9 place-items-center rounded-md bg-blue-50 text-blue-700"><i class="fa-solid fa-truck-fast text-sm"></i></span><div><h2 class="text-sm font-black text-ink">Delivery Charges</h2><p class="mt-0.5 text-xs text-gray-500">Applied automatically during checkout.</p></div></div>
            <div class="grid gap-5 p-5 md:grid-cols-2">
                <div><label class="mb-1.5 block text-sm font-bold">Inside Dhaka</label><div class="relative"><span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">BDT</span><input type="number" name="inside_dhaka_delivery_charge" value="{{ old('inside_dhaka_delivery_charge', $settings['inside_dhaka_delivery_charge']) }}" class="h-11 w-full pl-12" required></div>@error('inside_dhaka_delivery_charge')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1.5 block text-sm font-bold">Outside Dhaka</label><div class="relative"><span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">BDT</span><input type="number" name="outside_dhaka_delivery_charge" value="{{ old('outside_dhaka_delivery_charge', $settings['outside_dhaka_delivery_charge']) }}" class="h-11 w-full pl-12" required></div>@error('outside_dhaka_delivery_charge')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
        </section>

        <section class="rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-[#e7edeb] px-5 py-4"><span class="grid h-9 w-9 place-items-center rounded-md bg-[#f0f5d7] text-[#617311]"><i class="fa-solid fa-mobile-screen-button text-sm"></i></span><div><h2 class="text-sm font-black text-ink">Payment Accounts</h2><p class="mt-0.5 text-xs text-gray-500">Numbers shown in advance delivery-charge instructions.</p></div></div>
            <div class="grid gap-5 p-5 md:grid-cols-3">
                <div><label class="mb-1.5 block text-sm font-bold">Bkash Number</label><input name="bkash_number" value="{{ old('bkash_number', $settings['bkash_number']) }}" class="h-11 w-full" required>@error('bkash_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1.5 block text-sm font-bold">Nagad Number</label><input name="nagad_number" value="{{ old('nagad_number', $settings['nagad_number']) }}" class="h-11 w-full" required>@error('nagad_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1.5 block text-sm font-bold">Rocket Number</label><input name="rocket_number" value="{{ old('rocket_number', $settings['rocket_number']) }}" class="h-11 w-full" required>@error('rocket_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
        </section>

        <section class="rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-[#e7edeb] px-5 py-4"><span class="grid h-9 w-9 place-items-center rounded-md bg-amber-50 text-amber-700"><i class="fa-solid fa-language text-sm"></i></span><div><h2 class="text-sm font-black text-ink">Pay Later Instruction</h2><p class="mt-0.5 text-xs text-gray-500">Bangla notice shown before an unpaid order can be confirmed.</p></div></div>
            <div class="p-5"><label class="sr-only">Pay Later Bangla Instruction</label><textarea name="delivery_pay_later_note_bn" rows="4" class="w-full" required>{{ old('delivery_pay_later_note_bn', $settings['delivery_pay_later_note_bn']) }}</textarea>@error('delivery_pay_later_note_bn')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        </section>

        <div class="sticky bottom-4 flex items-center justify-between gap-4 rounded-lg border border-[#d7e1df] bg-white/95 p-3 shadow-[0_12px_35px_rgba(16,63,68,0.13)] backdrop-blur"><p class="hidden pl-2 text-xs font-semibold text-gray-500 sm:block">Changes affect future checkouts immediately.</p><button class="ml-auto inline-flex h-11 items-center gap-2 rounded-md bg-primary px-5 text-sm font-black text-white hover:bg-ink"><i class="fa-solid fa-floppy-disk"></i>Save Settings</button></div>
    </form>
</x-admin-layout>
