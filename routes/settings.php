<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\Settings\LoginHistoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::get('settings/appearance', function () {
        $locale = app()->getLocale();
        $basePath = rtrim(config('inertia-lang.lang_path', resource_path('lang')), '/');
        $path = "{$basePath}/{$locale}";

        $files = [];
        if (is_dir($path)) {
            $files = array_values(array_filter(array_map(function ($file) {
                return str_ends_with($file, '.php')
                    ? pathinfo($file, PATHINFO_FILENAME)
                    : null;
            }, scandir($path) ?: [])));
        }

        \syncLangFiles($files);

        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    Route::get('settings/login-history', [LoginHistoryController::class, 'index'])
        ->name('login-history.index');
});
