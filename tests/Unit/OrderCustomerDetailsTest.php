<?php

namespace Tests\Unit;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderCustomerDetailsTest extends TestCase
{
    public function test_customer_can_edit_details_before_confirmation(): void
    {
        foreach (['waiting_delivery_charge', 'pending'] as $status) {
            $order = new Order(['status' => $status]);

            $this->assertTrue($order->customerCanEditDetails(), "Expected {$status} orders to remain editable.");
        }
    }

    public function test_customer_cannot_edit_details_after_confirmation_or_closure(): void
    {
        foreach (['confirmed', 'processing', 'delivered', 'cancelled'] as $status) {
            $order = new Order(['status' => $status]);

            $this->assertFalse($order->customerCanEditDetails(), "Expected {$status} orders to be locked.");
        }
    }

    public function test_customer_cannot_edit_details_after_courier_submission(): void
    {
        $order = new Order([
            'status' => 'pending',
            'steadfast_consignment_id' => '123456',
        ]);

        $this->assertFalse($order->customerCanEditDetails());
    }
}
