<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Http\Resources\ApiTokenAllowedIpResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

    public function regenerate(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        $token = $user->apiTokens()->first();

        if (!$token) {
            abort(404, 'API token not found');
        }

        $token->update([
            'token' => $this->generateUniqueToken(),
        ]);

        return redirect()
            ->route('api.docs')
            ->with('success', __('frontend.api.token.refreshed'));
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (ApiToken::query()->where('token', $token)->exists());

        return $token;
    }
}


