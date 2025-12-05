<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next)
    {
        $provided = (string) $request->header('X-Api-Key', '');
        if ($provided === '') {
            return $this->unauthorized();
        }

        $token = ApiToken::query()
            ->with('user')
            ->where('token', $provided)
            ->first();

        if (!$token || !$token->user) {
            return $this->unauthorized();
        }

        $token->updateQuietly(['last_used_at' => Carbon::now()]);

        Auth::setUser($token->user);

        return $next($request);
    }

    private function unauthorized()
    {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }
}


