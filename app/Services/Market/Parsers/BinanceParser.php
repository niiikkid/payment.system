<?php

declare(strict_types=1);

namespace App\Services\Market\Parsers;

use App\Contracts\Market\MarketParserContract;
use App\Enums\MarketEnum;
use App\Models\MarketFiat;
use App\Services\Market\Data\MarketPrices;
use App\Services\Market\Exceptions\BinanceParserException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class BinanceParser implements MarketParserContract
{
    private const ENDPOINT = 'https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search';

    public function market(): MarketEnum
    {
        return MarketEnum::BINANCE;
    }

    public function getPrices(MarketFiat $fiat): ?MarketPrices
    {
        $buy = $this->fetchPrice($fiat, 'BUY');
        $sell = $this->fetchPrice($fiat, 'SELL');

        if ($buy === null && $sell === null) {
            return null;
        }

        return new MarketPrices($buy, $sell);
    }

    private function fetchPrice(MarketFiat $fiat, string $tradeType): ?float
    {
        $rows = max(1, (int) $fiat->rows);
        $payload = [
            'page' => 1,
            'rows' => $rows,
            'payTypes' => array_values($fiat->pay_types ?? []),
            'asset' => 'USDT',
            'tradeType' => $tradeType,
            'fiat' => strtoupper($fiat->code),
            'publisherType' => null,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
            ])->post(self::ENDPOINT, $payload);
        } catch (Throwable $exception) {
            Log::warning('Binance P2P request failed', [
                'fiat' => $fiat->code,
                'tradeType' => $tradeType,
                'message' => $exception->getMessage(),
            ]);
            return null;
        }

        if (! $response->successful()) {
            Log::warning('Binance P2P returned unsuccessful status', [
                'fiat' => $fiat->code,
                'tradeType' => $tradeType,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json('data');
        if (! is_array($data) || empty($data)) {
            return null;
        }

        $prices = [];
        foreach ($data as $item) {
            $price = $item['adv']['price'] ?? null;
            if ($price === null) {
                continue;
            }
            $priceFloat = (float) $price;
            if ($priceFloat > 0) {
                $prices[] = $priceFloat;
            }
            if (count($prices) >= $rows) {
                break;
            }
        }

        if (empty($prices)) {
            return null;
        }

        try {
            return array_sum($prices) / count($prices);
        } catch (Throwable $exception) {
            throw new BinanceParserException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
}


