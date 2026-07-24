<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RecordSiteVisit
{
    /**
     * Record one anonymous visit per browser session for each calendar day.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->is('admin', 'admin/*') && $request->hasSession()) {
            $now = now();

            DB::table('site_visits')->insertOrIgnore([
                'visitor_hash' => hash('sha256', $request->session()->getId()),
                'visited_on' => $now->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $next($request);
    }
}
