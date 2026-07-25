<?php

namespace Tests\Feature;

use App\Support\BotProtection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BotProtectionTest extends TestCase
{
    public function test_it_allows_requests_while_turnstile_is_not_configured(): void
    {
        config(['turnstile.enabled' => false]);

        BotProtection::ensureHuman(Request::create('/guest-checkout', 'POST'));

        $this->assertTrue(true);
    }

    public function test_it_rejects_a_completed_honeypot_field(): void
    {
        config(['turnstile.enabled' => false]);

        $this->expectException(ValidationException::class);

        BotProtection::ensureHuman(Request::create('/guest-checkout', 'POST', [
            'website' => 'https://spam.example',
        ]));
    }
}
