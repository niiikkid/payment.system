<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ApiController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('api/Index', [
            'publicApiKey' => config('services.public_api.key'),
            'apiBaseUrl' => url('/api/v1'),
        ]);
    }
}


