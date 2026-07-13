<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-3 text-gray-800 sm:p-6">
    <main class="mx-auto max-w-3xl overflow-hidden rounded bg-white p-4 shadow sm:p-8">
        <div class="flex flex-col gap-5 border-b pb-5 sm:flex-row sm:items-start sm:justify-between sm:gap-6 sm:pb-6">
            <div class="min-w-0">
                <h1 class="text-2xl font-black text-[#103f44] sm:text-3xl">Bonik Point</h1>
                <p class="mt-1 text-sm text-gray-500">Shimrail Zero point, Siddirganj, Narayanganj</p>
                <p class="text-sm text-gray-500">Customer service: 01540381020</p>
            </div>
            <div class="min-w-0 text-left sm:text-right">
                <p class="text-sm font-bold uppercase text-gray-400">Receipt</p>
                <p class="break-words font-bold">{{ $order->order_number }}</p>
                <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                <p class="mt-2 inline-flex max-w-full rounded bg-gray-100 px-3 py-1 text-sm font-semibold capitalize">{{ str($order->status)->replace('_', ' ') }}</p>
            </div>
        </div>

        <div class="grid gap-5 py-5 sm:gap-6 sm:py-6 md:grid-cols-2">
            <div class="min-w-0">
                <h2 class="font-bold text-[#103f44]">Customer</h2>
                <p class="mt-2">{{ $order->customer_name }}</p>
                <p class="text-sm text-gray-600">{{ $order->mobile }}</p>
                <p class="break-words text-sm text-gray-600">{{ $order->email }}</p>
            </div>
            <div class="min-w-0">
                <h2 class="font-bold text-[#103f44]">Delivery Address</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $order->address }}, {{ $order->city }}</p>
            </div>
        </div>

        <div class="space-y-3 sm:hidden">
            @foreach($order->items as $item)
                <div class="rounded border border-gray-100 bg-gray-50 p-3">
                    <p class="font-semibold text-[#103f44]">{{ $item->product_name }}</p>
                    @if($item->selected_color_name)
                        <span class="mt-1 block text-xs text-gray-500">Color: {{ $item->selected_color_name }}</span>
                    @endif
                    <div class="mt-3 grid grid-cols-3 gap-2 text-xs text-gray-500">
                        <div>
                            <span class="block uppercase">Qty</span>
                            <span class="font-bold text-gray-800">{{ $item->quantity }}</span>
                        </div>
                        <div>
                            <span class="block uppercase">Price</span>
                            <span class="font-bold text-gray-800">BDT {{ number_format($item->unit_price, 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block uppercase">Total</span>
                            <span class="font-bold text-gray-800">BDT {{ number_format($item->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <table class="hidden w-full text-left text-sm sm:table">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="p-3">Product</th>
                    <th class="p-3">Qty</th>
                    <th class="p-3">Price</th>
                    <th class="p-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($order->items as $item)
                    <tr>
                        <td class="p-3">
                            {{ $item->product_name }}
                            @if($item->selected_color_name)
                                <span class="mt-1 block text-xs text-gray-500">Color: {{ $item->selected_color_name }}</span>
                            @endif
                        </td>
                        <td class="p-3">{{ $item->quantity }}</td>
                        <td class="p-3">BDT {{ number_format($item->unit_price, 2) }}</td>
                        <td class="p-3 text-right">BDT {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ml-auto mt-6 max-w-sm space-y-2 text-sm">
            <div class="flex justify-between gap-4"><span>Subtotal</span><span class="text-right">BDT {{ number_format($order->subtotal, 2) }}</span></div>
            <div class="flex justify-between gap-4"><span>Delivery Charge</span><span class="text-right">BDT {{ number_format($order->shipping, 2) }}</span></div>
            <div class="flex justify-between gap-4 border-t pt-3 text-lg font-black text-[#103f44]"><span>Total</span><span class="text-right">BDT {{ number_format($order->total, 2) }}</span></div>
        </div>

        @if($order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later')
            <p class="mt-6 rounded bg-red-50 p-3 text-sm font-semibold leading-6 text-red-700">ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত আপনার অর্ডার সম্পূর্ণভাবে কনফার্ম হবে না।</p>
        @endif

        <div class="mt-8 flex print:hidden sm:justify-end">
            <button onclick="window.print()" class="w-full rounded bg-[#087c7f] px-6 py-3 font-semibold text-white sm:w-auto">Print Receipt</button>
        </div>
    </main>
</body>
</html>
