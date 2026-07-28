<?php

namespace Tests\Feature;

use App\Http\Middleware\RecordSiteVisit;
use Tests\TestCase;

class GuestCheckoutFlowTest extends TestCase
{
    public function test_checkout_start_sends_a_guest_directly_to_guest_checkout(): void
    {
        $this->withoutMiddleware(RecordSiteVisit::class)
            ->get(route('checkout.start'))
            ->assertRedirect(route('guest.checkout.create'));
    }

    public function test_guest_order_tracking_routes_are_registered(): void
    {
        $routes = app('router')->getRoutes();

        $this->assertNotNull($routes->getByName('guest.orders.track'));
        $this->assertNotNull($routes->getByName('guest.orders.track.lookup'));
    }
}
