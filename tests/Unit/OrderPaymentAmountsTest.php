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

    public function test_discounted_order_uses_the_adjusted_total_for_due_amount(): void
    {
        $order = new Order([
            'status' => 'confirmed',
            'subtotal' => 2000,
            'shipping' => 60,
            'total' => 1860,
            'adjustment_type' => 'fixed_discount',
            'adjustment_value' => 200,
            'discount_amount' => 200,
            'extra_charge_amount' => 0,
            'advance_delivery_required' => true,
            'delivery_charge_payment_option' => 'pay_now',
        ]);

        $this->assertTrue($order->hasAdjustment());
        $this->assertSame(2060.0, $order->originalTotal());
        $this->assertSame('Special discount', $order->adjustmentLabel());
        $this->assertSame(1800.0, $order->dueAmount());
    }

    public function test_percentage_discount_label_shows_the_rate(): void
    {
        $order = new Order([
            'adjustment_type' => 'percentage_discount',
            'adjustment_value' => 10,
            'discount_amount' => 200,
        ]);

        $this->assertSame('Special discount (10%)', $order->adjustmentLabel());
    }
}
