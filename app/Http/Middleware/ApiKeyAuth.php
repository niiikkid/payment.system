<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next)
    {
        $provided = (string) $request->header('X-Api-Key', '');
        $expected = (string) config('services.public_api.key', '');

        if ($expected === '' || !hash_equals($expected, $provided)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $this->resolveUser();
        if ($user) {
            Auth::setUser($user);
        }

        return $next($request);
    }

    private function resolveUser(): ?User
    {
        $configuredId = config('services.public_api.user_id');
        if ($configuredId) {
            $user = User::query()->find($configuredId);
            if ($user) {
                return $user;
            }
        }

        $admin = User::query()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->orderBy('id')
            ->first();

        if ($admin) {
            return $admin;
        }

        return User::query()->orderBy('id')->first();
    }
}


