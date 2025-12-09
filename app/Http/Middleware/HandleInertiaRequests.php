<?php

namespace App\Http\Middleware;

use App\Contracts\Lang\LanguageSettingsServiceContract;
use App\Enums\NotificationChannel;
use App\Models\Notification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    private const LOCALE_SHARE_KEY = 'locales';

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        if ($user) {
            $user->loadMissing('roles:id,name');
        }

        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $languageSettingsService = app(LanguageSettingsServiceContract::class);

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user,
                'roles' => $user ? $user->roles->pluck('name') : [],
                'is_admin' => $user ? $user->hasRole('admin') : false,
                'is_impersonated' => $request->session()->get('impersonating', false),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'notifications' => [
                'unread_count' => fn () => $user
                    ? Notification::query()
                        ->where('user_id', $user->id)
                        ->where('channel', NotificationChannel::IN_APP)
                        ->whereNull('read_at')
                        ->count()
                    : 0,
            ],
            'locale' => app()->getLocale(),
            self::LOCALE_SHARE_KEY => [
                'available' => $languageSettingsService->availableLocales(),
                'enabled' => $languageSettingsService->enabledLocaleCodes(),
            ],
        ];
    }
}
