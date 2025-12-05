<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\LoginHistory\LoginHistoryServiceContract;
use Illuminate\Auth\Events\Login;

class RecordUserLogin
{
    public function __construct(
        private readonly LoginHistoryServiceContract $loginHistoryService,
    ) {
    }

    public function handle(Login $event): void
    {
        $this->loginHistoryService->recordLogin($event->user, request());
    }
}

