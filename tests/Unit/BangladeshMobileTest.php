<?php

namespace Tests\Unit;

use App\Rules\BangladeshMobile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BangladeshMobileTest extends TestCase
{
    #[DataProvider('validNumbers')]
    public function test_it_normalizes_valid_bangladeshi_mobile_numbers(string $input, string $expected): void
    {
        $this->assertSame($expected, BangladeshMobile::normalize($input));
    }

    public static function validNumbers(): array
    {
        return [
            'local' => ['01712345678', '01712345678'],
            'international' => ['+8801712345678', '01712345678'],
            'international without plus' => ['8801712345678', '01712345678'],
            'formatted local' => ['01712-345678', '01712345678'],
            'formatted international' => ['+880 1712 345678', '01712345678'],
        ];
    }

    #[DataProvider('invalidNumbers')]
    public function test_it_rejects_invalid_mobile_numbers(mixed $input): void
    {
        $this->assertNull(BangladeshMobile::normalize($input));
    }

    public static function invalidNumbers(): array
    {
        return [
            'too short' => ['0171234567'],
            'too long' => ['017123456789'],
            'invalid operator prefix' => ['01212345678'],
            'landline' => ['029123456'],
            'letters' => ['01712ABC678'],
            'empty' => [''],
            'not a string' => [1712345678],
        ];
    }
}
