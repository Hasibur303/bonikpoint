<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireStorefrontEditMode
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->session()->get('storefront_edit_mode', false), 403, 'Turn on storefront edit mode first.');

        return $next($request);
    }
}
