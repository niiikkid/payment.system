<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressStatusRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\Address\AddressService;
use App\Enums\Currency;
use App\Enums\Network;
use App\Exceptions\Address\AddressNotFoundOnBlockchainException;
use App\Exceptions\Address\AddressServiceException;
use App\Exceptions\Address\CurrencyNetworkMismatchException;
use App\Exceptions\Address\DuplicateAddressException;
use App\Exceptions\Address\UnsupportedCurrencyException;
use App\Exceptions\Address\UnsupportedNetworkException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

class AddressController extends Controller
{
    /**
     * Display a listing of addresses.
     */
    public function index(): Response
    {
        $paginator = Address::query()
            ->latest('id')
            ->paginate(20)
            ->through(fn ($address) => (new AddressResource($address))->resolve());

        $currencyOptions = array_map(static function (Currency $c) {
            $value = $c->value;
            return [
                'value' => $value,
                'label' => strtoupper($value),
            ];
        }, Currency::cases());

        $networkOptions = array_map(static function (Network $n) {
            $value = $n->value;
            return [
                'value' => $value,
                'label' => strtoupper($value),
            ];
        }, Network::cases());

        return Inertia::render('addresses/Index', [
            'addresses' => $paginator,
            'currencyOptions' => $currencyOptions,
            'networkOptions' => $networkOptions,
        ]);
    }

    /**
     * Store a newly created address.
     */
    public function store(StoreAddressRequest $request, AddressService $addressService)
    {
        try {
            $address = $addressService->create(
                $request->string('currency')->toString(),
                $request->string('network')->toString(),
                $request->string('address')->toString(),
            );

            return $this->storeSuccessResponse($request, $address);
        } catch (AddressServiceException $exception) {
            $message = $this->mapServiceException($exception);

            return $this->storeErrorResponse($request, $message, HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $exception) {
            report($exception);

            return $this->storeErrorResponse($request, 'Не удалось добавить адрес.', HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function storeSuccessResponse(StoreAddressRequest $request, Address $address): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Address added',
                'address' => (new AddressResource($address))->resolve(),
            ], HttpResponse::HTTP_CREATED);
        }

        return back()->with('success', 'Address added');
    }

    private function storeErrorResponse(StoreAddressRequest $request, string $message, int $status): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], $status);
        }

        return back()
            ->with('error', $message)
            ->onlyInput('currency', 'network', 'address');
    }

    private function mapServiceException(AddressServiceException $exception): string
    {
        return match (true) {
            $exception instanceof AddressNotFoundOnBlockchainException => 'Указанный адрес не найден в блокчейне.',
            $exception instanceof DuplicateAddressException => 'Адрес уже существует для выбранной сети и валюты.',
            $exception instanceof UnsupportedCurrencyException => 'Неподдерживаемая валюта.',
            $exception instanceof UnsupportedNetworkException => 'Неподдерживаемая сеть.',
            $exception instanceof CurrencyNetworkMismatchException => 'Эта валюта недоступна в выбранной сети.',
            default => $exception->getMessage() ?: 'Не удалось добавить адрес.',
        };
    }

    /**
     * Update the specified address (enable/disable).
     */
    public function update(UpdateAddressStatusRequest $request, Address $address, AddressService $addressService)
    {
        $isActive = (bool) $request->boolean('is_active');

        if ($isActive) {
            $addressService->enable($address);
        } else {
            $addressService->disable($address);
        }

        return back()->with('success', 'Address updated');
    }
}


