<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SteadfastCourier
{
    public function configured(): bool
    {
        return filled(config('services.steadfast.api_key'))
            && filled(config('services.steadfast.secret_key'));
    }

    public function createOrder(Order $order): array
    {
        $this->ensureConfigured();
        $order->loadMissing('items');

        $response = $this->request()->post($this->endpoint('create_order'), [
            'invoice' => $this->invoice($order),
            'recipient_name' => Str::limit(trim($order->customer_name), 100, ''),
            'recipient_phone' => $this->phone($order->mobile),
            'recipient_address' => Str::limit($this->address($order), 250, ''),
            'cod_amount' => round($order->dueAmount(), 2),
            'note' => $this->note($order),
            'delivery_type' => 0,
        ]);

        $payload = $this->successfulPayload($response, 'Steadfast rejected the parcel submission.');
        $consignment = $payload['consignment'] ?? null;

        if (! is_array($consignment) || blank($consignment['consignment_id'] ?? null)) {
            throw new RuntimeException('Steadfast did not return a consignment ID. Please check the merchant panel before trying again.');
        }

        return [
            'consignment_id' => (string) $consignment['consignment_id'],
            'tracking_code' => filled($consignment['tracking_code'] ?? null)
                ? (string) $consignment['tracking_code']
                : null,
            'status' => (string) ($consignment['status'] ?? 'pending'),
        ];
    }

    public function status(string $consignmentId): string
    {
        $this->ensureConfigured();

        $response = $this->request()
            ->retry(2, 300, throw: false)
            ->get($this->endpoint('status_by_cid/'.rawurlencode($consignmentId)));

        $payload = $this->successfulPayload($response, 'Steadfast could not retrieve the parcel status.');
        $status = $payload['delivery_status'] ?? $payload['status_name'] ?? null;

        if (blank($status) || is_numeric($status)) {
            throw new RuntimeException('Steadfast returned an unreadable parcel status.');
        }

        return (string) $status;
    }

    private function request()
    {
        return Http::acceptJson()
            ->asJson()
            ->withHeaders([
                'Api-Key' => config('services.steadfast.api_key'),
                'Secret-Key' => config('services.steadfast.secret_key'),
            ])
            ->connectTimeout(8)
            ->timeout(20);
    }

    private function endpoint(string $path): string
    {
        return rtrim((string) config('services.steadfast.base_url'), '/').'/'.ltrim($path, '/');
    }

    private function successfulPayload(Response $response, string $fallback): array
    {
        $payload = $response->json();

        if (! $response->successful() || ! is_array($payload) || (isset($payload['status']) && (int) $payload['status'] !== 200)) {
            $message = is_array($payload) ? ($payload['message'] ?? null) : null;

            throw new RuntimeException(filled($message) ? (string) $message : $fallback);
        }

        return $payload;
    }

    private function ensureConfigured(): void
    {
        if (! $this->configured()) {
            throw new RuntimeException('Steadfast API credentials are not configured on this server.');
        }
    }

    private function invoice(Order $order): string
    {
        $invoice = preg_replace('/[^A-Za-z0-9_-]+/', '-', $order->order_number);

        return Str::limit(trim((string) $invoice, '-'), 60, '');
    }

    private function phone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) === 13 && str_starts_with($digits, '880')) {
            $digits = '0'.substr($digits, 3);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            $digits = '0'.$digits;
        }

        if (! preg_match('/^01\d{9}$/', $digits)) {
            throw new RuntimeException('The customer phone number must be a valid 11-digit Bangladeshi mobile number.');
        }

        return $digits;
    }

    private function address(Order $order): string
    {
        return collect([$order->address, $order->city])
            ->filter(fn ($part) => filled($part))
            ->map(fn ($part) => trim((string) $part))
            ->unique()
            ->implode(', ');
    }

    private function note(Order $order): string
    {
        $items = $order->items
            ->map(fn ($item) => $item->product_name.' x'.$item->quantity)
            ->implode(', ');
        $parts = collect([$items, $order->notes])
            ->filter(fn ($part) => filled($part))
            ->implode(' | ');

        return Str::limit($parts, 250, '');
    }
}
