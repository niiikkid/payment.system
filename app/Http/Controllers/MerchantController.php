<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Merchant\MerchantServiceContract;
use App\Exceptions\Merchant\MerchantLogoInvalidException;
use App\Exceptions\Merchant\MerchantLogoNotSquareException;
use App\Exceptions\Merchant\MerchantLogoTooLargeException;
use App\Exceptions\Merchant\MerchantServiceException;
use App\Http\Requests\Merchant\MerchantStoreRequest;
use App\Http\Requests\Merchant\MerchantUpdateRequest;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MerchantController extends Controller
{
    public function index(): InertiaResponse
    {
        $userId = Auth::id();

        $merchants = Merchant::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->paginate(20)
            ->through(fn ($merchant) => (new MerchantResource($merchant))->resolve());

        return $this->inertia('merchants/Index', [
            'merchants' => $merchants,
        ]);
    }

    public function store(MerchantStoreRequest $request, MerchantServiceContract $service): JsonResponse|RedirectResponse
    {
        try {
            $merchant = $service->create(
                $request->user(),
                $request->string('name')->toString(),
                $request->filled('description') ? $request->string('description')->toString() : null,
                $request->string('initials')->toString(),
                $request->file('logo')
            );

            return $this->successResponse($request, $merchant, Response::HTTP_CREATED);
        } catch (MerchantServiceException $exception) {
            return $this->errorResponse($request, $this->mapException($exception), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $exception) {
            report($exception);
            return $this->errorResponse($request, __('messages.merchants.create_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(MerchantUpdateRequest $request, Merchant $merchant, MerchantServiceContract $service): RedirectResponse|JsonResponse
    {
        $this->authorizeMerchant($merchant);

        try {
            $service->update(
                $merchant,
                $request->string('name')->toString(),
                $request->filled('description') ? $request->string('description')->toString() : null,
                $request->string('initials')->toString(),
                $request->file('logo')
            );

            return $this->successResponse($request, $merchant->refresh());
        } catch (MerchantServiceException $exception) {
            return $this->errorResponse($request, $this->mapException($exception), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $exception) {
            report($exception);
            return $this->errorResponse($request, __('messages.merchants.update_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function authorizeMerchant(Merchant $merchant): void
    {
        if ($merchant->user_id !== Auth::id()) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }

    private function successResponse(MerchantStoreRequest|MerchantUpdateRequest $request, Merchant $merchant, int $status = Response::HTTP_OK): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'merchant' => (new MerchantResource($merchant))->resolve(),
            ], $status);
        }

        $message = $status === Response::HTTP_CREATED
            ? __('messages.merchants.created')
            : __('messages.merchants.updated');

        return back()->with('success', $message);
    }

    private function errorResponse(MerchantStoreRequest|MerchantUpdateRequest $request, string $message, int $status): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], $status);
        }

        return back()->with('error', $message);
    }

    private function mapException(MerchantServiceException $exception): string
    {
        return match (true) {
            $exception instanceof MerchantLogoInvalidException => __('messages.merchants.errors.logo_invalid'),
            $exception instanceof MerchantLogoNotSquareException => __('messages.merchants.errors.logo_not_square'),
            $exception instanceof MerchantLogoTooLargeException => __('messages.merchants.errors.logo_too_large'),
            default => $exception->getMessage() ?: __('messages.merchants.create_failed'),
        };
    }
}

