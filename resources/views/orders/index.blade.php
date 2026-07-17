<x-user-dashboard-layout>
    <section>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between sm:gap-6">
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-primary">Purchase history</p>
                <h1 class="mt-1 text-2xl font-black text-ink sm:text-3xl">My Orders</h1>
                <p class="mt-2 text-sm text-gray-500">Review order status, payment, and delivery details.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="inline-flex h-10 items-center justify-center gap-2 self-start rounded-md bg-ink px-4 text-sm font-black text-white hover:bg-primary sm:self-auto">
                <i class="fa-solid fa-store"></i>
                Shop Products
            </a>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="divide-y divide-gray-100 md:hidden">
                @forelse($orders as $order)
                    @php
                        $needsPayment = $order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later';
                        $statusStyle = match($order->status) {
                            'delivered' => 'bg-emerald-50 text-emerald-700',
                            'cancelled' => 'bg-red-50 text-red-700',
                            'waiting_delivery_charge' => 'bg-amber-50 text-amber-700',
                            default => 'bg-[#eaf5f3] text-primary',
                        };
                    @endphp
                    <article class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-all text-sm font-black text-ink">{{ $order->order_number }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                            </div>
                            <span class="rounded px-2 py-1 text-[10px] font-black capitalize {{ $statusStyle }}">{{ str($order->status)->replace('_', ' ') }}</span>
                        </div>
                        <div class="mt-4 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-gray-400">Order total</p>
                                <p class="mt-0.5 font-black text-ink">BDT {{ number_format($order->total, 2) }}</p>
                            </div>
                            <div class="flex gap-2">
                                @if($needsPayment)
                                    <a href="{{ route('orders.delivery-payment', $order) }}" class="inline-flex h-9 items-center rounded-md bg-[#8a681b] px-3 text-xs font-black text-white">Pay Now</a>
                                @endif
                                <a href="{{ route('orders.show', $order) }}" class="inline-flex h-9 items-center gap-1.5 rounded-md border border-gray-200 px-3 text-xs font-black text-ink hover:border-primary hover:text-primary">Details <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="p-10 text-center">
                        <span class="mx-auto grid h-12 w-12 place-items-center rounded-md bg-gray-100 text-gray-400"><i class="fa-solid fa-box-open"></i></span>
                        <p class="mt-3 font-black text-ink">No orders yet</p>
                        <a href="{{ route('shop.index') }}" class="mt-2 inline-block text-sm font-bold text-primary">Start shopping</a>
                    </div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#f8f9fa] text-[10px] font-black uppercase text-gray-400">
                        <tr><th class="px-6 py-3">Order</th><th class="px-6 py-3">Total</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Date</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($orders as $order)
                            @php
                                $needsPayment = $order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later';
                                $statusStyle = match($order->status) {
                                    'delivered' => 'bg-emerald-50 text-emerald-700',
                                    'cancelled' => 'bg-red-50 text-red-700',
                                    'waiting_delivery_charge' => 'bg-amber-50 text-amber-700',
                                    default => 'bg-[#eaf5f3] text-primary',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-black text-ink">{{ $order->order_number }}</td>
                                <td class="px-6 py-4 font-bold">BDT {{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4"><span class="rounded px-2 py-1 text-xs font-bold capitalize {{ $statusStyle }}">{{ str($order->status)->replace('_', ' ') }}</span></td>
                                <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($needsPayment)
                                            <a href="{{ route('orders.delivery-payment', $order) }}" class="rounded-md bg-[#8a681b] px-3 py-2 text-xs font-black text-white hover:bg-ink">Pay Delivery</a>
                                        @endif
                                        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1.5 font-bold text-primary hover:text-ink">View <i class="fa-solid fa-arrow-right text-xs"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-10 text-center text-gray-500">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </section>
</x-user-dashboard-layout>
