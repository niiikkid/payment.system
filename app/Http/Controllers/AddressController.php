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
use App\Exceptions\Address\DuplicateAddressException;
use App\Exceptions\Address\UnsupportedCurrencyException;
use App\Exceptions\Address\UnsupportedNetworkException;
use App\Exceptions\Address\CurrencyNetworkMismatchException;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

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
            $addressService->create(
                $request->string('currency')->toString(),
                $request->string('network')->toString(),
                $request->string('address')->toString(),
            );

            return back()->with('success', 'Address added');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Не удалось добавить адрес.')
                ->onlyInput('currency', 'network', 'address');
        }
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


