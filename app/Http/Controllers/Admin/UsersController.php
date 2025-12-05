<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class UsersController extends Controller
{
    public function index(): Response
    {
        $paginator = User::query()
            ->with('roles:id,name')
            ->latest('id')
            ->paginate(20)
            ->through(fn (User $user) => (new UserResource($user))->resolve());

        return Inertia::render('admin/users/Index', [
            'users' => $paginator,
            'roleOptions' => [
                ['value' => 'admin', 'label' => 'Администратор'],
                ['value' => 'user', 'label' => 'Пользователь'],
            ],
            'currentUserId' => Auth::id(),
        ]);
    }

    public function store(StoreUserRequest $request, CreatesNewUsers $creator): JsonResponse|RedirectResponse
    {
        $payload = $request->validated();

        $user = $creator->create(Arr::only($payload, [
            'name',
            'email',
            'password',
            'password_confirmation',
        ]));

        $roleName = $payload['role'] ?? 'user';
        $role = $this->resolveRole($roleName);
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        event(new Registered($user));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Пользователь создан',
                'user' => (new UserResource($user))->resolve(),
            ], HttpResponse::HTTP_CREATED);
        }

        return back()->with('success', 'Пользователь создан');
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse|RedirectResponse
    {
        $payload = $request->validated();

        $user->fill(Arr::only($payload, ['name', 'email']));

        if (!empty($payload['password'])) {
            $user->password = $payload['password'];
        }

        $user->save();

        if (isset($payload['role'])) {
            if ($user->id === $request->user()?->id) {
                abort(HttpResponse::HTTP_FORBIDDEN, 'Нельзя менять собственную роль');
            }
            $role = $this->resolveRole($payload['role']);
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Пользователь обновлён',
                'user' => (new UserResource($user->fresh('roles')))->resolve(),
            ]);
        }

        return back()->with('success', 'Пользователь обновлён');
    }

    private function resolveRole(string $name): ?Role
    {
        if (!in_array($name, ['admin', 'user'], true)) {
            return null;
        }

        return Role::query()->firstOrCreate(
            ['name' => $name],
            [
                'display_name' => $name === 'admin' ? 'Администратор' : 'Пользователь',
                'description' => $name === 'admin' ? 'Администратор' : 'Обычный пользователь',
            ]
        );
    }
}


