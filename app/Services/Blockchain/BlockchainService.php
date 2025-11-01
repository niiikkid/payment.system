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

    public function getTransactionInfoByHash(Network $network, Currency $currency, string $txid): array
    {
        if ($network !== Network::TRON) {
            throw new UnsupportedNetworkException('Only TRON network is supported for this operation.');
        }

        if ($currency === Currency::USDT) {
            return $this->getTrc20TransactionByHash($txid);
        }

        if ($currency === Currency::TRX) {
            return $this->getTrxTransactionByHash($txid);
        }

        throw new UnsupportedCurrencyException('Currency is not supported.');
    }

    public function getAddressBalance(Network $network, Currency $currency, string $address): MoneyAmount
    {
        $money = app(MoneyServiceContract::class);

        if ($network !== Network::TRON) {
            throw new UnsupportedNetworkException('Only TRON network is supported for this operation.');
        }
        if ($currency === Currency::USDT) {
            $minor = $this->getTrc20BalanceMinorByContract($address);
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
                'contract_address' => self::TRON_USDT_CONTRACT,
                'only_confirmed' => true,
                'limit' => 30,
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

    private function getTrc20TransactionByHash(string $txid): array
    {
        $base = rtrim($this->getBaseUrl(), '/');
        $eventsUrl = $base . "/v1/transactions/{$txid}/events";
        $txUrl = $base . "/v1/transactions/{$txid}";

        $eventsResponse = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($eventsUrl, [
                'only_confirmed' => true,
                'contract_address' => self::TRON_USDT_CONTRACT,
                'limit' => 50,
            ]);

        if (!$eventsResponse->successful()) {
            throw ApiRequestException::forHttpStatus($eventsResponse->status(), $eventsUrl, $eventsResponse->body());
        }

        $eventsPayload = $eventsResponse->json();
        $events = is_array($eventsPayload) ? ($eventsPayload['data'] ?? []) : [];
        if (!is_array($events)) {
            throw new ApiRequestException('Malformed TronGrid events response: data is not an array.');
        }

        $transferEvent = null;
        foreach ($events as $event) {
            if (!is_array($event)) {
                continue;
            }
            $name = (string) ($event['event_name'] ?? '');
            $contractAddr = (string) ($event['contract_address'] ?? '');
            if (strcasecmp($name, 'Transfer') === 0 && strcasecmp($contractAddr, self::TRON_USDT_CONTRACT) === 0) {
                $transferEvent = $event;
                break;
            }
        }

        if ($transferEvent === null) {
            throw new ApiRequestException('TRC20 Transfer event not found for given txid.');
        }

        // Получаем номер блока и текущий блок (с fallback, без исключения на 404)
        $blockNumber = $this->getTransactionBlockNumber($txid);
        $currentBlock = $this->getCurrentBlockNumber();
        $confirmations = ($blockNumber > 0 && $currentBlock > 0)
            ? max(0, $currentBlock - $blockNumber)
            : 0;

        $money = app(MoneyServiceContract::class);
        $amountMinor = (string) ($transferEvent['result']['value'] ?? ($transferEvent['result']['amount'] ?? '0'));
        $from = (string) ($transferEvent['result']['from'] ?? ($transferEvent['transferFromAddress'] ?? ''));
        $to = (string) ($transferEvent['result']['to'] ?? ($transferEvent['transferToAddress'] ?? ''));
        $timestamp = (int) ($transferEvent['block_timestamp'] ?? 0);

        return [
            'txid' => $txid,
            'from' => $from,
            'to' => $to,
            'amount' => $money->format($money->fromMinor($amountMinor, Currency::USDT)),
            'timestamp' => $timestamp,
            'block' => $blockNumber,
            'current_block' => $currentBlock,
            'confirmations' => $confirmations,
        ];
    }

    private function getTrxTransactionByHash(string $txid): array
    {
        $base = rtrim($this->getBaseUrl(), '/');
        $txUrl = $base . "/v1/transactions/{$txid}";

        $txResponse = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($txUrl);

        $from = '';
        $to = '';
        $amountMinor = '0';
        $timestamp = 0;
        $blockNumber = 0;

        if ($txResponse->successful()) {
            $payload = $txResponse->json();
            if (!is_array($payload)) {
                throw new ApiRequestException('Malformed TronGrid transaction response: not an object');
            }
            $txData = $payload['data'][0] ?? null;
            if (is_array($txData)) {
                $timestamp = (int) ($txData['block_timestamp'] ?? 0);
                $blockNumber = (int) ($txData['block'] ?? ($txData['blockNumber'] ?? ($txData['block_number'] ?? 0)));
                $contractList = $txData['raw_data']['contract'] ?? [];
                $firstContract = is_array($contractList) ? ($contractList[0] ?? []) : [];
                $type = (string) ($firstContract['type'] ?? '');
                $param = is_array($firstContract) ? ($firstContract['parameter']['value'] ?? []) : [];
                if (strcasecmp($type, 'TransferContract') === 0 && is_array($param)) {
                    $from = (string) ($param['owner_address'] ?? '');
                    $to = (string) ($param['to_address'] ?? '');
                    $amountMinor = (string) ($param['amount'] ?? '0'); // in SUN
                }
            }
        } elseif ($txResponse->status() !== 404) {
            throw ApiRequestException::forHttpStatus($txResponse->status(), $txUrl, $txResponse->body());
        }

        // Fallback: если не удалось получить номер блока из TronGrid
        if ($blockNumber <= 0) {
            $blockNumber = $this->getTransactionBlockNumber($txid);
        }

        $currentBlock = $this->getCurrentBlockNumber();

        $confirmations = $blockNumber > 0 && $currentBlock > 0 ? max(0, $currentBlock - $blockNumber) : 0;

        $money = app(MoneyServiceContract::class);

        return [
            'txid' => $txid,
            'from' => $from,
            'to' => $to,
            'amount' => $money->format($money->fromMinor($amountMinor, Currency::TRX)),
            'timestamp' => $timestamp,
            'block' => $blockNumber,
            'current_block' => $currentBlock,
            'confirmations' => $confirmations,
        ];
    }

    private function getTransactionBlockNumber(string $txid): int
    {
        $base = rtrim($this->getBaseUrl(), '/');
        $txUrl = $base . "/v1/transactions/{$txid}";
        $resp = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->get($txUrl);
        if ($resp->successful()) {
            $payload = $resp->json();
            if (is_array($payload)) {
                $data = $payload['data'][0] ?? null;
                if (is_array($data)) {
                    return (int) ($data['block'] ?? ($data['blockNumber'] ?? ($data['block_number'] ?? 0)));
                }
            }
        } elseif ($resp->status() !== 404) {
            throw ApiRequestException::forHttpStatus($resp->status(), $txUrl, $resp->body());
        }

        // Fallback: Tron wallet RPC
        $walletUrl = $base . '/wallet/gettransactioninfobyid';
        $walletResp = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->asJson()
            ->post($walletUrl, ['value' => $txid]);
        if ($walletResp->successful()) {
            $payload = $walletResp->json();
            if (is_array($payload)) {
                return (int) ($payload['blockNumber'] ?? 0);
            }
        }

        return 0;
    }

    private function getCurrentBlockNumber(): int
    {
        $base = rtrim($this->getBaseUrl(), '/');
        $nowBlockUrl = $base . '/wallet/getnowblock';
        $nowResponse = Http::withHeaders($this->buildHeaders())
            ->acceptJson()
            ->asJson()
            ->post($nowBlockUrl, []);
        if (!$nowResponse->successful()) {
            throw ApiRequestException::forHttpStatus($nowResponse->status(), $nowBlockUrl, $nowResponse->body());
        }
        $nowPayload = $nowResponse->json();
        if (!is_array($nowPayload)) {
            throw new ApiRequestException('Malformed Tron wallet nowblock response: not an object');
        }
        return (int) ($nowPayload['block_header']['raw_data']['number'] ?? 0);
    }

    private function getTrc20BalanceMinorByContract(string $address): string
    {
        $url = 'https://apilist.tronscan.org/api/account';
        $response = Http::acceptJson()->get($url, [
            'address' => $address,
            'includeToken' => true,
        ]);
        if (!$response->successful()) {
            throw ApiRequestException::forHttpStatus($response->status(), $url, $response->body());
        }

        $payload = $response->json();
        if (!is_array($payload)) {
            throw new ApiRequestException('Malformed Tronscan response: not an object');
        }

        $tokens = $payload['trc20token_balances'] ?? [];
        if (!is_array($tokens)) {
            throw new ApiRequestException('Malformed Tronscan response: trc20token_balances is not an array');
        }

        foreach ($tokens as $token) {
            if (!is_array($token)) {
                continue;
            }
            $name = (string) ($token['tokenName'] ?? '');
            $abbr = (string) ($token['tokenAbbr'] ?? '');
            if (strcasecmp($name, 'Tether USD') === 0 || strcasecmp($abbr, 'USDT') === 0) {
                $raw = $token['balance'] ?? '0'; // minor units
                return is_string($raw) ? $raw : (string) $raw;
            }
        }

        throw TokenContractNotFoundException::forAddress($address, self::TRON_USDT_CONTRACT);
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
}



