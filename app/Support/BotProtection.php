<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

final class BotProtection
{
    public static function ensureHuman(Request $request): void
    {
        // Hidden from customers, but simple automated form fillers usually complete it.
        if (filled($request->input('website'))) {
            self::reject();
        }

        if (! config('turnstile.enabled')) {
            return;
        }

        $token = (string) $request->input('cf-turnstile-response', '');

        if ($token === '') {
            self::reject('Please complete the verification and try again.');
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (ConnectionException) {
            self::reject('Verification is temporarily unavailable. Please try again.');
        }

        if (! $response->successful() || ! $response->json('success')) {
            self::reject('Please complete the verification and try again.');
        }
    }

    private static function reject(string $message = 'We could not verify this request. Please refresh the page and try again.'): never
    {
        throw ValidationException::withMessages([
            'bot_protection' => $message,
        ]);
    }
}
