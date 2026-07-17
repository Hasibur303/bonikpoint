<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    private const BANGLADESH_CITIES = [
        'Bagerhat', 'Bandarban', 'Barguna', 'Barishal', 'Bhola', 'Bogura',
        'Brahmanbaria', 'Chandpur', 'Chapainawabganj', 'Chattogram', 'Chuadanga',
        "Cox's Bazar", 'Cumilla', 'Dhaka', 'Dinajpur', 'Faridpur', 'Feni',
        'Gaibandha', 'Gazipur', 'Gopalganj', 'Habiganj', 'Jamalpur', 'Jashore',
        'Jhalokati', 'Jhenaidah', 'Joypurhat', 'Khagrachhari', 'Khulna',
        'Kishoreganj', 'Kurigram', 'Kushtia', 'Lakshmipur', 'Lalmonirhat',
        'Madaripur', 'Magura', 'Manikganj', 'Meherpur', 'Moulvibazar',
        'Munshiganj', 'Mymensingh', 'Naogaon', 'Narail', 'Narayanganj',
        'Narsingdi', 'Natore', 'Netrokona', 'Nilphamari', 'Noakhali', 'Pabna',
        'Panchagarh', 'Patuakhali', 'Pirojpur', 'Rajbari', 'Rajshahi',
        'Rangamati', 'Rangpur', 'Satkhira', 'Shariatpur', 'Sherpur', 'Sirajganj',
        'Sunamganj', 'Sylhet', 'Tangail', 'Thakurgaon',
    ];

    public function create(): View|RedirectResponse
    {
        return $this->renderCheckout(false);
    }

    public function guestCreate(): View|RedirectResponse
    {
        return $this->renderCheckout(true);
    }

    private function renderCheckout(bool $isGuestCheckout): View|RedirectResponse
    {
        $cartItems = app(CartController::class)->items();

        if (count($cartItems) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $city = $this->canonicalCity(old('city'));

        return view('checkout.create', [
            'cartItems' => $cartItems,
            'subtotal' => CartController::subtotal(),
            'shipping' => $city ? $this->deliveryChargeForCity($city) : 0,
            'advanceDeliveryRequired' => $this->advanceDeliveryRequired($cartItems),
            'deliverySettings' => StoreSetting::deliverySettings(),
            'cities' => self::BANGLADESH_CITIES,
            'isGuestCheckout' => $isGuestCheckout,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->placeOrder($request, false);
    }

    public function guestStore(Request $request): RedirectResponse
    {
        return $this->placeOrder($request, true);
    }

    private function placeOrder(Request $request, bool $isGuestCheckout): RedirectResponse
    {
        $cartItems = app(CartController::class)->items();

        if (count($cartItems) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $advanceDeliveryRequired = $this->advanceDeliveryRequired($cartItems);

        $request->merge(['city' => $this->canonicalCity($request->input('city'))]);

        $rules = [
            'customer_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', Rule::in(self::BANGLADESH_CITIES)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($advanceDeliveryRequired) {
            $rules = [
                ...$rules,
                'delivery_charge_payment_option' => [$isGuestCheckout ? 'nullable' : 'required', $isGuestCheckout ? Rule::in(['pay_now']) : 'in:pay_now,pay_later'],
                'delivery_payment_method' => [$isGuestCheckout ? 'required' : 'required_if:delivery_charge_payment_option,pay_now', 'nullable', 'in:Bkash,Nagad,Rocket'],
                'delivery_payment_mobile' => [$isGuestCheckout ? 'required' : 'required_if:delivery_charge_payment_option,pay_now', 'nullable', 'string', 'max:30'],
                'delivery_transaction_id' => [$isGuestCheckout ? 'required' : 'required_if:delivery_charge_payment_option,pay_now', 'nullable', 'string', 'max:120'],
                'delivery_payment_proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ];
        }

        $data = $request->validate($rules);
        $data['delivery_charge_payment_option'] = $advanceDeliveryRequired
            ? ($isGuestCheckout ? 'pay_now' : $data['delivery_charge_payment_option'])
            : null;

        $deliveryArea = $this->deliveryAreaForCity($data['city']);
        $paymentProofPath = null;

        if ($advanceDeliveryRequired && $data['delivery_charge_payment_option'] === 'pay_now' && $request->hasFile('delivery_payment_proof')) {
            $paymentProofPath = $request->file('delivery_payment_proof')->store('delivery-payment-proofs', 'local');
        }

        try {
            $order = DB::transaction(function () use ($data, $cartItems, $advanceDeliveryRequired, $deliveryArea, $isGuestCheckout, $paymentProofPath) {
                $subtotal = collect($cartItems)->sum('total');
                $shipping = $advanceDeliveryRequired ? $this->deliveryCharge($deliveryArea) : 0;
                $guestToken = $isGuestCheckout ? Str::random(48) : null;
                $order = Order::create([
                    ...$data,
                    'user_id' => $isGuestCheckout ? null : auth()->id(),
                    'guest_token' => $guestToken,
                    'order_number' => 'BP-'.now()->format('YmdHis').'-'.($isGuestCheckout ? 'G'.Str::upper(Str::random(4)) : auth()->id()),
                    'status' => $advanceDeliveryRequired && $data['delivery_charge_payment_option'] === 'pay_later' ? 'waiting_delivery_charge' : 'pending',
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $subtotal + $shipping,
                    'advance_delivery_required' => $advanceDeliveryRequired,
                    'delivery_area' => $advanceDeliveryRequired ? $deliveryArea : null,
                    'delivery_charge_payment_option' => $advanceDeliveryRequired ? $data['delivery_charge_payment_option'] : null,
                    'delivery_payment_method' => $advanceDeliveryRequired ? $data['delivery_payment_method'] : null,
                    'delivery_payment_mobile' => $advanceDeliveryRequired ? $data['delivery_payment_mobile'] : null,
                    'delivery_transaction_id' => $advanceDeliveryRequired ? $data['delivery_transaction_id'] : null,
                    'delivery_payment_proof' => $paymentProofPath,
                ]);

                foreach ($cartItems as $item) {
                    $product = $item['product'];
                    $quantity = $item['quantity'];

                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'selected_color_name' => $item['product_color_name'] ?? null,
                        'selected_color_hex' => $item['product_color_hex'] ?? null,
                        'buying_price' => $product->buying_price,
                        'unit_price' => $item['unit_price'],
                        'quantity' => $quantity,
                        'total' => $item['total'],
                    ]);

                    $product->decrement('stock', $quantity);
                }

                return $order;
            });
        } catch (Throwable $exception) {
            if ($paymentProofPath) {
                Storage::disk('local')->delete($paymentProofPath);
            }

            throw $exception;
        }

        session()->forget('cart');

        if ($isGuestCheckout) {
            return redirect()->route('guest.orders.show', [$order->order_number, $order->guest_token])->with('success', 'Order placed successfully.');
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully.');
    }

    private function advanceDeliveryRequired(array $cartItems): bool
    {
        return collect($cartItems)->contains(fn ($item) => (bool) $item['product']->advance_delivery_charge);
    }

    private function deliveryCharge(?string $area): int
    {
        $settings = StoreSetting::deliverySettings();

        return $area === 'outside_dhaka'
            ? $settings['outside_dhaka_delivery_charge']
            : $settings['inside_dhaka_delivery_charge'];
    }

    private function deliveryChargeForCity(string $city): int
    {
        return $this->deliveryCharge($this->deliveryAreaForCity($city));
    }

    private function deliveryAreaForCity(string $city): string
    {
        return $city === 'Dhaka' ? 'inside_dhaka' : 'outside_dhaka';
    }

    private function canonicalCity(?string $city): ?string
    {
        $city = trim((string) $city);

        if ($city === '') {
            return null;
        }

        foreach (self::BANGLADESH_CITIES as $option) {
            if (strcasecmp($option, $city) === 0) {
                return $option;
            }
        }

        return $city;
    }
}
