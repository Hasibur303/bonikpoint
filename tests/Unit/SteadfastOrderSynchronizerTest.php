<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\SteadfastOrderSynchronizer;
use Tests\TestCase;

class SteadfastOrderSynchronizerTest extends TestCase
{
    public function test_delivered_courier_status_marks_the_store_order_delivered(): void
    {
        $order = new Order(['status' => 'processing']);

        $updates = app(SteadfastOrderSynchronizer::class)->updatesFor($order, 'Delivered');

        $this->assertSame('delivered', $updates['steadfast_status']);
        $this->assertSame('delivered', $updates['status']);
    }

    public function test_cancelled_store_order_is_never_reopened_by_courier_sync(): void
    {
        $order = new Order(['status' => 'cancelled']);

        $updates = app(SteadfastOrderSynchronizer::class)->updatesFor($order, 'delivered');

        $this->assertSame('delivered', $updates['steadfast_status']);
        $this->assertArrayNotHasKey('status', $updates);
    }

    public function test_non_delivered_courier_status_stays_separate_for_admin_review(): void
    {
        $order = new Order(['status' => 'processing']);

        $updates = app(SteadfastOrderSynchronizer::class)->updatesFor($order, 'Partial Delivered');

        $this->assertSame('partial_delivered', $updates['steadfast_status']);
        $this->assertArrayNotHasKey('status', $updates);
    }
}
