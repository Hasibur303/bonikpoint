<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Receipt {{ $order->order_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                @if($order->parcel_id)
                    <p class="mt-1 break-all text-sm font-bold text-[#087c7f]">Parcel ID: {{ $order->parcel_id }}</p>
                @endif
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
                    @if($item->selected_flavor_name)
                        <span class="mt-1 block text-xs text-gray-500">Flavor: {{ $item->selected_flavor_name }}</span>
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
                            @if($item->selected_flavor_name)
                                <span class="mt-1 block text-xs text-gray-500">Flavor: {{ $item->selected_flavor_name }}</span>
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
            <div class="flex justify-between gap-4 text-green-700"><span>Paid Amount</span><span class="text-right font-bold">BDT {{ number_format($order->paidAmount(), 2) }}</span></div>
            <div class="flex justify-between gap-4 rounded bg-red-50 px-3 py-2 text-lg font-black text-red-700"><span>Due Amount</span><span class="text-right">BDT {{ number_format($order->dueAmount(), 2) }}</span></div>
        </div>

        @if($order->advance_delivery_required)
            <div class="mt-6 rounded border border-gray-200 bg-gray-50 p-4 text-sm">
                <h2 class="font-bold text-[#103f44]">Delivery Payment</h2>
                <div class="mt-2 grid gap-1 sm:grid-cols-2">
                    <p>Payment status: <strong>{{ $order->delivery_charge_payment_option === 'pay_now' ? 'Submitted' : 'Not paid' }}</strong></p>
                    <p>Delivery charge: <strong>BDT {{ number_format($order->shipping, 2) }}</strong></p>
                    @if($order->delivery_charge_payment_option === 'pay_now')
                        <p>Method: <strong>{{ $order->delivery_payment_method ?: 'Admin recorded' }}</strong></p>
                        @if($order->delivery_payment_mobile)<p>Payment mobile: <strong>{{ $order->delivery_payment_mobile }}</strong></p>@endif
                        @if($order->delivery_transaction_id)<p class="break-all sm:col-span-2">Transaction ID: <strong>{{ $order->delivery_transaction_id }}</strong></p>@endif
                    @endif
                </div>
            </div>
        @endif

        @if($order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later')
            <p class="mt-6 rounded bg-red-50 p-3 text-sm font-semibold leading-6 text-red-700">ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত আপনার অর্ডার সম্পূর্ণভাবে কনফার্ম হবে না।</p>
        @endif

        <div class="mt-8 flex flex-col gap-2 print:hidden sm:flex-row sm:justify-end">
            @if(!empty($adminReceipt))
                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center rounded border border-gray-200 px-6 py-3 font-semibold text-gray-700 hover:border-[#087c7f] hover:text-[#087c7f]">Back to Order</a>
            @endif
            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded bg-[#087c7f] px-6 py-3 font-semibold text-white hover:bg-[#103f44]">
                <i class="fa-solid fa-print"></i>
                Print / Save as PDF
            </button>
        </div>
    </main>
</body>
</html>
