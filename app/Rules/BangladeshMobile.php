<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BangladeshMobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (self::normalize($value) === null) {
            $fail('Enter a valid Bangladeshi mobile number, such as 01712345678.');
        }
    }

    public static function normalize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '' || ! preg_match('/^[\d+\s().-]+$/', $value)) {
            return null;
        }

        $compact = preg_replace('/[\s().-]+/', '', $value);

        if (str_starts_with($compact, '+880')) {
            $compact = '0'.substr($compact, 4);
        } elseif (str_starts_with($compact, '880')) {
            $compact = '0'.substr($compact, 3);
        }

        return preg_match('/^01[3-9]\d{8}$/', $compact) ? $compact : null;
    }
}
