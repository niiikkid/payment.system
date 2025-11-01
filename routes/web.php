<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\InvoiceController;

Route::get('/', [HomeController::class, 'index'])->name('home');

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
});

require __DIR__.'/settings.php';
