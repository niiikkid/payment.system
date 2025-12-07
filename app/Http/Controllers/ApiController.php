<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Http\Resources\ApiTokenAllowedIpResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ApiController extends Controller
{
    public function __invoke(): Response
    {
        $user = Auth::user();
        $token = null;

        if ($user) {
            $token = $user->apiTokens()->first();
            if (!$token) {
                $token = $user->apiTokens()->create([
                    'name' => 'Default',
                    'token' => $this->generateUniqueToken(),
                ]);
            }
        }

        $allowedIps = $token
            ? ApiTokenAllowedIpResource::collection(
                $token->allowedIps()->orderByDesc('created_at')->get()
            )->resolve()
            : [];

        return $this->inertia('api/Index', [
            'publicApiKey' => $token?->token ?? '',
            'apiBaseUrl' => url('/api/v1'),
            'apiTokenId' => $token?->id,
            'allowedIps' => $allowedIps,
        ]);
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (ApiToken::query()->where('token', $token)->exists());

        return $token;
    }
}


