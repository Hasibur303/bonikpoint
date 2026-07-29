<?php

namespace Tests\Unit;

use App\Support\OrderAdjustmentCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OrderAdjustmentCalculatorTest extends TestCase
{
    private OrderAdjustmentCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new OrderAdjustmentCalculator;
    }

    public function test_it_calculates_a_fixed_discount(): void
    {
        $this->assertSame([
            'discount_amount' => 200.0,
            'extra_charge_amount' => 0.0,
            'total' => 1860.0,
        ], $this->calculator->calculate(2000, 60, 'fixed_discount', 200));
    }

    public function test_it_calculates_a_percentage_discount(): void
    {
        $this->assertSame([
            'discount_amount' => 200.0,
            'extra_charge_amount' => 0.0,
            'total' => 1860.0,
        ], $this->calculator->calculate(2000, 60, 'percentage_discount', 10));
    }

    public function test_it_calculates_an_extra_charge(): void
    {
        $this->assertSame([
            'discount_amount' => 0.0,
            'extra_charge_amount' => 200.0,
            'total' => 2260.0,
        ], $this->calculator->calculate(2000, 60, 'extra_charge', 200));
    }

    public function test_it_rejects_a_discount_greater_than_the_product_subtotal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot be greater than the product subtotal');

        $this->calculator->calculate(2000, 60, 'fixed_discount', 2001);
    }

    public function test_it_rejects_a_percentage_greater_than_one_hundred(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot be greater than 100%');

        $this->calculator->calculate(2000, 60, 'percentage_discount', 101);
    }
}
