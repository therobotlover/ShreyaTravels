<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BkashService
{
    public function createPayment(Booking $booking): string
    {
        if (! config('bkash.enabled')) {
            throw new \RuntimeException(__('messages.bkash_not_configured'));
        }

        if (config('bkash.base_url') === 'mock') {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'provider' => 'bkash',
                'provider_payment_id' => (string) Str::uuid(),
                'amount' => $booking->total_amount,
                'status' => Payment::STATUS_INITIATED,
                'raw_payload' => ['mode' => config('bkash.mode')],
            ]);

            Log::info('bkash.payment.initiated', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
            ]);

            return route('bkash.callback', [
                'paymentID' => $payment->provider_payment_id,
                'status' => 'success',
            ]);
        }

        $token = $this->getToken();
        $callbackUrl = (string) (config('bkash.callback_url') ?: route('bkash.callback'));
        $payload = [
            'amount' => number_format((float) $booking->total_amount, 2, '.', ''),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $booking->reference ?: ('ST-'.Str::uuid()),
            'callbackURL' => $callbackUrl,
        ];

        $response = $this->post('/payment/create', $token, $payload);
        $paymentId = (string) ($response['paymentID'] ?? '');
        $bkashUrl = (string) ($response['bkashURL'] ?? '');

        if ($paymentId === '' || $bkashUrl === '') {
            Log::error('bkash.payment.create.missing_fields', ['response' => $response]);
            throw new \RuntimeException(__('messages.bkash_start_failed'));
        }

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'provider' => 'bkash',
            'provider_payment_id' => $paymentId,
            'amount' => $booking->total_amount,
            'status' => Payment::STATUS_INITIATED,
            'raw_payload' => [
                'request' => $payload,
                'response' => $response,
                'mode' => config('bkash.mode'),
            ],
        ]);

        Log::info('bkash.payment.initiated', [
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
            'provider_payment_id' => $paymentId,
        ]);

        return $bkashUrl;
    }

    public function executePayment(Payment $payment): array
    {
        if (! config('bkash.enabled')) {
            throw new \RuntimeException(__('messages.bkash_not_configured'));
        }

        $token = $this->getToken();
        $response = $this->post('/payment/execute/'.$payment->provider_payment_id, $token);

        $payment->trx_id = (string) ($response['trxID'] ?? $payment->trx_id);
        $payment->raw_payload = array_merge($payment->raw_payload ?? [], [
            'execute_response' => $response,
        ]);
        $payment->save();

        return $response;
    }

    private function getToken(): string
    {
        $response = Http::withHeaders([
            'username' => (string) config('bkash.username'),
            'password' => (string) config('bkash.password'),
        ])->post($this->endpoint('/token/grant'), [
            'app_key' => (string) config('bkash.app_key'),
            'app_secret' => (string) config('bkash.app_secret'),
        ]);

        if (! $response->successful()) {
            Log::error('bkash.token.failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException(__('messages.bkash_auth_failed'));
        }

        $data = $response->json() ?? [];
        $status = strtolower((string) ($data['status'] ?? ''));
        $token = (string) ($data['id_token'] ?? '');
        if ($status !== 'success' || $token === '') {
            Log::error('bkash.token.missing', ['body' => $data]);
            throw new \RuntimeException((string) ($data['msg'] ?? __('messages.bkash_token_invalid')));
        }

        return $token;
    }

    private function post(string $path, string $token, array $payload = []): array
    {
        $response = Http::withHeaders([
            'authorization' => $token,
            'x-app-key' => (string) config('bkash.app_key'),
        ])->post($this->endpoint($path), $payload);

        if (! $response->successful()) {
            Log::error('bkash.api.failed', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException(__('messages.bkash_request_failed'));
        }

        return $response->json() ?? [];
    }

    private function endpoint(string $path): string
    {
        return rtrim((string) config('bkash.base_url'), '/').'/'.ltrim($path, '/');
    }
}
