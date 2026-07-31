<?php

namespace Tests\Unit;

use App\Http\Controllers\CartController;
use ReflectionMethod;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    public function test_it_counts_only_other_cart_lines_for_the_same_product(): void
    {
        $cart = [
            10 => ['quantity' => 2],
            '10:color-4' => ['quantity' => 3],
            '10:flavor-7' => ['quantity' => 4],
            12 => ['quantity' => 9],
        ];

        $method = new ReflectionMethod(CartController::class, 'quantityForProduct');
        $quantity = $method->invoke(new CartController, $cart, 10, '10:color-4');

        $this->assertSame(6, $quantity);
    }

    public function test_it_handles_a_different_product_when_the_cart_already_has_an_item(): void
    {
        $cart = [
            10 => ['quantity' => 1],
        ];

        $method = new ReflectionMethod(CartController::class, 'quantityForProduct');
        $quantity = $method->invoke(new CartController, $cart, 1, '1');

        $this->assertSame(0, $quantity);
    }
}
