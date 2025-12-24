<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequestTiming
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $durationMs = (int) round((microtime(true) - $start) * 1000);

        $slowThresholdMs = (int) env('SLOW_REQUEST_MS', 1000);
        if ($durationMs >= $slowThresholdMs) {
            logger()->warning('request.slow', [
                'method' => $request->method(),
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'duration_ms' => $durationMs,
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
            ]);
        }

        $status = $response->getStatusCode();
        if ($status >= 500) {
            logger()->error('request.error', [
                'method' => $request->method(),
                'path' => $request->path(),
                'status' => $status,
                'duration_ms' => $durationMs,
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
            ]);
        }

        return $response;
    }
}
