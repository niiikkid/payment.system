<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Рұқсат етілмеген.',
    ],
    'impersonation' => [
        'forbidden' => 'Басқа пайдаланушы атынан кіруге құқығыңыз жоқ.',
        'self' => 'Өз есептік жазбаңызға атынан кіру мүмкін емес.',
        'admin_forbidden' => 'Әкімші аккаунтына атынан кіруге болмайды.',
        'logged_in_as' => 'Сіз енді :email ретінде кірдіңіз',
        'session_inactive' => 'Пайдаланушы атынан кіру режимі белсенді емес.',
        'session_missing' => 'Бастапқы сессия табылмады, қайта кіріңіз.',
        'returned_to_admin' => 'Сіз әкімші аккаунтына қайттыңыз.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Әкімші',
            'admin_description' => 'Әкімші',
            'user' => 'Пайдаланушы',
            'user_description' => 'Қарапайым пайдаланушы',
        ],
        'created' => 'Пайдаланушы құрылды',
        'updated' => 'Пайдаланушы жаңартылды',
        'cannot_change_own_role' => 'Өз рөліңізді өзгертуге болмайды.',
    ],
    'invoices' => [
        'created' => 'Инвойс жасалды',
        'updated' => 'Инвойс жаңартылды',
        'create_failed' => 'Инвойсты жасау мүмкін болмады.',
        'update_failed' => 'Инвойсты жаңарту мүмкін болмады.',
        'already_finalized' => 'Инвойс қазірдің өзінде финалданған.',
        'already_expired' => 'Инвойс мерзімі өтіп кеткен.',
        'amount_below_min' => 'Сома ең төменгі мәннен аз.',
        'amount_above_max' => 'Сома ең жоғарғы мәннен көп.',
    ],
    'addresses' => [
        'added' => 'Адрес қосылды',
        'updated' => 'Адрес жаңартылды',
        'add_failed' => 'Адрес қосу мүмкін болмады.',
        'errors' => [
            'not_found_blockchain' => 'Көрсетілген адрес блокчейнден табылмады.',
            'duplicate' => 'Бұл желі мен валюта үшін адрес қазірдің өзінде бар.',
            'unsupported_currency' => 'Қолдау көрсетілмейтін валюта.',
            'unsupported_currency_value' => 'Қолдау көрсетілмейтін валюта: :currency',
            'unsupported_network' => 'Қолдау көрсетілмейтін желі.',
            'unsupported_network_value' => 'Қолдау көрсетілмейтін желі: :network',
            'currency_mismatch' => 'Бұл валюта таңдалған желіде қолжетімсіз.',
            'currency_mismatch_value' => ':currency валютасы :network желісінде қолжетімсіз.',
            'not_owner' => 'Адрес ағымдағы пайдаланушыға тиесілі емес.',
            'not_exist_blockchain' => 'Көрсетілген валюта/желім үшін адрес блокчейнда жоқ.',
            'no_available' => 'Қазіргі уақытта осы сомаға қолжетімді адрес жоқ.',
            'invalid_balance' => 'Баланс теріс емес ондық жол болуы тиіс.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Ең үлкен сома :amount USDT-тан аспауы керек.',
    ],
    'blockchain' => [
        'only_tron' => 'Бұл операция үшін тек TRON желісі қолжетімді.',
        'only_usdt' => 'Бұл операция үшін тек USDT (TRC20) қолжетімді.',
        'malformed_trongrid_data' => 'TronGrid жауабы қате: data массив емес.',
        'malformed_trongrid_events' => 'TronGrid оқиғалар жауабы қате: data массив емес.',
        'trc20_transfer_missing' => 'Көрсетілген txid үшін TRC20 Transfer оқиғасы табылмады.',
        'malformed_wallet_nowblock' => 'Tron wallet nowblock жауабы қате: объект емес.',
        'malformed_trongrid_account' => 'TronGrid аккаунт жауабы қате: объект емес.',
        'account_not_found' => 'Аккаунт деректері табылмады.',
        'malformed_trongrid_trc20' => 'TronGrid аккаунт жауабы қате: trc20 массив емес.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Сұраулар лимиті асып кетті.',
        'request_failed' => 'Сұрау :status статусымен аяқталды.',
    ],
    'api' => [
        'callback_failed' => 'Callback-сұрау :status статусымен аяқталды.',
    ],
    'money' => [
        'empty_string' => 'Сома жолы бос.',
        'currency_mismatch' => 'Валюталар сәйкес келуі тиіс.',
    ],
];


