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

    public function test_offline_courier_cod_remains_due_until_delivery(): void
    {
        $order = new Order([
            'status' => 'confirmed',
            'is_offline_sale' => true,
            'requires_courier' => true,
            'offline_payment_collected' => false,
            'total' => 2160,
            'shipping' => 60,
        ]);

        $this->assertSame(0.0, $order->paidAmount());
        $this->assertSame(2160.0, $order->dueAmount());
        $this->assertTrue($order->canSendToSteadfast());
    }

    public function test_paid_offline_courier_order_has_zero_cod_due(): void
    {
        $order = new Order([
            'status' => 'confirmed',
            'is_offline_sale' => true,
            'requires_courier' => true,
            'offline_payment_collected' => true,
            'total' => 2160,
        ]);

        $this->assertSame(2160.0, $order->paidAmount());
        $this->assertSame(0.0, $order->dueAmount());
        $this->assertTrue($order->canSendToSteadfast());
    }

    public function test_counter_sale_cannot_be_sent_to_steadfast(): void
    {
        $order = new Order([
            'status' => 'confirmed',
            'is_offline_sale' => true,
            'requires_courier' => false,
            'offline_payment_collected' => true,
            'total' => 2000,
        ]);

        $this->assertFalse($order->canSendToSteadfast());
    }

    public function test_active_steadfast_parcel_cannot_be_permanently_deleted(): void
    {
        $order = new Order([
            'status' => 'processing',
            'steadfast_consignment_id' => '123456',
        ]);

        $this->assertTrue($order->hasActiveSteadfastShipment());
        $this->assertTrue($order->shouldRestoreStockWhenDeleted());

        $order->status = 'delivered';

        $this->assertFalse($order->hasActiveSteadfastShipment());
        $this->assertFalse($order->shouldRestoreStockWhenDeleted());
    }

    public function test_unfulfilled_order_restores_stock_when_deleted(): void
    {
        foreach (['waiting_delivery_charge', 'pending', 'confirmed', 'processing'] as $status) {
            $order = new Order(['status' => $status]);

            $this->assertTrue($order->shouldRestoreStockWhenDeleted());
        }

        foreach (['delivered', 'cancelled'] as $status) {
            $order = new Order(['status' => $status]);

            $this->assertFalse($order->shouldRestoreStockWhenDeleted());
        }
    }
}
