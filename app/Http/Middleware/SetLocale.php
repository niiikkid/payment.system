<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Contracts\Lang\LanguageSettingsServiceContract;
use App\Support\LocaleOptions;
use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        /** @var LanguageSettingsServiceContract $languageSettingsService */
        $languageSettingsService = app(LanguageSettingsServiceContract::class);

        $localeFromSession = $request->session()->get('locale', config('app.locale'));
        $normalizedLocale = LocaleOptions::normalize((string) $localeFromSession) ?? config('app.locale');

        $allowed = $languageSettingsService->enabledLocaleCodes();
        $locale = in_array($normalizedLocale, $allowed, true) ? $normalizedLocale : config('app.locale');

        app()->setLocale($locale);
        if ($localeFromSession !== $locale) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}

