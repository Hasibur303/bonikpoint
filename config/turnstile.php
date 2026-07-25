<?php

return [
    'site_key' => env('TURNSTILE_SITE_KEY'),
    'secret_key' => env('TURNSTILE_SECRET_KEY'),
    'enabled' => (bool) (env('TURNSTILE_SITE_KEY') && env('TURNSTILE_SECRET_KEY')),
];
