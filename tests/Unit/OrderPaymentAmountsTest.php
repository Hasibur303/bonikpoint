<?php

namespace Tests\Unit;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderPaymentAmountsTest extends TestCase
{
    public function test_advance_delivery_payment_is_deducted_from_the_due_amount(): void
    {
        $order = new Order([
            'status' => 'confirmed',
            'total' => 2010,
            'shipping' => 120,
            'advance_delivery_required' => true,
            'delivery_charge_payment_option' => 'pay_now',
        ]);

        $this->assertSame(120.0, $order->paidAmount());
        $this->assertSame(1890.0, $order->dueAmount());
    }

    public function test_pay_later_order_keeps_the_full_total_due(): void
    {
        $order = new Order([
            'status' => 'waiting_delivery_charge',
            'total' => 2010,
            'shipping' => 120,
            'advance_delivery_required' => true,
            'delivery_charge_payment_option' => 'pay_later',
        ]);

        $this->assertSame(0.0, $order->paidAmount());
        $this->assertSame(2010.0, $order->dueAmount());
    }

    public function test_delivered_order_has_no_remaining_due_amount(): void
    {
        $order = new Order([
            'status' => 'delivered',
            'total' => 2010,
            'shipping' => 120,
            'advance_delivery_required' => true,
            'delivery_charge_payment_option' => 'pay_now',
        ]);

        $this->assertSame(2010.0, $order->paidAmount());
        $this->assertSame(0.0, $order->dueAmount());
    }
}
