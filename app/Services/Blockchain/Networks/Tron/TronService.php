<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Networks\Tron;

use App\Contracts\Blockchain\Networks\BlockchainNetworkServiceContract;
use App\Enums\Currency;
use Illuminate\Support\Facades\Http;
use App\Contracts\Money\MoneyServiceContract;
use App\Services\Money\MoneyAmount;

class TronService implements BlockchainNetworkServiceContract
{
    private string $baseUrl;
    private ?string $apiKey;
    private MoneyServiceContract $money;

    private const DEFAULT_USDT_CONTRACT = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

    public function __construct(MoneyServiceContract $money)
    {
        $this->baseUrl = (string) (config('services.trongrid.base_url') ?? 'https://api.trongrid.io');
        $this->apiKey = config('services.trongrid.api_key');
        $this->money = $money;
    }

    public function getNetworkKey(): string
    {
        return 'tron';
    }

    public function getAddressBalance(Currency $currency, string $address): MoneyAmount
    {
        return match ($currency) {
            Currency::USDT => $this->money->create($this->getTrc20BalanceByContract(self::DEFAULT_USDT_CONTRACT, $address), Currency::USDT),
            Currency::TRX => $this->money->create($this->getTrxBalance($address), Currency::TRX),
        };
    }

    private function getTrxBalance(string $address): string
    {
        $url = rtrim($this->baseUrl, '/') . "/v1/accounts/{$address}";

        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($url, [
                'only_confirmed' => true,
            ]);

        if (!$response->successful()) {
            return '0.000000';
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];
        if (!is_array($data) || empty($data) || !is_array($data[0] ?? null)) {
            return '0.000000';
        }
        $account = $data[0];
        $raw = $account['balance'] ?? 0;
        $rawStr = is_string($raw) ? $raw : (string) $raw;
        return $this->fromTrc20Decimals($rawStr, 6);
    }

    private function getTrc20BalanceByContract(string $contract, string $address): string
    {
        $url = rtrim($this->baseUrl, '/') . "/v1/accounts/{$address}/tokens";

        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($url, [
                'contract_address' => $contract,
                'only_confirmed' => true,
            ]);

        if (!$response->successful()) {
            return '0.000000';
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];
        if (!is_array($data)) {
            return '0.000000';
        }

        // Ищем первый токен по контракту и читаем баланс
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }
            // Возможные ключи: token_id, tokenId, token_address
            $tokenId = ($item['token_id'] ?? ($item['tokenId'] ?? ($item['token_address'] ?? null)));
            if ($tokenId && strcasecmp((string) $tokenId, $contract) === 0) {
                $raw = $item['balance'] ?? '0';
                $rawStr = is_string($raw) ? $raw : (string) $raw;
                return $this->fromTrc20Decimals($rawStr, 6);
            }
        }

        return '0.000000';
    }

    private function buildHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];
        if (!empty($this->apiKey)) {
            $headers['TRON-PRO-API-KEY'] = (string) $this->apiKey;
        }
        return $headers;
    }

    private function fromTrc20Decimals(string $raw, int $decimals): string
    {
        $raw = ltrim($raw, '+');
        if ($raw === '') {
            return '0.000000';
        }

        $scale = max(6, $decimals);
        $divisor = '1' . str_repeat('0', $decimals);
        return bcdiv($raw, $divisor, $scale);
    }
}


