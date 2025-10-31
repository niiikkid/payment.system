<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Networks\Tron;

use App\Contracts\Blockchain\Networks\BlockchainNetworkServiceContract;
use App\Enums\Currency;
use Illuminate\Support\Facades\Http;

class TronService implements BlockchainNetworkServiceContract
{
    private string $baseUrl;
    private ?string $apiKey;

    private const DEFAULT_USDT_CONTRACT = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

    public function __construct()
    {
        $this->baseUrl = (string) (config('services.trongrid.base_url') ?? 'https://api.trongrid.io');
        $this->apiKey = config('services.trongrid.api_key');
    }

    public function getNetworkKey(): string
    {
        return 'tron';
    }

    public function getIncomingTransfers(Currency $currency, string $address, int $limit = 30): array
    {
        return match ($currency) {
            Currency::USDT => $this->getTrc20IncomingByContract(self::DEFAULT_USDT_CONTRACT, $address, $limit),
            Currency::TRX => $this->getTrxIncomingTransfers($address, $limit),
            default => [],
        };
    }

    public function getOutgoingTransfers(Currency $currency, string $address, int $limit = 30): array
    {
        return match ($currency) {
            Currency::USDT => $this->getTrc20OutgoingByContract(self::DEFAULT_USDT_CONTRACT, $address, $limit),
            Currency::TRX => $this->getTrxOutgoingTransfers($address, $limit),
            default => [],
        };
    }

    private function getTrc20IncomingByContract(string $contract, string $address, int $limit = 30): array
    {
        $url = rtrim($this->baseUrl, '/') . "/v1/accounts/{$address}/transactions/trc20";

        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($url, [
                'limit' => $limit,
                'contract_address' => $contract,
            ]);

        if (!$response->successful()) {
            return [];
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];
        if (!is_array($data)) {
            return [];
        }

        $normalized = [];
        foreach ($data as $tx) {
            if (!is_array($tx)) {
                continue;
            }
            $to = $tx['to'] ?? null;
            $value = $tx['value'] ?? null;
            $txid = $tx['transaction_id'] ?? ($tx['txid'] ?? null);
            $timestamp = $tx['block_timestamp'] ?? null;

            if (!$txid || !$to || !$value) {
                continue;
            }

            if (mb_strtolower((string) $to) !== mb_strtolower($address)) {
                continue;
            }

            $amount = $this->fromTrc20Decimals((string) $value, 6);

            $normalized[] = [
                'txid' => (string) $txid,
                'amount' => $amount,
                'timestamp' => is_numeric($timestamp) ? (int) $timestamp : null,
            ];
        }

        return $normalized;
    }

    private function getTrc20OutgoingByContract(string $contract, string $address, int $limit = 30): array
    {
        $url = rtrim($this->baseUrl, '/') . "/v1/accounts/{$address}/transactions/trc20";

        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($url, [
                'limit' => $limit,
                'contract_address' => $contract,
            ]);

        if (!$response->successful()) {
            return [];
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];
        if (!is_array($data)) {
            return [];
        }

        $normalized = [];
        foreach ($data as $tx) {
            if (!is_array($tx)) {
                continue;
            }
            $from = $tx['from'] ?? null;
            $value = $tx['value'] ?? null;
            $txid = $tx['transaction_id'] ?? ($tx['txid'] ?? null);
            $timestamp = $tx['block_timestamp'] ?? null;

            if (!$txid || !$from || !$value) {
                continue;
            }

            if (mb_strtolower((string) $from) !== mb_strtolower($address)) {
                continue;
            }

            $amount = $this->fromTrc20Decimals((string) $value, 6);

            $normalized[] = [
                'txid' => (string) $txid,
                'amount' => $amount,
                'timestamp' => is_numeric($timestamp) ? (int) $timestamp : null,
            ];
        }

        return $normalized;
    }

    private function getTrxIncomingTransfers(string $address, int $limit = 30): array
    {
        $url = rtrim($this->baseUrl, '/') . "/v1/accounts/{$address}/transactions";

        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($url, [
                'limit' => $limit,
                'only_confirmed' => true,
            ]);

        if (!$response->successful()) {
            return [];
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];
        if (!is_array($data)) {
            return [];
        }

        $normalized = [];
        foreach ($data as $tx) {
            if (!is_array($tx)) {
                continue;
            }

            $rawData = $tx['raw_data'] ?? null;
            if (!is_array($rawData)) {
                continue;
            }
            $contracts = $rawData['contract'] ?? null;
            if (!is_array($contracts) || empty($contracts) || !is_array($contracts[0] ?? null)) {
                continue;
            }
            $contract = $contracts[0];
            $parameter = $contract['parameter']['value'] ?? null;
            if (!is_array($parameter)) {
                continue;
            }

            $to = $parameter['to_address'] ?? null;
            $amount = $parameter['amount'] ?? null;
            $txid = $tx['txID'] ?? ($tx['transaction_id'] ?? null);
            $timestamp = $tx['block_timestamp'] ?? null;

            if (!$txid || !$to || !is_numeric($amount)) {
                continue;
            }

            if (mb_strtolower((string) $to) !== mb_strtolower($address)) {
                continue;
            }

            $normalized[] = [
                'txid' => (string) $txid,
                'amount' => $this->fromTrc20Decimals((string) $amount, 6),
                'timestamp' => is_numeric($timestamp) ? (int) $timestamp : null,
            ];
        }

        return $normalized;
    }

    private function getTrxOutgoingTransfers(string $address, int $limit = 30): array
    {
        $url = rtrim($this->baseUrl, '/') . "/v1/accounts/{$address}/transactions";

        $response = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($url, [
                'limit' => $limit,
                'only_confirmed' => true,
            ]);

        if (!$response->successful()) {
            return [];
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];
        if (!is_array($data)) {
            return [];
        }

        $normalized = [];
        foreach ($data as $tx) {
            if (!is_array($tx)) {
                continue;
            }

            $rawData = $tx['raw_data'] ?? null;
            if (!is_array($rawData)) {
                continue;
            }
            $contracts = $rawData['contract'] ?? null;
            if (!is_array($contracts) || empty($contracts) || !is_array($contracts[0] ?? null)) {
                continue;
            }
            $contract = $contracts[0];
            $parameter = $contract['parameter']['value'] ?? null;
            if (!is_array($parameter)) {
                continue;
            }

            $from = $parameter['owner_address'] ?? null;
            $amount = $parameter['amount'] ?? null;
            $txid = $tx['txID'] ?? ($tx['transaction_id'] ?? null);
            $timestamp = $tx['block_timestamp'] ?? null;

            if (!$txid || !$from || !is_numeric($amount)) {
                continue;
            }

            if (mb_strtolower((string) $from) !== mb_strtolower($address)) {
                continue;
            }

            $normalized[] = [
                'txid' => (string) $txid,
                'amount' => $this->fromTrc20Decimals((string) $amount, 6),
                'timestamp' => is_numeric($timestamp) ? (int) $timestamp : null,
            ];
        }

        return $normalized;
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


