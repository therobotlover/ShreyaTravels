<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('otp-request', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email', '')));
            $limits = config('limits.otp_request');

            return [
                Limit::perMinutes($limits['ip']['window_minutes'], $limits['ip']['max'])->by($request->ip()),
                Limit::perMinutes($limits['email']['window_minutes'], $limits['email']['max'])->by('email:'.$email),
                Limit::perMinutes($limits['ip_email']['window_minutes'], $limits['ip_email']['max'])->by($request->ip().'|'.$email),
            ];
        });

        RateLimiter::for('otp-verify', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email', '')));
            $limits = config('limits.otp_verify');

            return [
                Limit::perMinutes($limits['ip']['window_minutes'], $limits['ip']['max'])->by($request->ip()),
                Limit::perMinutes($limits['email']['window_minutes'], $limits['email']['max'])->by('email:'.$email),
            ];
        });

        RateLimiter::for('checkout', function (Request $request) {
            $limits = config('limits.checkout');
            $userKey = $request->user()?->id ?? 'guest';

            return [
                Limit::perMinutes($limits['user']['window_minutes'], $limits['user']['max'])->by('user:'.$userKey),
                Limit::perMinutes($limits['ip']['window_minutes'], $limits['ip']['max'])->by($request->ip()),
            ];
        });

        RateLimiter::for('bkash-create', function (Request $request) {
            $limits = config('limits.bkash_create');
            $userKey = $request->user()?->id ?? 'guest';

            return [
                Limit::perMinutes($limits['user']['window_minutes'], $limits['user']['max'])->by('user:'.$userKey),
                Limit::perMinutes($limits['ip']['window_minutes'], $limits['ip']['max'])->by($request->ip()),
            ];
        });

        RateLimiter::for('admin', function (Request $request) {
            $limits = config('limits.admin');
            $email = $request->user()?->email ?? 'unknown';

            return [
                Limit::perMinutes($limits['user']['window_minutes'], $limits['user']['max'])->by('admin:'.$email),
            ];
        });

        RateLimiter::for('seed-tour', function (Request $request) {
            return [
                Limit::perMinute(1)->by('seed-tour:'.$request->ip()),
            ];
        });
    }
}
