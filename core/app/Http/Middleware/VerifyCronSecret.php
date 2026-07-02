<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCronSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('tenancy.cron_secret');

        if (empty($secret)) {
            abort(404);
        }

        $provided = $request->header('X-Cron-Secret', $request->query('secret'));

        if (!hash_equals($secret, (string) $provided)) {
            abort(403, 'Invalid cron secret.');
        }

        return $next($request);
    }
}
