<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreSetting;
use App\Support\BotProtection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('orders.index', [
            'orders' => auth()->user()->orders()->latest()->paginate(10),
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        return view('orders.show', ['order' => $order->load('items.product', 'reviews')]);
    }

    public function receipt(Order $order): View
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        return view('orders.receipt', ['order' => $order->load('items')]);
    }

    public function guestShow(Order $order, string $token): View
    {
        $this->authorizeGuestOrder($order, $token);

        return view('orders.guest-show', ['order' => $order->load('items.product')]);
    }

    public function guestReceipt(Order $order, string $token): View
    {
        $this->authorizeGuestOrder($order, $token);

        return view('orders.receipt', ['order' => $order->load('items')]);
    }

    public function updateDetails(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);

        return $this->updateCustomerDetails($request, $order, function (Order $lockedOrder): void {
            abort_unless($lockedOrder->user_id === auth()->id(), 403);
        });
    }

    public function guestUpdateDetails(Request $request, Order $order, string $token): RedirectResponse
    {
        $this->authorizeGuestOrder($order, $token);

        return $this->updateCustomerDetails($request, $order, function (Order $lockedOrder) use ($token): void {
            $this->authorizeGuestOrder($lockedOrder, $token);
        });
    }

    public function trackForm(): View
    {
        return view('orders.track', ['order' => null]);
    }

    public function track(Request $request): View
    {
        BotProtection::ensureHuman($request);

        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:80'],
            'mobile' => ['required', 'string', 'max:30'],
        ]);

        $order = Order::where('order_number', trim($data['order_number']))->first();
        $providedMobile = $this->normalizedMobile($data['mobile']);
        $orderMobile = $order ? $this->normalizedMobile($order->mobile) : '';

        if (! $order || $providedMobile === '' || ! hash_equals($orderMobile, $providedMobile)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'order_number' => 'No matching order was found. Check the order number and mobile number.',
            ]);
        }

        return view('orders.track', ['order' => $order]);
    }

    public function deliveryPayment(Order $order): View
    {
        $this->authorizeDeliveryPayment($order);

        return view('orders.delivery-payment', [
            'order' => $order,
            'settings' => StoreSetting::deliverySettings(),
        ]);
    }

    public function updateDeliveryPayment(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeDeliveryPayment($order);

        $hasPaymentScreenshot = $request->hasFile('delivery_payment_proof');
        $hasPaymentDetails = filled($request->input('delivery_payment_mobile'))
            || filled($request->input('delivery_transaction_id'));

        $data = $request->validate([
            'delivery_payment_method' => ['required', 'in:Bkash,Nagad,Rocket'],
            'delivery_payment_mobile' => [Rule::requiredIf(! $hasPaymentScreenshot), 'nullable', 'string', 'max:30'],
            'delivery_transaction_id' => [Rule::requiredIf(! $hasPaymentScreenshot), 'nullable', 'string', 'max:120'],
            'delivery_payment_proof' => [Rule::requiredIf(! $hasPaymentDetails), 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'delivery_payment_mobile.required' => 'Enter the payment mobile number and transaction ID, or upload a payment screenshot.',
            'delivery_transaction_id.required' => 'Enter the payment mobile number and transaction ID, or upload a payment screenshot.',
            'delivery_payment_proof.required' => 'Upload a payment screenshot, or enter both payment mobile number and transaction ID.',
        ]);

        $paymentProofPath = $request->hasFile('delivery_payment_proof')
            ? $request->file('delivery_payment_proof')->store('delivery-payment-proofs', 'local')
            : null;

        try {
            $order->update([
                ...$data,
                'delivery_payment_proof' => $paymentProofPath,
                'delivery_charge_payment_option' => 'pay_now',
                'status' => 'pending',
            ]);
        } catch (Throwable $exception) {
            if ($paymentProofPath) {
                Storage::disk('local')->delete($paymentProofPath);
            }

            throw $exception;
        }

        return redirect()->route('orders.show', $order)->with('success', 'Delivery charge payment submitted. Admin will review and confirm your order.');
    }

    private function authorizeDeliveryPayment(Order $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later', 404);
    }

    private function authorizeGuestOrder(Order $order, string $token): void
    {
        abort_unless($order->user_id === null && $order->guest_token && hash_equals($order->guest_token, $token), 403);
    }

    private function updateCustomerDetails(Request $request, Order $order, callable $authorize): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $data, $authorize): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            $authorize($lockedOrder);

            if (! $lockedOrder->customerCanEditDetails()) {
                throw ValidationException::withMessages([
                    'order_details' => 'Order details can only be changed before the order is confirmed.',
                ]);
            }

            $lockedOrder->update([
                'customer_name' => trim($data['customer_name']),
                'email' => trim($data['email']),
                'mobile' => trim($data['mobile']),
                'address' => trim($data['address']),
            ]);
        });

        return back()->with('success', 'Order contact and delivery details updated successfully.');
    }

    private function normalizedMobile(?string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', (string) $mobile);

        if (strlen($digits) === 13 && str_starts_with($digits, '880')) {
            return '0'.substr($digits, 3);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '0'.$digits;
        }

        return $digits;
    }
}
