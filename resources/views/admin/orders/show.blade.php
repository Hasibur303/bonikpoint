<x-admin-layout>
    <div class="mb-6 flex flex-col items-start justify-between gap-4 xl:flex-row xl:gap-6">
        <div><p class="text-xs font-black uppercase tracking-wide text-primary">{{ $order->is_offline_sale ? 'Offline Sale' : 'Order' }}</p><h1 class="mt-1 text-3xl font-black text-ink">{{ $order->order_number }}</h1></div>
        <div class="flex flex-wrap items-center gap-2">
            @if(in_array($order->status, ['confirmed', 'processing', 'delivered'], true))
                <a href="{{ route('admin.orders.receipt', $order) }}" target="_blank" class="inline-flex items-center gap-2 rounded bg-ink px-4 py-2 font-semibold text-white hover:bg-primary">
                    <i class="fa-solid fa-print"></i>
                    Print / Download Receipt
                </a>
            @endif
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-2">
                @csrf @method('PATCH')
                <label class="min-w-[190px] flex-1 sm:flex-none">
                    <span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Parcel ID</span>
                    <span class="relative block">
                        <i class="fa-solid fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                        <input name="parcel_id" value="{{ old('parcel_id', $order->parcel_id) }}" maxlength="120" placeholder="Enter courier parcel ID" @readonly($order->hasSteadfastShipment()) class="h-10 w-full pl-9 text-sm sm:w-56 {{ $order->hasSteadfastShipment() ? 'cursor-not-allowed bg-gray-100 text-gray-500' : '' }}">
                    </span>
                </label>
                <label>
                    <span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Order Status</span>
                    <select name="status" class="h-10 min-w-[170px] text-sm">
                        @foreach(['waiting_delivery_charge', 'pending', 'confirmed', 'processing', 'delivered', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="min-w-[190px] flex-1 sm:flex-none">
                    <span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Delivery Payment Screenshot <span class="normal-case text-gray-400">(optional)</span></span>
                    <span class="relative block">
                        <i class="fa-solid fa-image absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                        <input name="delivery_payment_proof" type="file" accept="image/jpeg,image/png,image/webp" class="h-10 w-full cursor-pointer text-xs sm:w-56" title="Uploading a screenshot marks the advance delivery charge as paid">
                    </span>
                </label>
                <label class="min-w-[220px] flex-1 sm:flex-none">
                    <span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Cancellation Reason <span class="normal-case text-red-500">(required when cancelled)</span></span>
                    <input name="cancellation_note" value="{{ old('cancellation_note', $order->cancellation_note) }}" maxlength="1000" placeholder="Reason for cancellation" class="h-10 w-full text-sm sm:w-72">
                </label>
                <button class="inline-flex h-10 items-center gap-2 rounded-md bg-primary px-4 text-sm font-black text-white hover:bg-ink"><i class="fa-solid fa-check text-xs"></i>Update</button>
            </form>
            @foreach(['parcel_id', 'delivery_payment_proof', 'cancellation_note'] as $field)
                @error($field)<p class="w-full text-right text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            @endforeach
        </div>
    </div>
    @php
        $steadfastStatusClass = match($order->steadfast_status) {
            'delivered' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'cancelled', 'partial_delivered' => 'bg-red-50 text-red-700 ring-red-200',
            'in_review', 'pending' => 'bg-amber-50 text-amber-800 ring-amber-200',
            default => 'bg-blue-50 text-blue-700 ring-blue-200',
        };
    @endphp
    <section class="mb-6 overflow-hidden rounded-lg border border-[#d8e3e0] bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-[#e5ecea] bg-[#f4f8f7] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-[#d71920] text-white shadow-sm"><i class="fa-solid fa-truck-fast"></i></span>
                <div>
                    <h2 class="font-black text-ink">Steadfast Courier</h2>
                    <p class="mt-0.5 text-xs text-gray-500">{{ $order->hasSteadfastShipment() ? 'Parcel submitted and connected to this order.' : 'Send confirmed order details directly to Steadfast.' }}</p>
                </div>
            </div>

            @if($order->hasSteadfastShipment())
                <form method="POST" action="{{ route('admin.orders.steadfast.status', $order) }}">
                    @csrf
                    <button class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-[#ccd9d6] bg-white px-4 text-xs font-black text-ink shadow-sm transition hover:border-primary hover:text-primary">
                        <i class="fa-solid fa-rotate"></i>
                        Refresh Status
                    </button>
                </form>
            @elseif($order->canSendToSteadfast() && $steadfastConfigured)
                <form method="POST" action="{{ route('admin.orders.steadfast.send', $order) }}" onsubmit="return confirm('Send this order to Steadfast with COD amount BDT {{ number_format($order->dueAmount(), 2, '.', '') }}? This action cannot be repeated.');">
                    @csrf
                    <button class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-[#d71920] px-4 text-xs font-black text-white shadow-[0_4px_0_#99171b] transition hover:-translate-y-0.5 hover:bg-[#bf151b] hover:shadow-[0_5px_0_#871317] active:translate-y-1 active:shadow-none">
                        <i class="fa-solid fa-paper-plane"></i>
                        Send to Steadfast
                    </button>
                </form>
            @endif
        </div>

        <div class="p-5">
            @if($order->hasSteadfastShipment())
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-md border border-gray-100 bg-[#fafcfc] p-3">
                        <p class="text-[10px] font-black uppercase text-gray-400">Consignment ID</p>
                        <p class="mt-1 break-all font-mono text-sm font-black text-ink">{{ $order->steadfast_consignment_id }}</p>
                    </div>
                    <div class="rounded-md border border-gray-100 bg-[#fafcfc] p-3">
                        <p class="text-[10px] font-black uppercase text-gray-400">Tracking Code</p>
                        <p class="mt-1 break-all font-mono text-sm font-black text-primary">{{ $order->steadfast_tracking_code ?: 'Not provided' }}</p>
                    </div>
                    <div class="rounded-md border border-gray-100 bg-[#fafcfc] p-3">
                        <p class="text-[10px] font-black uppercase text-gray-400">Courier Status</p>
                        <span class="mt-1.5 inline-flex rounded-full px-2.5 py-1 text-[10px] font-black capitalize ring-1 {{ $steadfastStatusClass }}">{{ str($order->steadfast_status ?: 'pending')->replace('_', ' ') }}</span>
                    </div>
                    <div class="rounded-md border border-gray-100 bg-[#fafcfc] p-3">
                        <p class="text-[10px] font-black uppercase text-gray-400">COD Submitted</p>
                        <p class="mt-1 text-sm font-black text-red-700">BDT {{ number_format($order->steadfast_cod_amount, 2) }}</p>
                    </div>
                </div>
                <p class="mt-3 text-[11px] font-semibold text-gray-400">
                    Submitted {{ $order->steadfast_submitted_at?->format('d M Y, h:i A') ?: 'recently' }}
                    @if($order->steadfast_last_synced_at)
                        · Last checked {{ $order->steadfast_last_synced_at->diffForHumans() }}
                    @endif
                </p>
            @elseif(! $steadfastConfigured)
                <div class="flex items-start gap-3 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <i class="fa-solid fa-key mt-0.5"></i>
                    <p><span class="font-black">API credentials are not active here.</span> Add the Steadfast API key and secret to this server’s <code>.env</code>, then rebuild the configuration cache.</p>
                </div>
            @elseif($order->is_offline_sale)
                <p class="text-sm font-semibold text-gray-500">Offline sales are recorded for profit reporting and are not submitted to a courier.</p>
            @elseif($order->status !== 'confirmed')
                <div class="flex items-start gap-3 rounded-md border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <p>Change the store order status to <strong>Confirmed</strong> after checking the customer and payment details. The Send to Steadfast button will then become available.</p>
                </div>
            @endif

            @if($order->steadfast_last_error)
                <div class="mt-3 rounded-md border border-red-200 bg-red-50 p-3 text-xs font-semibold text-red-800">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    Last API error: {{ $order->steadfast_last_error }}
                </div>
            @endif
        </div>
    </section>
    @php
        $adjustmentLocked = in_array($order->status, ['delivered', 'cancelled'], true) || $order->hasSteadfastShipment();
    @endphp
    <section class="mb-6 overflow-hidden rounded-lg border border-[#d8e3e0] bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-[#e5ecea] bg-[#f4f8f7] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-primary text-white shadow-sm"><i class="fa-solid fa-tags"></i></span>
                <div>
                    <h2 class="font-black text-ink">Order Adjustment</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Apply a negotiated discount or an additional charge without changing product prices.</p>
                </div>
            </div>
            @if($order->hasAdjustment())
                <span class="inline-flex w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-black uppercase text-emerald-700 ring-1 ring-emerald-200">Adjustment active</span>
            @endif
        </div>

        <div class="grid gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div>
                @if($adjustmentLocked)
                    <div class="flex items-start gap-3 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        <i class="fa-solid fa-lock mt-0.5"></i>
                        <p>
                            <span class="font-black">Adjustment locked.</span>
                            {{ $order->hasSteadfastShipment() ? 'This parcel has already been submitted to Steadfast, so changing the amount could create a COD mismatch.' : 'Delivered or cancelled orders cannot be adjusted.' }}
                        </p>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.orders.adjustment.update', $order) }}" class="grid gap-4 sm:grid-cols-2">
                        @csrf
                        @method('PATCH')
                        <label>
                            <span class="mb-1.5 block text-xs font-black text-ink">Adjustment type</span>
                            <select name="adjustment_type" class="h-11 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary" required>
                                <option value="">Select adjustment</option>
                                <option value="fixed_discount" @selected(old('adjustment_type', $order->adjustment_type) === 'fixed_discount')>Fixed discount (BDT)</option>
                                <option value="percentage_discount" @selected(old('adjustment_type', $order->adjustment_type) === 'percentage_discount')>Percentage discount (%)</option>
                                <option value="extra_charge" @selected(old('adjustment_type', $order->adjustment_type) === 'extra_charge')>Additional charge (BDT)</option>
                            </select>
                            @error('adjustment_type')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </label>
                        <label>
                            <span class="mb-1.5 block text-xs font-black text-ink">Value</span>
                            <input name="adjustment_value" type="number" min="0.01" max="99999999.99" step="0.01" value="{{ old('adjustment_value', $order->hasAdjustment() ? $order->adjustment_value : '') }}" placeholder="Example: 200 or 10" class="h-11 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary" required>
                            @error('adjustment_value')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </label>
                        <label class="sm:col-span-2">
                            <span class="mb-1.5 block text-xs font-black text-ink">Reason <span class="text-red-500">(required)</span></span>
                            <input name="adjustment_reason" maxlength="255" value="{{ old('adjustment_reason', $order->adjustment_reason) }}" placeholder="Example: Customer negotiated bundle price" class="h-11 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary" required>
                            @error('adjustment_reason')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </label>
                        <label class="sm:col-span-2">
                            <span class="mb-1.5 block text-xs font-black text-ink">Internal note <span class="font-medium text-gray-400">(optional, admin only)</span></span>
                            <textarea name="adjustment_note" rows="2" maxlength="1000" placeholder="Add any internal context for your team" class="w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm focus:border-primary focus:ring-primary">{{ old('adjustment_note', $order->adjustment_note) }}</textarea>
                            @error('adjustment_note')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </label>
                        <div class="sm:col-span-2">
                            <button class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-black text-white shadow-[0_4px_0_#075f62] transition hover:-translate-y-0.5 hover:bg-ink active:translate-y-1 active:shadow-none">
                                <i class="fa-solid fa-calculator"></i>
                                {{ $order->hasAdjustment() ? 'Replace Adjustment' : 'Apply Adjustment' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <aside class="rounded-md border border-[#dce6e3] bg-[#f7faf9] p-4">
                <p class="text-[10px] font-black uppercase text-gray-400">Payable calculation</p>
                <div class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Product subtotal</span><span class="font-bold text-ink">BDT {{ number_format($order->subtotal, 2) }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Delivery charge</span><span class="font-bold text-ink">BDT {{ number_format($order->shipping, 2) }}</span></div>
                    @if((float) $order->discount_amount > 0)
                        <div class="flex justify-between gap-3 text-emerald-700"><span>{{ $order->adjustmentLabel() }}</span><span class="font-black">- BDT {{ number_format($order->discount_amount, 2) }}</span></div>
                    @endif
                    @if((float) $order->extra_charge_amount > 0)
                        <div class="flex justify-between gap-3 text-amber-700"><span>Additional charge</span><span class="font-black">+ BDT {{ number_format($order->extra_charge_amount, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between gap-3 border-t border-[#d8e3e0] pt-3 text-base"><span class="font-black text-ink">Final total</span><span class="font-black text-primary">BDT {{ number_format($order->total, 2) }}</span></div>
                </div>

                @if($order->hasAdjustment())
                    <div class="mt-4 border-t border-[#d8e3e0] pt-4">
                        <p class="text-xs font-black text-ink">{{ $order->adjustment_reason }}</p>
                        @if($order->adjustment_note)<p class="mt-1 text-xs leading-5 text-gray-500">{{ $order->adjustment_note }}</p>@endif
                        <p class="mt-2 text-[10px] font-semibold text-gray-400">
                            Applied by {{ $order->adjustedBy?->name ?: 'Administrator' }}
                            @if($order->adjusted_at) on {{ $order->adjusted_at->format('d M Y, h:i A') }}@endif
                        </p>

                        @unless($adjustmentLocked)
                            <form method="POST" action="{{ route('admin.orders.adjustment.clear', $order) }}" class="mt-3">
                                @csrf
                                @method('DELETE')
                                <label>
                                    <span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Removal reason</span>
                                    <input name="clear_reason" maxlength="255" placeholder="Why is it being removed?" class="h-9 w-full rounded-md border-gray-200 bg-white text-xs focus:border-red-400 focus:ring-red-200" required>
                                </label>
                                @error('clear_reason')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                <button class="mt-2 inline-flex h-9 items-center gap-2 rounded-md border border-red-200 bg-white px-3 text-xs font-black text-red-700 hover:bg-red-50">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    Restore Original Total
                                </button>
                            </form>
                        @endunless
                    </div>
                @endif
            </aside>
        </div>
    </section>
    <div class="grid min-w-0 gap-8 lg:grid-cols-[1fr_360px]">
        <div class="min-w-0 rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-black text-ink">Items</h2>
            <div class="overflow-x-auto">
            <table class="min-w-[360px] w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-3">Product</th><th class="p-3">Qty</th><th class="p-3">Price</th><th class="p-3 text-right">Total</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="p-3">
                                {{ $item->product_name }}
                                @if($item->selected_color_name)
                                    <span class="mt-1 flex items-center gap-2 text-xs font-semibold text-gray-500">
                                        <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: {{ $item->selected_color_hex ?: '#E5E7EB' }}"></span>
                                        Color: {{ $item->selected_color_name }}
                                    </span>
                                @endif
                                @if($item->selected_flavor_name)
                                    <span class="mt-1 block text-xs font-semibold text-gray-500">Flavor: {{ $item->selected_flavor_name }}</span>
                                @endif
                            </td>
                            <td class="p-3">{{ $item->quantity }}</td>
                            <td class="p-3">৳{{ number_format($item->unit_price, 2) }}</td>
                            <td class="p-3 text-right">৳{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        <aside class="h-fit min-w-0 rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-black text-ink">Customer</h2>
            <p class="font-semibold">{{ $order->customer_name }}</p>
            <p class="text-sm text-gray-600">{{ $order->email }}</p>
            <p class="text-sm text-gray-600">{{ $order->mobile }}</p>
            <p class="mt-3 text-sm text-gray-600">{{ $order->address }}, {{ $order->city }}</p>
            @if($order->parcel_id)
                <div class="mt-3 flex items-center justify-between gap-3 rounded-md bg-[#edf5f3] px-3 py-2 text-xs"><span class="font-bold text-gray-500">Parcel ID</span><span class="font-mono font-black text-primary">{{ $order->parcel_id }}</span></div>
            @endif
            @if($order->notes)<p class="mt-3 rounded bg-gray-50 p-3 text-sm">{{ $order->notes }}</p>@endif
            @if($order->cancellation_note)
                <div class="mt-3 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-900">
                    <p class="font-black">Cancellation reason</p>
                    <p class="mt-1">{{ $order->cancellation_note }}</p>
                </div>
            @endif
            @if($order->delivery_payment_proof)
                <a href="{{ route('admin.orders.payment-proof', $order) }}" target="_blank" class="mt-3 block overflow-hidden rounded-md border border-primary/20 bg-white p-2 hover:border-primary">
                    <img src="{{ route('admin.orders.payment-proof', $order) }}" alt="Payment proof for {{ $order->order_number }}" class="max-h-56 w-full rounded object-contain">
                    <span class="mt-2 flex items-center justify-center gap-2 text-xs font-bold text-primary">
                        <i class="fa-solid fa-up-right-from-square"></i>
                        Open payment proof
                    </span>
                </a>
            @endif
            @if($order->advance_delivery_required)
                <div class="mt-5 rounded border border-accent/40 bg-accent/10 p-4 text-sm">
                    <p class="font-bold text-ink">Advance Delivery Charge</p>
                    <p class="mt-1">Area: {{ $order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : 'Inside Dhaka' }}</p>
                    <p>Charge: BDT {{ number_format($order->shipping, 2) }}</p>
                    <p>Option: {{ $order->delivery_charge_payment_option === 'pay_later' ? 'Pay Later' : 'Paid Now' }}</p>
                        @if($order->delivery_charge_payment_option === 'pay_now')
                        <p>Method: {{ $order->delivery_payment_method ?: 'Admin recorded' }}</p>
                        @if($order->delivery_payment_mobile)<p>Payment Mobile: {{ $order->delivery_payment_mobile }}</p>@endif
                        @if($order->delivery_transaction_id)<p>Transaction ID: {{ $order->delivery_transaction_id }}</p>@endif
                        @if(! $order->delivery_payment_proof)
                            <p class="mt-2 text-xs font-semibold text-gray-500">No payment screenshot submitted.</p>
                        @endif
                    @else
                        <p class="mt-2 rounded bg-red-50 p-2 font-semibold text-red-700">ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত অর্ডার সম্পূর্ণভাবে কনফার্ম নয়।</p>
                    @endif
                </div>
            @endif
            <div class="mt-5 space-y-2 border-t pt-5 text-sm">
                <div class="flex justify-between gap-4"><span class="text-gray-500">Product subtotal</span><span class="font-bold text-ink">BDT {{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between gap-4"><span class="text-gray-500">Delivery charge</span><span class="font-bold text-ink">BDT {{ number_format($order->shipping, 2) }}</span></div>
                @if((float) $order->discount_amount > 0)
                    <div class="flex justify-between gap-4 text-green-700"><span>{{ $order->adjustmentLabel() }}</span><span class="font-bold">- BDT {{ number_format($order->discount_amount, 2) }}</span></div>
                @endif
                @if((float) $order->extra_charge_amount > 0)
                    <div class="flex justify-between gap-4 text-amber-700"><span>Additional charge</span><span class="font-bold">+ BDT {{ number_format($order->extra_charge_amount, 2) }}</span></div>
                @endif
                <div class="flex justify-between gap-4 border-t pt-2"><span class="font-black text-ink">Final order total</span><span class="font-black text-primary">BDT {{ number_format($order->total, 2) }}</span></div>
                <div class="flex justify-between gap-4"><span class="text-gray-500">Paid amount</span><span class="font-bold text-green-700">BDT {{ number_format($order->paidAmount(), 2) }}</span></div>
                <div class="flex justify-between gap-4 border-t pt-2 text-lg font-black"><span class="text-ink">Due amount</span><span class="text-red-700">BDT {{ number_format($order->dueAmount(), 2) }}</span></div>
            </div>
        </aside>
    </div>
</x-admin-layout>
