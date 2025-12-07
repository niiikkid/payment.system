<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Yetkisiz.',
    ],
    'impersonation' => [
        'forbidden' => 'Başka bir kullanıcıyı taklit etme izniniz yok.',
        'self' => 'Kendi hesabınızı taklit edemezsiniz.',
        'admin_forbidden' => 'Yönetici hesabını taklit edemezsiniz.',
        'logged_in_as' => 'Şimdi :email olarak oturum açtınız',
        'session_inactive' => 'Taklit modu etkin değil.',
        'session_missing' => 'Orijinal oturum bulunamadı, lütfen tekrar giriş yapın.',
        'returned_to_admin' => 'Yönetici hesabına geri döndünüz.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Yönetici',
            'admin_description' => 'Yönetici',
            'user' => 'Kullanıcı',
            'user_description' => 'Normal kullanıcı',
        ],
        'created' => 'Kullanıcı oluşturuldu',
        'updated' => 'Kullanıcı güncellendi',
        'cannot_change_own_role' => 'Kendi rolünüzü değiştiremezsiniz.',
    ],
    'invoices' => [
        'created' => 'Fatura oluşturuldu',
        'updated' => 'Fatura güncellendi',
        'create_failed' => 'Fatura oluşturulamadı.',
        'update_failed' => 'Fatura güncellenemedi.',
        'already_finalized' => 'Fatura zaten tamamlandı.',
        'already_expired' => 'Fatura zaten süresi dolmuş.',
        'amount_below_min' => 'Tutar izin verilen minimumun altında.',
        'amount_above_max' => 'Tutar izin verilen maksimumu aşıyor.',
    ],
    'addresses' => [
        'added' => 'Adres eklendi',
        'updated' => 'Adres güncellendi',
        'add_failed' => 'Adres eklenemedi.',
        'errors' => [
            'not_found_blockchain' => 'Belirtilen adres blok zincirinde bulunamadı.',
            'duplicate' => 'Adres seçilen ağ ve para birimi için zaten mevcut.',
            'unsupported_currency' => 'Desteklenmeyen para birimi.',
            'unsupported_currency_value' => 'Desteklenmeyen para birimi: :currency',
            'unsupported_network' => 'Desteklenmeyen ağ.',
            'unsupported_network_value' => 'Desteklenmeyen ağ: :network',
            'currency_mismatch' => 'Bu para birimi seçilen ağda mevcut değil.',
            'currency_mismatch_value' => ':network ağında :currency para birimi mevcut değil.',
            'not_owner' => 'Adres mevcut kullanıcıya ait değil.',
            'not_exist_blockchain' => 'Adres belirtilen para birimi/ağ için blok zincirinde yok.',
            'no_available' => 'Şu anda verilen tutar için kullanılabilir adres yok.',
            'invalid_balance' => 'Bakiye negatif olmayan ondalık bir dize olmalıdır.',
        ],
    ],
    'merchants' => [
        'created' => 'Mağaza oluşturuldu',
        'updated' => 'Mağaza güncellendi',
        'create_failed' => 'Mağaza oluşturulamadı.',
        'update_failed' => 'Mağaza güncellenemedi.',
        'errors' => [
            'logo_invalid' => 'Logo dosyası okunamadı.',
            'logo_not_square' => 'Logo kare olmalıdır.',
            'logo_too_large' => 'Logo 500x500 pikseli geçmemelidir.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Maksimum tutar :amount USDT değerini aşamaz.',
    ],
    'blockchain' => [
        'only_tron' => 'Bu işlem için yalnızca TRON ağı desteklenir.',
        'only_usdt' => 'Bu işlem için yalnızca USDT (TRC20) desteklenir.',
        'malformed_trongrid_data' => 'Bozuk TronGrid yanıtı: data bir dizi değil.',
        'malformed_trongrid_events' => 'Bozuk TronGrid events yanıtı: data bir dizi değil.',
        'trc20_transfer_missing' => 'Belirtilen txid için TRC20 Transfer olayı bulunamadı.',
        'malformed_wallet_nowblock' => 'Bozuk Tron wallet nowblock yanıtı: nesne değil.',
        'malformed_trongrid_account' => 'Bozuk TronGrid hesap yanıtı: nesne değil.',
        'account_not_found' => 'Hesap verisi bulunamadı.',
        'malformed_trongrid_trc20' => 'Bozuk TronGrid hesap yanıtı: trc20 bir dizi değil.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Kotayı aştınız.',
        'request_failed' => 'İstek :status durum kodu ile başarısız oldu.',
    ],
    'api' => [
        'callback_failed' => 'Callback isteği :status durum kodu ile başarısız oldu.',
    ],
    'money' => [
        'empty_string' => 'Para değeri boş.',
        'currency_mismatch' => 'Para birimleri eşleşmelidir.',
    ],
];

