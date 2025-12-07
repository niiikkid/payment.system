<?php

declare(strict_types=1);

namespace App\Services\Market\Parsers;

use App\Contracts\Market\MarketParserContract;
use App\Enums\MarketEnum;
use App\Models\MarketFiat;
use App\Services\Market\Data\MarketPrices;
use App\Services\Market\Exceptions\BybitParserException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class BybitParser implements MarketParserContract
{
    private const ENDPOINT = 'https://api2.bybit.com/fiat/otc/item/online';

    public function market(): MarketEnum
    {
        return MarketEnum::BYBIT;
    }

    public function getPrices(MarketFiat $fiat): ?MarketPrices
    {
        $buy = $this->fetchPrice($fiat, true);
        $sell = $this->fetchPrice($fiat, false);

        if ($buy === null && $sell === null) {
            return null;
        }

        return new MarketPrices($buy, $sell);
    }

    private function fetchPrice(MarketFiat $fiat, bool $buy): ?float
    {
        $rows = max(1, (int) $fiat->rows);

        $payload = [
            'userId' => '',
            'tokenId' => 'USDT',
            'currencyId' => strtoupper($fiat->code),
            'payment' => $fiat->bybit_payment_method ? [(string) $fiat->bybit_payment_method] : [],
            'side' => $buy ? '1' : '0',
            'size' => (string) $rows,
            'page' => '1',
            'amount' => $fiat->bybit_amount !== null ? (string) $fiat->bybit_amount : '',
            'authMaker' => false,
            'canTrade' => false,
        ];

        try {
            $response = Http::asJson()
                ->withHeaders([
                    'Accept' => '*/*',
                    'Accept-Encoding' => 'gzip, deflate, br',
                ])
                ->post(self::ENDPOINT, $payload);
        } catch (Throwable $exception) {
            Log::warning('Bybit request failed', [
                'fiat' => $fiat->code,
                'side' => $buy ? 'BUY' : 'SELL',
                'message' => $exception->getMessage(),
            ]);
            return null;
        }

        if ($response->failed()) {
            Log::warning('Bybit request returned non-success status', [
                'fiat' => $fiat->code,
                'side' => $buy ? 'BUY' : 'SELL',
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $items = $response->json('result.items');
        if (! is_array($items) || empty($items)) {
            Log::warning('Bybit response is empty', [
                'fiat' => $fiat->code,
                'side' => $buy ? 'BUY' : 'SELL',
                'payload' => $payload,
            ]);
            return null;
        }

        $prices = [];
        foreach ($items as $item) {
            $price = $item['price'] ?? null;
            if ($price === null) {
                continue;
            }

            $floatPrice = (float) $price;
            if ($floatPrice > 0) {
                $prices[] = $floatPrice;
            }

            if (count($prices) >= $rows) {
                break;
            }
        }

        if (empty($prices)) {
            return null;
        }

        try {
            $delimiter = min(count($prices), $rows);
            return array_sum($prices) / $delimiter;
        } catch (Throwable $exception) {
            throw new BybitParserException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
}


