<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\SteadfastOrderSynchronizer;
use Illuminate\Console\Command;
use Throwable;

class SyncSteadfastOrders extends Command
{
    protected $signature = 'steadfast:sync-orders {--limit=100 : Maximum orders to check in one run}';

    protected $description = 'Refresh active Steadfast parcels and mark delivered store orders automatically';

    public function handle(SteadfastOrderSynchronizer $synchronizer): int
    {
        $limit = max(1, min((int) $this->option('limit'), 500));
        $orders = Order::query()
            ->whereNotNull('steadfast_consignment_id')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->where(function ($query) {
                $query->whereNull('steadfast_last_synced_at')
                    ->orWhere('steadfast_last_synced_at', '<=', now()->subMinutes(4));
            })
            ->oldest('steadfast_last_synced_at')
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No Steadfast orders currently need synchronization.');

            return self::SUCCESS;
        }

        $synced = 0;
        $delivered = 0;
        $failed = 0;

        foreach ($orders as $order) {
            try {
                $previousStatus = $order->status;
                $courierStatus = $synchronizer->sync($order);
                $order->refresh();
                $synced++;

                if ($previousStatus !== 'delivered' && $order->status === 'delivered') {
                    $delivered++;
                }

                $this->line("{$order->order_number}: {$courierStatus}");
            } catch (Throwable $exception) {
                report($exception);
                $failed++;
                $this->error("{$order->order_number}: {$exception->getMessage()}");
            }
        }

        $this->info("Steadfast sync complete. Checked: {$synced}; newly delivered: {$delivered}; failed: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
