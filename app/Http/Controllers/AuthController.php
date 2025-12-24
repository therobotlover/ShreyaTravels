<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function requestOtp(RequestOtpRequest $request)
    {
        $email = $this->normalizeEmail($request->validated()['email']);
        $lock = config('limits.otp_fail_lock');
        $failKey = 'otp_fail:'.$email;

        if (Cache::get($failKey, 0) >= $lock['threshold']) {
            return response()->json(['message' => __('messages.otp_locked')], 429);
        }

        $otp = random_int(100000, 999999);
        $ttlMinutes = (int) env('OTP_TTL_MINUTES', 10);

        Cache::put('otp:'.$email, Hash::make((string) $otp), now()->addMinutes($ttlMinutes));

        Mail::raw(__('messages.otp_email_body', ['otp' => $otp, 'minutes' => $ttlMinutes]), function ($message) use ($email) {
            $message->to($email)->subject(__('messages.otp_email_subject'));
        });

        logger()->info('auth.otp.requested', [
            'email' => $email,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => __('messages.otp_sent')]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $email = $this->normalizeEmail($request->validated()['email']);
        $otp = (string) $request->validated()['otp'];
        $lock = config('limits.otp_fail_lock');
        $failKey = 'otp_fail:'.$email;

        if (Cache::get($failKey, 0) >= $lock['threshold']) {
            return response()->json(['message' => __('messages.otp_locked')], 429);
        }

        $hashed = Cache::get('otp:'.$email);

        if (! $hashed || ! Hash::check($otp, $hashed)) {
            if (Cache::has($failKey)) {
                Cache::increment($failKey);
            } else {
                Cache::put($failKey, 1, now()->addMinutes($lock['window_minutes']));
            }

            return response()->json(['message' => __('messages.otp_invalid')], 422);
        }

        Cache::forget('otp:'.$email);
        Cache::forget($failKey);

        $user = User::firstOrCreate(['email' => $email]);
        Auth::login($user);

        logger()->info('auth.otp.verified', [
            'user_id' => $user->id,
            'email' => $email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => __('messages.otp_verified'),
            'redirect' => route('dashboard'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
