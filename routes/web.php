<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BkashController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lang/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'bn'], true)) {
        abort(404);
    }
    session(['locale' => $locale]);

    return redirect()->back();
})->name('lang.switch');

Route::post('/auth/request-otp', [AuthController::class, 'requestOtp'])->middleware('throttle:otp-request');
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:otp-verify');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::post('/checkout', [CheckoutController::class, 'checkout'])->middleware(['auth', 'throttle:checkout'])->name('checkout');
Route::post('/pay/bkash/create', [BkashController::class, 'create'])->middleware(['auth', 'throttle:bkash-create'])->name('bkash.create');
Route::get('/pay/bkash/callback', [BkashController::class, 'callback'])->name('bkash.callback');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::post('/seed-tour', function (Request $request) {
    if (! app()->environment('production')) {
        abort(403);
    }

    $token = (string) env('SEED_TOKEN', '');
    if ($token === '' || $request->header('X-Seed-Token') !== $token) {
        abort(403);
    }

    Artisan::call('db:seed', [
        '--class' => 'Database\\Seeders\\DatabaseSeeder',
        '--force' => true,
    ]);

    return response()->json(['status' => 'ok']);
})->middleware('throttle:seed-tour')->name('seed.tour');

Route::prefix('admin')->middleware(['auth', 'admin', 'throttle:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/bookings/{booking}', [AdminController::class, 'show'])->name('admin.bookings.show');
    Route::post('/bookings/{booking}/cancel', [AdminController::class, 'cancel'])->name('admin.bookings.cancel');
});
