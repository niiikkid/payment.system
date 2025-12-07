<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Планировщик: обновление балансов адресов каждую минуту
Schedule::command('addresses:sync-balances --only-active=1')->everyMinute();
// Планировщик: диспетчер задач парсинга курсов маркетов
Schedule::command('markets:dispatch BINANCE')->everySecond();
Schedule::command('markets:dispatch RAPIRA')->everySecond();
Schedule::command('markets:dispatch BYBIT')->everySecond();
