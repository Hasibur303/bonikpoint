<?php

namespace App\Support;

use InvalidArgumentException;

class OrderAdjustmentCalculator
{
    public function calculate(float $subtotal, float $shipping, string $type, float $value): array
    {
        $subtotal = round($subtotal, 2);
        $shipping = round($shipping, 2);
        $value = round($value, 2);

        if ($value <= 0) {
            throw new InvalidArgumentException('Adjustment value must be greater than zero.');
        }

        $discount = 0.0;
        $extraCharge = 0.0;

        if ($type === 'percentage_discount') {
            if ($value > 100) {
                throw new InvalidArgumentException('Percentage discount cannot be greater than 100%.');
            }

            $discount = round($subtotal * ($value / 100), 2);
        } elseif ($type === 'fixed_discount') {
            $discount = $value;
        } elseif ($type === 'extra_charge') {
            $extraCharge = $value;
        } else {
            throw new InvalidArgumentException('Choose a valid adjustment type.');
        }

        if ($discount > $subtotal) {
            throw new InvalidArgumentException('The discount cannot be greater than the product subtotal.');
        }

        $total = round($subtotal + $shipping - $discount + $extraCharge, 2);

        if ($total > 99999999.99) {
            throw new InvalidArgumentException('The adjusted order total is too large.');
        }

        return [
            'discount_amount' => $discount,
            'extra_charge_amount' => $extraCharge,
            'total' => $total,
        ];
    }
}
