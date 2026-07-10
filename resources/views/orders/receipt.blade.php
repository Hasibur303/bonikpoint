<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 text-gray-800">
    <main class="mx-auto max-w-3xl rounded bg-white p-8 shadow">
        <div class="flex items-start justify-between gap-6 border-b pb-6">
            <div>
                <h1 class="text-3xl font-black text-[#103f44]">Bonik Point</h1>
                <p class="mt-1 text-sm text-gray-500">Shimrail Zero point, Siddirganj, Narayanganj</p>
                <p class="text-sm text-gray-500">Contact: 01540381020</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold uppercase text-gray-400">Receipt</p>
                <p class="font-bold">{{ $order->order_number }}</p>
                <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                <p class="mt-2 rounded bg-gray-100 px-3 py-1 text-sm font-semibold capitalize">{{ str($order->status)->replace('_', ' ') }}</p>
            </div>
        </div>

        <div class="grid gap-6 py-6 md:grid-cols-2">
            <div>
                <h2 class="font-bold text-[#103f44]">Customer</h2>
                <p class="mt-2">{{ $order->customer_name }}</p>
                <p class="text-sm text-gray-600">{{ $order->mobile }}</p>
                <p class="text-sm text-gray-600">{{ $order->email }}</p>
            </div>
            <div>
                <h2 class="font-bold text-[#103f44]">Delivery Address</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $order->address }}, {{ $order->city }}</p>
            </div>
        </div>

        <table class="w-full text-left text-sm">
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
                        <td class="p-3">{{ $item->product_name }}</td>
                        <td class="p-3">{{ $item->quantity }}</td>
                        <td class="p-3">BDT {{ number_format($item->unit_price, 2) }}</td>
                        <td class="p-3 text-right">BDT {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ml-auto mt-6 max-w-sm space-y-2 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>BDT {{ number_format($order->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span>Delivery Charge</span><span>BDT {{ number_format($order->shipping, 2) }}</span></div>
            <div class="flex justify-between border-t pt-3 text-lg font-black text-[#103f44]"><span>Total</span><span>BDT {{ number_format($order->total, 2) }}</span></div>
        </div>

        @if($order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later')
            <p class="mt-6 rounded bg-red-50 p-3 text-sm font-semibold text-red-700">ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত আপনার অর্ডার সম্পূর্ণভাবে কনফার্ম হবে না।</p>
        @endif

        <div class="mt-8 flex justify-end print:hidden">
            <button onclick="window.print()" class="rounded bg-[#087c7f] px-6 py-3 font-semibold text-white">Print Receipt</button>
        </div>
    </main>
</body>
</html>
