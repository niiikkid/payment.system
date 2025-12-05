<?php

declare(strict_types=1);

namespace App\Services\IpGeolocation;

use App\Contracts\IpGeolocation\IpGeolocationServiceContract;
use App\Services\IpGeolocation\Exceptions\IpGeolocationException;
use App\Services\IpGeolocation\Exceptions\IpGeolocationQuotaExceededException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

final class IpGeolocationService implements IpGeolocationServiceContract
{
    private const DEFAULT_BASE_URL = 'https://api.ipgeolocation.io/v2';

    public function __construct(
        private readonly ?string $apiKey = null,
        private readonly ?string $baseUrl = null,
    ) {
    }

    public function lookup(string $ip): IpGeolocationResult
    {
        $apiKey = $this->apiKey ?? config('services.ipgeolocation.api_key');
        $baseUrl = rtrim($this->baseUrl ?? (string) config('services.ipgeolocation.base_url', self::DEFAULT_BASE_URL), '/');

        if (empty($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return new IpGeolocationResult(IpGeolocationResult::STATUS_SKIPPED, null);
        }

        if (empty($apiKey)) {
            return new IpGeolocationResult(IpGeolocationResult::STATUS_SKIPPED, $ip);
        }

        $url = $baseUrl . '/ipgeo';

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->get($url, [
                    'apiKey' => $apiKey,
                    'ip' => $ip,
                ]);

            if ($response->status() === 402 || $response->status() === 429) {
                throw new IpGeolocationQuotaExceededException('Quota exceeded.');
            }

            if ($response->failed()) {
                throw new IpGeolocationException('Request failed with status ' . $response->status());
            }

            /** @var array<string,mixed> $data */
            $data = $response->json() ?? [];

            return new IpGeolocationResult(
                IpGeolocationResult::STATUS_OK,
                $data['ip'] ?? $ip,
                $this->mapPayload($data),
            );
        } catch (IpGeolocationQuotaExceededException $e) {
            return new IpGeolocationResult(IpGeolocationResult::STATUS_LIMITED, $ip);
        } catch (Throwable) {
            return new IpGeolocationResult(IpGeolocationResult::STATUS_FAILED, $ip);
        }
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>|null
     */
    private function mapPayload(array $data): ?array
    {
        if (! isset($data['location']) || ! is_array($data['location'])) {
            return null;
        }

        $location = $data['location'];
        $countryName = $location['country_name'] ?? null;
        $city = $location['city'] ?? null;
        $state = $location['state_prov'] ?? null;
        $flag = $location['country_flag'] ?? null;
        $emoji = $location['country_emoji'] ?? null;
        $latitude = $location['latitude'] ?? null;
        $longitude = $location['longitude'] ?? null;
        $countryCode = $location['country_code2'] ?? null;

        if ($countryName === null && $city === null && $state === null) {
            return null;
        }

        $currency = $data['currency'] ?? null;

        return [
            'country' => [
                'name' => $countryName,
                'code' => $countryCode,
                'flag' => $flag,
                'emoji' => $emoji,
            ],
            'location' => [
                'city' => $city,
                'state' => $state,
                'latitude' => $this->toFloatOrNull($latitude),
                'longitude' => $this->toFloatOrNull($longitude),
            ],
            'currency' => [
                'code' => $currency['code'] ?? null,
                'name' => $currency['name'] ?? null,
                'symbol' => $currency['symbol'] ?? null,
            ],
            'raw' => [
                'continent' => $location['continent_name'] ?? null,
                'is_eu' => $location['is_eu'] ?? null,
                'geoname_id' => $location['geoname_id'] ?? null,
            ],
        ];
    }

    private function toFloatOrNull(null|string|int|float $value): ?float
    {
        if ($value === null) {
            return null;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $normalized = Str::replace(',', '.', (string) $value);
        return is_numeric($normalized) ? (float) $normalized : null;
    }
}

