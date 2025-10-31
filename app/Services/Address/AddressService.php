<?php

declare(strict_types=1);

namespace App\Services\Address;

use App\Contracts\Address\AddressServiceContract;
use App\Enums\Currency;
use App\Enums\Network;
use App\Enums\NetworkCurrency;
use App\Models\Address;
use App\Contracts\Blockchain\BlockchainServiceContract;
use App\Contracts\Money\MoneyServiceContract;
use App\Exceptions\Address\CurrencyNetworkMismatchException;
use App\Exceptions\Address\DuplicateAddressException;
use App\Exceptions\Address\InvalidBalanceFormatException;
use App\Exceptions\Address\AddressNotFoundOnBlockchainException;
use App\Exceptions\Address\UnsupportedCurrencyException;
use App\Exceptions\Address\UnsupportedNetworkException;
use App\Exceptions\Blockchain\TokenContractNotFoundException;
use Carbon\Carbon;

class AddressService implements AddressServiceContract
{
    public function __construct(
        private readonly BlockchainServiceContract $blockchain,
        private readonly MoneyServiceContract $money,
    ) {
    }

    public function create(string $currency, string $network, string $address): Address
    {
        $currencyEnum = Currency::tryFrom(strtoupper(trim($currency)));
        $networkEnum = Network::tryFrom(strtolower(trim($network)));

        if (!$currencyEnum) {
            throw new UnsupportedCurrencyException('Unsupported currency: ' . $currency);
        }
        if (!$networkEnum) {
            throw new UnsupportedNetworkException('Unsupported network: ' . $network);
        }

        // Проверка совместимости валюты и сети
        $allowedNetworks = NetworkCurrency::networksByCurrency($currencyEnum);
        if (!in_array($networkEnum, $allowedNetworks, true)) {
            throw new CurrencyNetworkMismatchException('Currency ' . $currencyEnum->value . ' is not available on network ' . $networkEnum->value);
        }

        $exists = Address::query()
            ->where('currency', $currencyEnum->value)
            ->where('network', $networkEnum->value)
            ->where('address', $address)
            ->exists();
        if ($exists) {
            throw new DuplicateAddressException('Address already exists for the network & currency');
        }

        // Получаем баланс адреса из блокчейн-сервиса
        try {
            $balance = $this->blockchain->getAddressBalance($networkEnum, $currencyEnum, $address);
        } catch (TokenContractNotFoundException $e) {
            // Отсутствие токена/баланса трактуем как несуществующий адрес для нужной валюты
            throw new AddressNotFoundOnBlockchainException('Address does not exist on blockchain for specified currency/network.');
        }

        $balanceMinor = $this->money->toMinor($balance);

        return Address::query()->create([
            'currency' => $currencyEnum,
            'network' => $networkEnum,
            'address' => $address,
            'is_active' => true,
            'balance' => $balanceMinor,
            'last_checked_at' => Carbon::now(),
        ]);
    }

    public function enable(Address $address): Address
    {
        $address->update(['is_active' => true]);
        return $address->refresh();
    }

    public function disable(Address $address): Address
    {
        $address->update(['is_active' => false]);
        return $address->refresh();
    }

    public function updateChecked(Address $address, string $balance): Address
    {
        if (!$this->isValidDecimalString($balance)) {
            throw new InvalidBalanceFormatException('Balance must be a non-negative decimal string');
        }
        $address->update([
            'balance' => ltrim($balance, '+'),
            'last_checked_at' => Carbon::now(),
        ]);
        return $address->refresh();
    }

    private function isValidDecimalString(string $value): bool
    {
        if ($value === '') {
            return false;
        }
        return (bool) preg_match('/^\d+(?:\.\d+)?$/', $value);
    }
}


