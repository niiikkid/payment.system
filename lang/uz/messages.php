<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Avtorizatsiya qilinmagan.',
    ],
    'impersonation' => [
        'forbidden' => 'Boshqa foydalanuvchi nomidan kirishga ruxsat yo\'q.',
        'self' => 'O\'z akkauntingiz nomidan kirib bo\'lmaydi.',
        'admin_forbidden' => 'Administrator nomidan kirish mumkin emas.',
        'logged_in_as' => 'Siz :email sifatida kirdingiz',
        'session_inactive' => 'Boshqa foydalanuvchi rejimi faol emas.',
        'session_missing' => 'Asl sessiya topilmadi, iltimos, qayta kiring.',
        'returned_to_admin' => 'Siz admin akkauntingizga qaytdingiz.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrator',
            'admin_description' => 'Administrator',
            'user' => 'Foydalanuvchi',
            'user_description' => 'Oddiy foydalanuvchi',
        ],
        'created' => 'Foydalanuvchi yaratildi',
        'updated' => 'Foydalanuvchi yangilandi',
        'cannot_change_own_role' => 'O\'z rolingizni o\'zgartirib bo\'lmaydi.',
    ],
    'invoices' => [
        'created' => 'Invoys yaratildi',
        'updated' => 'Invoys yangilandi',
        'create_failed' => 'Invoys yaratib bo\'lmadi.',
        'update_failed' => 'Invoysni yangilab bo\'lmadi.',
        'already_finalized' => 'Invoys allaqachon yakunlangan.',
        'already_expired' => 'Invoys muddati tugagan.',
        'amount_below_min' => 'Summasi minimal chegaradan past.',
        'amount_above_max' => 'Summasi maksimal chegaradan yuqori.',
    ],
    'addresses' => [
        'added' => 'Manzil qo\'shildi',
        'updated' => 'Manzil yangilandi',
        'add_failed' => 'Manzilni qo\'shib bo\'lmadi.',
        'errors' => [
            'not_found_blockchain' => 'Ko\'rsatilgan manzil blokcheynda topilmadi.',
            'duplicate' => 'Bu manzil tanlangan tarmoq va valyuta uchun allaqachon mavjud.',
            'unsupported_currency' => 'Qo\'llab-quvvatlanmaydigan valyuta.',
            'unsupported_currency_value' => 'Qo\'llab-quvvatlanmaydigan valyuta: :currency',
            'unsupported_network' => 'Qo\'llab-quvvatlanmaydigan tarmoq.',
            'unsupported_network_value' => 'Qo\'llab-quvvatlanmaydigan tarmoq: :network',
            'currency_mismatch' => 'Valyuta tanlangan tarmoqqa mos emas.',
            'currency_mismatch_value' => ':currency valyutasi :network tarmog\'ida mavjud emas.',
            'not_owner' => 'Manzil joriy foydalanuvchiga tegishli emas.',
            'not_exist_blockchain' => 'Manzil ko\'rsatilgan valyuta/tarmoq uchun blokcheynda mavjud emas.',
            'no_available' => 'Bu summa uchun mos manzil mavjud emas.',
            'invalid_balance' => 'Balans manfiy bo\'lmagan o\'nlik satr bo\'lishi kerak.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Maksimal summa :amount USDT dan oshmasligi kerak.',
    ],
    'blockchain' => [
        'only_tron' => 'Bu operatsiya faqat TRON tarmog\'ini qo\'llab-quvvatlaydi.',
        'only_usdt' => 'Bu operatsiya faqat USDT (TRC20) uchun qo\'llab-quvvatlanadi.',
        'malformed_trongrid_data' => 'Noto\'g\'ri TronGrid javobi: data massiv emas.',
        'malformed_trongrid_events' => 'Noto\'g\'ri TronGrid hodisa javobi: data massiv emas.',
        'trc20_transfer_missing' => 'Ko\'rsatilgan txid uchun TRC20 Transfer hodisasi topilmadi.',
        'malformed_wallet_nowblock' => 'Noto\'g\'ri Tron wallet nowblock javobi: obyekt emas.',
        'malformed_trongrid_account' => 'Noto\'g\'ri TronGrid akkaunt javobi: obyekt emas.',
        'account_not_found' => 'Akkaunt ma\'lumotlari topilmadi.',
        'malformed_trongrid_trc20' => 'Noto\'g\'ri TronGrid akkaunt javobi: trc20 massiv emas.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'So\'rovlar limiti oshib ketdi.',
        'request_failed' => 'So\'rov :status statusi bilan tugadi.',
    ],
    'api' => [
        'callback_failed' => 'Callback so\'rovi :status statusi bilan tugadi.',
    ],
    'money' => [
        'empty_string' => 'Summa satri bo\'sh.',
        'currency_mismatch' => 'Valyutalar mos kelishi kerak.',
    ],
];

