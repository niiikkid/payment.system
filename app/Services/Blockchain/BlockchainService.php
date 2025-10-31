<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Contracts\Blockchain\BlockchainServiceContract;
use App\Enums\Currency;
use App\Enums\Network;
use App\Contracts\Money\MoneyServiceContract;
use Illuminate\Support\Facades\Http;
use App\Services\Money\MoneyAmount;
use App\Exceptions\Blockchain\UnsupportedNetworkException;
use App\Exceptions\Blockchain\UnsupportedCurrencyException;
use App\Exceptions\Blockchain\ApiRequestException;
use App\Exceptions\Blockchain\TokenContractNotFoundException;

class BlockchainService implements BlockchainServiceContract
{
    private const TRON_USDT_CONTRACT = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

    public function getAddressBalance(Network $network, Currency $currency, string $address): MoneyAmount
    {
        $money = app(MoneyServiceContract::class);

        if ($network !== Network::TRON) {
            throw new UnsupportedNetworkException('Only TRON network is supported for this operation.');
        }
        if ($currency === Currency::USDT) {
            $minor = $this->getTrc20BalanceMinorByContract($this->getUsdtContract(), $address);
            return $money->fromMinor($minor, Currency::USDT);
        }
        if ($currency === Currency::TRX) {
            $minor = $this->getTrxBalanceMinor($address);
            return $money->fromMinor($minor, Currency::TRX);
        }

        throw new UnsupportedCurrencyException('Currency is not supported.');
    }

    public function getIncomingTransactions(Network $network, Currency $currency, string $address): array
    {
        if ($network !== Network::TRON) {
            throw new UnsupportedNetworkException('Only TRON network is supported for this operation.');
        }
        if ($currency !== Currency::USDT) {
            throw new UnsupportedCurrencyException('Only USDT (TRC20) is supported for this operation.');
        }

        $url = rtrim($this->getBaseUrl(), '/') . "/v1/accounts/{$address}/transactions/trc20";

        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($url, [
                'contract_address' => $this->getUsdtContract(),
                'only_confirmed' => true,
                'limit' => 200,
            ]);

        if (!$response->successful()) {
            throw ApiRequestException::forHttpStatus($response->status(), $url, $response->body());
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];
        if (!is_array($data)) {
            throw new ApiRequestException('Malformed TronGrid response: data is not an array.');
        }

        $result = [];
        $money = app(MoneyServiceContract::class);
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }

            $to = (string) ($item['to'] ?? '');
            if ($to === '' || strcasecmp($to, $address) !== 0) {
                continue; // оставляем только входящие
            }

            $rawAmountMinor = (string) ($item['value'] ?? '0');
            $amountHuman = $money->format($money->fromMinor($rawAmountMinor, Currency::USDT));

            $result[] = [
                'txid' => (string) ($item['transaction_id'] ?? ($item['transactionId'] ?? '')),
                'from' => (string) ($item['from'] ?? ''),
                'to' => $to,
                'amount' => $amountHuman,
                'timestamp' => (int) ($item['block_timestamp'] ?? 0),
            ];
        }

        return $result;
    }

    private function getTrc20BalanceMinorByContract(string $contract, string $address): string
    {
        $url = rtrim($this->getBaseUrl(), '/') . "/v1/accounts/{$address}/tokens";
        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($url, [
                'contract_address' => $contract,
                'only_confirmed' => true,
            ]);
        if (!$response->successful()) {
            throw ApiRequestException::forHttpStatus($response->status(), $url, $response->body());
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];
        if (!is_array($data)) {
            throw new ApiRequestException('Malformed TronGrid response: data is not an array.');
        }

        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }
            $tokenId = ($item['token_id'] ?? ($item['tokenId'] ?? ($item['token_address'] ?? null)));
            if ($tokenId && strcasecmp((string) $tokenId, $contract) === 0) {
                $raw = $item['balance'] ?? '0';
                $rawStr = is_string($raw) ? $raw : (string) $raw;
                return $rawStr; // minor units
            }
        }

        throw TokenContractNotFoundException::forAddress($address, $contract);
    }

    private function getTrxBalanceMinor(string $address): string
    {
        // Tron wallet RPC: returns balance in SUN (1 TRX = 1_000_000 SUN)
        $url = rtrim($this->getBaseUrl(), '/') . '/wallet/getaccount';
        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->asJson()
            ->post($url, [
                'address' => $address,
                'visible' => true,
            ]);

        if (!$response->successful()) {
            throw ApiRequestException::forHttpStatus($response->status(), $url, $response->body());
        }

        $payload = $response->json();
        if (!is_array($payload)) {
            throw new ApiRequestException('Malformed Tron wallet response: not an object');
        }
        $balance = $payload['balance'] ?? 0; // in SUN
        return is_string($balance) ? $balance : (string) $balance;
    }

    private function buildHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];
        $apiKey = config('services.trongrid.api_key');
        if (!empty($apiKey)) {
            $headers['TRON-PRO-API-KEY'] = (string) $apiKey;
        }
        return $headers;
    }

    private function getBaseUrl(): string
    {
        $base = (string) (config('services.trongrid.base_url') ?? 'https://api.trongrid.io');
        $base = rtrim($base, "/\t\n\r\0\x0B");
        // Удаляем завершающий /v1 или /v1/
        if ($base !== '') {
            $trimmed = rtrim($base, '/');
            if (substr($trimmed, -3) === '/v1') {
                $base = rtrim(substr($trimmed, 0, -3), '/');
            }
        }
        return $base;
    }

    private function getUsdtContract(): string
    {
        $fromConfig = (string) (config('services.trongrid.usdt_contract') ?? '');
        return $fromConfig !== '' ? $fromConfig : self::TRON_USDT_CONTRACT;
    }
}



