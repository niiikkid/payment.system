<?php

declare(strict_types=1);

namespace App\Support;

final class LocaleOptions
{
    private const AVAILABLE = [
        ['code' => 'ru', 'label' => 'Русский', 'flag' => 'RU'],
        ['code' => 'az', 'label' => 'Azərbaycan', 'flag' => 'AZ'],
        ['code' => 'kk', 'label' => 'Қазақша', 'flag' => 'KZ'],
        ['code' => 'ky', 'label' => 'Кыргызча', 'flag' => 'KG'],
        ['code' => 'uz', 'label' => "O'zbekcha", 'flag' => 'UZ'],
        ['code' => 'uk', 'label' => 'Українська', 'flag' => 'UA'],
        ['code' => 'tr', 'label' => 'Türkçe', 'flag' => 'TR'],
        ['code' => 'de', 'label' => 'Deutsch', 'flag' => 'DE'],
        ['code' => 'pl', 'label' => 'Polski', 'flag' => 'PL'],
        ['code' => 'fr', 'label' => 'Français', 'flag' => 'FR'],
        ['code' => 'it', 'label' => 'Italiano', 'flag' => 'IT'],
        ['code' => 'es', 'label' => 'Español', 'flag' => 'ES'],
        ['code' => 'es-MX', 'label' => 'Español (México)', 'flag' => 'MX'],
        ['code' => 'pt', 'label' => 'Português', 'flag' => 'PT'],
        ['code' => 'en', 'label' => 'English', 'flag' => 'US'],
        ['code' => 'ar', 'label' => 'العربية', 'flag' => 'AE'],
        ['code' => 'hi', 'label' => 'हिन्दी', 'flag' => 'IN'],
        ['code' => 'zh', 'label' => '中文', 'flag' => 'CN'],
    ];

    public static function all(): array
    {
        return self::AVAILABLE;
    }

    public static function codes(): array
    {
        return array_column(self::AVAILABLE, 'code');
    }

    public static function find(string $code): ?array
    {
        foreach (self::AVAILABLE as $locale) {
            if ($locale['code'] === $code) {
                return $locale;
            }
        }

        return null;
    }

    public static function normalize(string $code): ?string
    {
        foreach (self::AVAILABLE as $locale) {
            if (strcasecmp($locale['code'], $code) === 0) {
                return $locale['code'];
            }
        }

        return null;
    }
}


