<?php

namespace App\Services;

use App\Models\Order;
use RuntimeException;
use Throwable;

class SteadfastOrderSynchronizer
{
    public function __construct(private readonly SteadfastCourier $courier)
    {
    }

    public function sync(Order $order): string
    {
        if (! $order->hasSteadfastShipment()) {
            throw new RuntimeException('This order has not been submitted to Steadfast yet.');
        }

        try {
            $status = $this->normalizeStatus($this->courier->status($order->steadfast_consignment_id));
            $order->update($this->updatesFor($order, $status));

            return $status;
        } catch (Throwable $exception) {
            $order->update([
                'steadfast_last_error' => $exception->getMessage(),
                'steadfast_last_synced_at' => now(),
            ]);

            throw $exception;
        }
    }

    public function updatesFor(Order $order, string $courierStatus): array
    {
        $updates = [
            'steadfast_status' => $this->normalizeStatus($courierStatus),
            'steadfast_last_synced_at' => now(),
            'steadfast_last_error' => null,
        ];

        if ($updates['steadfast_status'] === 'delivered' && $order->status !== 'cancelled') {
            $updates['status'] = 'delivered';
        }

        return $updates;
    }

    private function normalizeStatus(string $status): string
    {
        return (string) str($status)
            ->trim()
            ->lower()
            ->replace([' ', '-'], '_');
    }
}
