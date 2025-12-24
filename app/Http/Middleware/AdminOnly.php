<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $allowed = collect(explode(',', (string) config('app.admin_emails', '')))
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter()
            ->contains(strtolower((string) $user?->email));

        if (! $user || ! $allowed) {
            abort(403);
        }

        return $next($request);
    }
}
