<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'İcazə yoxdur.',
    ],
    'impersonation' => [
        'forbidden' => 'Başqa istifadəçini təqlid etməyə icazəniz yoxdur.',
        'self' => 'Öz hesabınızı təqlid edə bilməzsiniz.',
        'admin_forbidden' => 'Administratoru təqlid edə bilməzsiniz.',
        'logged_in_as' => 'Siz artıq :email kimi daxil oldunuz',
        'session_inactive' => 'Təqlid rejimi aktiv deyil.',
        'session_missing' => 'Əsas sessiya tapılmadı, lütfən yenidən daxil olun.',
        'returned_to_admin' => 'Administrator hesabına qayıtdınız.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrator',
            'admin_description' => 'Administrator',
            'user' => 'İstifadəçi',
            'user_description' => 'Adi istifadəçi',
        ],
        'created' => 'İstifadəçi yaradıldı',
        'updated' => 'İstifadəçi yeniləndi',
        'cannot_change_own_role' => 'Öz rolunuzu dəyişə bilməzsiniz.',
    ],
    'invoices' => [
        'created' => 'Hesab yaradıldı',
        'updated' => 'Hesab yeniləndi',
        'create_failed' => 'Hesabı yaratmaq mümkün olmadı.',
        'update_failed' => 'Hesabı yeniləmək mümkün olmadı.',
        'already_finalized' => 'Hesab artıq yekunlaşdırılıb.',
        'already_expired' => 'Hesabın müddəti bitib.',
        'amount_below_min' => 'Məbləğ icazə verilən minimumdan aşağıdır.',
        'amount_above_max' => 'Məbləğ icazə verilən maksimumdan çoxdur.',
    ],
    'addresses' => [
        'added' => 'Ünvan əlavə edildi',
        'updated' => 'Ünvan yeniləndi',
        'add_failed' => 'Ünvanı əlavə etmək mümkün olmadı.',
        'errors' => [
            'not_found_blockchain' => 'Göstərilən ünvan blokçeyndə tapılmadı.',
            'duplicate' => 'Bu ünvan seçilmiş şəbəkə və valyuta üçün artıq mövcuddur.',
            'unsupported_currency' => 'Dəstəklənməyən valyuta.',
            'unsupported_currency_value' => 'Dəstəklənməyən valyuta: :currency',
            'unsupported_network' => 'Dəstəklənməyən şəbəkə.',
            'unsupported_network_value' => 'Dəstəklənməyən şəbəkə: :network',
            'currency_mismatch' => 'Bu valyuta seçilmiş şəbəkədə mövcud deyil.',
            'currency_mismatch_value' => ':currency valyutası :network şəbəkəsində mövcud deyil.',
            'not_owner' => 'Ünvan cari istifadəçiyə məxsus deyil.',
            'not_exist_blockchain' => 'Ünvan göstərilən valyuta/şəbəkə üçün blokçeyndə mövcud deyil.',
            'no_available' => 'Hazırda tələb olunan məbləğ üçün əlçatan ünvan yoxdur.',
            'invalid_balance' => 'Balans mənfi olmayan onluq sətir olmalıdır.',
        ],
    ],
    'merchants' => [
        'created' => 'Tacir yaradıldı',
        'updated' => 'Tacir yeniləndi',
        'create_failed' => 'Taciri yaratmaq mümkün olmadı.',
        'update_failed' => 'Taciri yeniləmək mümkün olmadı.',
        'errors' => [
            'logo_invalid' => 'Loqo faylını oxumaq mümkün deyil.',
            'logo_not_square' => 'Loqo kvadrat olmalıdır.',
            'logo_too_large' => 'Loqonun ölçüsü 500x500 pikseldən böyük olmamalıdır.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Maksimum məbləğ :amount USDT-dən çox ola bilməz.',
    ],
    'blockchain' => [
        'only_tron' => 'Bu əməliyyat üçün yalnız TRON şəbəkəsi dəstəklənir.',
        'only_usdt' => 'Bu əməliyyat üçün yalnız USDT (TRC20) dəstəklənir.',
        'malformed_trongrid_data' => 'TronGrid cavabı səhvdir: data massiv deyil.',
        'malformed_trongrid_events' => 'TronGrid events cavabı səhvdir: data massiv deyil.',
        'trc20_transfer_missing' => 'Verilmiş txid üçün TRC20 Transfer hadisəsi tapılmadı.',
        'malformed_wallet_nowblock' => 'Tron wallet nowblock cavabı səhvdir: obyekt deyil.',
        'malformed_trongrid_account' => 'TronGrid account cavabı səhvdir: obyekt deyil.',
        'account_not_found' => 'Hesab məlumatı tapılmadı.',
        'malformed_trongrid_trc20' => 'TronGrid account cavabı səhvdir: trc20 massiv deyil.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Limit bitib.',
        'request_failed' => 'Sorğu :status statusu ilə uğursuz oldu.',
    ],
    'api' => [
        'callback_failed' => 'Callback sorğusu :status statusu ilə uğursuz oldu.',
    ],
    'money' => [
        'empty_string' => 'Məbləğ sətiri boşdur.',
        'currency_mismatch' => 'Valyutalar uyğun olmalıdır.',
    ],
];


