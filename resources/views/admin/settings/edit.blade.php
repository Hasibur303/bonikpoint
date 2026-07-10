<x-admin-layout>
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-wide text-primary">Store Control</p>
        <h1 class="text-4xl font-black text-ink">Settings</h1>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-4xl rounded-lg bg-white p-6 shadow-sm">
        @csrf
        @method('PATCH')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold">Inside Dhaka Delivery Charge</label>
                <input type="number" name="inside_dhaka_delivery_charge" value="{{ old('inside_dhaka_delivery_charge', $settings['inside_dhaka_delivery_charge']) }}" class="w-full rounded border-gray-200" required>
                @error('inside_dhaka_delivery_charge')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Outside Dhaka Delivery Charge</label>
                <input type="number" name="outside_dhaka_delivery_charge" value="{{ old('outside_dhaka_delivery_charge', $settings['outside_dhaka_delivery_charge']) }}" class="w-full rounded border-gray-200" required>
                @error('outside_dhaka_delivery_charge')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Bkash Number</label>
                <input name="bkash_number" value="{{ old('bkash_number', $settings['bkash_number']) }}" class="w-full rounded border-gray-200" required>
                @error('bkash_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Nagad Number</label>
                <input name="nagad_number" value="{{ old('nagad_number', $settings['nagad_number']) }}" class="w-full rounded border-gray-200" required>
                @error('nagad_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Rocket Number</label>
                <input name="rocket_number" value="{{ old('rocket_number', $settings['rocket_number']) }}" class="w-full rounded border-gray-200" required>
                @error('rocket_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold">Pay Later Bangla Instruction</label>
                <textarea name="delivery_pay_later_note_bn" rows="3" class="w-full rounded border-gray-200" required>{{ old('delivery_pay_later_note_bn', $settings['delivery_pay_later_note_bn']) }}</textarea>
                @error('delivery_pay_later_note_bn')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <button class="mt-6 rounded bg-primary px-6 py-3 font-semibold text-white">Save Settings</button>
    </form>
</x-admin-layout>
