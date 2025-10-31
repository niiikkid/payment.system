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
        $addresses = AddressResource::collection(
            Address::query()->latest('id')->get()
        )->resolve();

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
            'addresses' => $addresses,
            'currencyOptions' => $currencyOptions,
            'networkOptions' => $networkOptions,
        ]);
    }

    /**
     * Store a newly created address.
     */
    public function store(StoreAddressRequest $request, AddressService $addressService)
    {
        $addressService->create(
            $request->string('currency')->toString(),
            $request->string('network')->toString(),
            $request->string('address')->toString(),
        );

        return back()->with('success', 'Address added');
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


