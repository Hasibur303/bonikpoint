<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\SteadfastCourier;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SteadfastCourierTest extends TestCase
{
    public function test_it_submits_the_remaining_due_as_cod(): void
    {
        config([
            'services.steadfast.api_key' => 'test-api-key',
            'services.steadfast.secret_key' => 'test-secret-key',
            'services.steadfast.base_url' => 'https://portal.packzy.com/api/v1',
        ]);

        Http::fake([
            'portal.packzy.com/api/v1/create_order' => Http::response([
                'status' => 200,
                'message' => 'Consignment has been created successfully.',
                'consignment' => [
                    'consignment_id' => 123456,
                    'tracking_code' => 'TRACK-123',
                    'status' => 'in_review',
                ],
            ]),
        ]);

        $order = new Order([
            'order_number' => 'BP-2026-1001',
            'customer_name' => 'Test Customer',
            'mobile' => '+8801712345678',
            'address' => 'House 10, Road 2',
            'city' => 'Dhaka',
            'status' => 'confirmed',
            'shipping' => 60,
            'total' => 1360,
            'discount_amount' => 200,
            'advance_delivery_required' => true,
            'delivery_charge_payment_option' => 'pay_now',
        ]);
        $order->setRelation('items', collect([
            new OrderItem([
                'product_name' => 'Test Product',
                'quantity' => 2,
            ]),
        ]));

        $shipment = app(SteadfastCourier::class)->createOrder($order);

        $this->assertSame('123456', $shipment['consignment_id']);
        $this->assertSame('TRACK-123', $shipment['tracking_code']);
        $this->assertSame('in_review', $shipment['status']);

        Http::assertSent(fn ($request) => $request->url() === 'https://portal.packzy.com/api/v1/create_order'
            && $request->hasHeader('Api-Key', 'test-api-key')
            && $request->hasHeader('Secret-Key', 'test-secret-key')
            && $request['recipient_phone'] === '01712345678'
            && (float) $request['cod_amount'] === 1300.0
            && $request['invoice'] === 'BP-2026-1001');
    }

    public function test_it_reads_the_current_delivery_status(): void
    {
        config([
            'services.steadfast.api_key' => 'test-api-key',
            'services.steadfast.secret_key' => 'test-secret-key',
            'services.steadfast.base_url' => 'https://portal.packzy.com/api/v1',
        ]);

        Http::fake([
            'portal.packzy.com/api/v1/status_by_cid/123456' => Http::response([
                'status' => 200,
                'delivery_status' => 'delivered',
            ]),
        ]);

        $this->assertSame('delivered', app(SteadfastCourier::class)->status('123456'));
    }
}
