<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function value(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function deliverySettings(): array
    {
        return [
            'inside_dhaka_delivery_charge' => (int) static::value('inside_dhaka_delivery_charge', '60'),
            'outside_dhaka_delivery_charge' => (int) static::value('outside_dhaka_delivery_charge', '120'),
            'bkash_number' => static::value('bkash_number', '01832510343'),
            'nagad_number' => static::value('nagad_number', '01832510343'),
            'rocket_number' => static::value('rocket_number', '018325103435'),
            'delivery_pay_later_note_bn' => static::value('delivery_pay_later_note_bn', 'ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত আপনার অর্ডার সম্পূর্ণভাবে কনফার্ম হবে না।'),
        ];
    }
}
