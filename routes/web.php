<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CallbackLogController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Dev\CallbackSandboxController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ImpersonationController;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('lang/{locale}', function (string $locale) {
    $available = ['en', 'ru', 'uk', 'zh', 'kk'];

    if (! in_array($locale, $available, true)) {
        abort(404);
    }

    session(['locale' => $locale]);
    app()->setLocale($locale);

    return back();
})->name('locale.switch');

// Public payment page
Route::get('pay/{invoice}', [InvoiceController::class, 'public'])->name('invoices.public');
Route::get('pay/{invoice}/data', [InvoiceController::class, 'publicData'])->name('invoices.public.data');
Route::get('pay/{invoice}/qr', [InvoiceController::class, 'publicQr'])->name('invoices.public.qr');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::patch('addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');

    // Invoices
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::patch('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::post('invoices/{invoice}/send-callback', [InvoiceController::class, 'sendCallback'])->name('invoices.send-callback');

    // Callback Logs
    Route::get('callback-logs', [CallbackLogController::class, 'index'])->name('callback-logs.index');

    // App Settings (глобальные настройки проекта) — только admin
    Route::middleware('role:admin')->group(function () {
        Route::get('app-settings', [\App\Http\Controllers\AppSettingsController::class, 'index'])->name('app-settings.index');
        Route::put('app-settings', [\App\Http\Controllers\AppSettingsController::class, 'update'])->name('app-settings.update');
    });

    // API Docs & Playground (не в настройках)
    Route::get('api', ApiController::class)->name('api.docs');
});

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
        Route::post('users', [UsersController::class, 'store'])->name('users.store');
        Route::patch('users/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::post('impersonate/{user}', [ImpersonationController::class, 'start'])->name('impersonate.start');
    });

Route::post('impersonate/leave', [ImpersonationController::class, 'leave'])
    ->middleware('auth')
    ->name('impersonate.leave');

require __DIR__.'/settings.php';

if (app()->environment(['local', 'development', 'dev'])) {
    Route::post('/dev/callback-sandbox', CallbackSandboxController::class)->name('dev.callback.sandbox');
}
