<?php

declare(strict_types=1);

namespace App\Services\Market\Parsers;

use App\Contracts\Market\MarketParserContract;
use App\Enums\MarketEnum;
use App\Models\MarketFiat;
use App\Services\Market\Data\MarketPrices;
use App\Services\Market\Exceptions\RapiraParserException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class RapiraParser implements MarketParserContract
{
    private const ENDPOINT = 'https://api.rapira.net/market/exchange-plate-mini?symbol=USDT/RUB';
    private const AVERAGE_DEPTH = 5;

    public function market(): MarketEnum
    {
        return MarketEnum::RAPIRA;
    }

    public function getPrices(MarketFiat $fiat): ?MarketPrices
    {
        try {
            $response = Http::retry(2, 200)
                ->timeout(5)
                ->get(self::ENDPOINT);
        } catch (Throwable $exception) {
            Log::warning('Rapira request failed', [
                'fiat' => $fiat->code,
                'message' => $exception->getMessage(),
            ]);
            return null;
        }

        if ($response->failed()) {
            Log::warning('Rapira request returned non-success status', [
                'fiat' => $fiat->code,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        try {
            $asks = $response->json('ask.items');
            $bids = $response->json('bid.items');

            if (! is_array($asks) || ! is_array($bids) || empty($asks) || empty($bids)) {
                throw new RapiraParserException('Пустой или некорректный стакан Rapira.');
            }

            $averageAskPrice = $this->averageTopPrices($asks);
            $averageBidPrice = $this->averageTopPrices($bids);
        } catch (RapiraParserException $exception) {
            Log::warning('Rapira parsing failed', [
                'fiat' => $fiat->code,
                'message' => $exception->getMessage(),
            ]);
            return null;
        }

        if ($averageAskPrice === null && $averageBidPrice === null) {
            return null;
        }

        return new MarketPrices(
            $averageBidPrice,
            $averageAskPrice,
        );
    }

    private function averageTopPrices(array $items): ?float
    {
        $topItems = array_slice($items, 0, self::AVERAGE_DEPTH);
        $prices = [];

        foreach ($topItems as $item) {
            $rawPrice = $item['price'] ?? null;
            if ($rawPrice === null) {
                continue;
            }

            $price = (float) $rawPrice;
            if ($price > 0) {
                $prices[] = $price;
            }
        }

        if (empty($prices)) {
            return null;
        }

        return array_sum($prices) / count($prices);
    }
}


