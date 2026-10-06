<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductVisitor
{
    public static function hash(Request $request, bool $forLike = false): string
    {
        if ($forLike && $request->user()) {
            return hash('sha256', 'user:'.$request->user()->getAuthIdentifier());
        }

        if (! $request->session()->has('product_visitor')) {
            $request->session()->put('product_visitor', (string) Str::uuid());
        }

        return hash('sha256', 'visitor:'.$request->session()->get('product_visitor'));
    }
}
