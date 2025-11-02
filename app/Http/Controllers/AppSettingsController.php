<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\AppSettings\AppSettingsServiceContract;
use App\Enums\Currency;
use App\Http\Requests\UpdateAppSettingsRequest;
use App\Http\Resources\AppSettingResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AppSettingsController extends Controller
{
    public function index(AppSettingsServiceContract $service): Response
    {
        $settings = $service->all();
        return Inertia::render('app-settings/Index', [
            'settings' => AppSettingResource::collection($settings)->resolve(),
            'currencies' => array_map(fn (Currency $c) => $c->value, Currency::cases()),
        ]);
    }

    public function update(UpdateAppSettingsRequest $request, AppSettingsServiceContract $service): RedirectResponse
    {
        foreach ($request->toMinorMap(app('App\\Contracts\\Money\\MoneyServiceContract')) as $item) {
            $service->upsert($item['currency'], $item['min_minor'], $item['max_minor']);
        }

        return back();
    }
}


