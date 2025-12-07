<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Рухсат жок.',
    ],
    'impersonation' => [
        'forbidden' => 'Башка колдонуучунун атынан кирүүгө укугуңуз жок.',
        'self' => 'Өз аккаунтуңуздун атынан кирүү мүмкүн эмес.',
        'admin_forbidden' => 'Администратордун аккаунтуна атынан кирүүгө болбойт.',
        'logged_in_as' => 'Сиз азыр :email катары кирдиңиз',
        'session_inactive' => 'Имперсонация режими активдүү эмес.',
        'session_missing' => 'Баштапкы сессия табылган жок, кайра кириңиз.',
        'returned_to_admin' => 'Сиз админ аккаунтуна кайтып келдиңиз.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Администратор',
            'admin_description' => 'Администратор',
            'user' => 'Колдонуучу',
            'user_description' => 'Катталган колдонуучу',
        ],
        'created' => 'Колдонуучу түзүлдү',
        'updated' => 'Колдонуучу жаңыртылды',
        'cannot_change_own_role' => 'Өз ролиңизди өзгөртө албайсыз.',
    ],
    'invoices' => [
        'created' => 'Инвойс түзүлдү',
        'updated' => 'Инвойс жаңыртылды',
        'create_failed' => 'Инвойс түзүү мүмкүн болгон жок.',
        'update_failed' => 'Инвойс жаңыртуу мүмкүн болгон жок.',
        'already_finalized' => 'Инвойс мурунтан эле финалданган.',
        'already_expired' => 'Инвойстун мөөнөтү өтүп кеткен.',
        'amount_below_min' => 'Сома уруксат берилген минимумдан төмөн.',
        'amount_above_max' => 'Сома уруксат берилген максимумдан жогору.',
    ],
    'addresses' => [
        'added' => 'Дарек кошулду',
        'updated' => 'Дарек жаңыртылды',
        'add_failed' => 'Даректи кошуу мүмкүн болгон жок.',
        'errors' => [
            'not_found_blockchain' => 'Көрсөтүлгөн дарек блокчейнде табылган жок.',
            'duplicate' => 'Бул тармак жана валюта үчүн дарек мурунтан бар.',
            'unsupported_currency' => 'Колдоого алынбаган валюта.',
            'unsupported_currency_value' => 'Колдоого алынбаган валюта: :currency',
            'unsupported_network' => 'Колдоого алынбаган тармак.',
            'unsupported_network_value' => 'Колдоого алынбаган тармак: :network',
            'currency_mismatch' => 'Бул валюта тандалган тармакта жеткиликтүү эмес.',
            'currency_mismatch_value' => ':currency валютасы :network тармагында жеткиликтүү эмес.',
            'not_owner' => 'Дарек учурдагы колдонуучуга таандык эмес.',
            'not_exist_blockchain' => 'Көрсөтүлгөн валюта/тармак үчүн дарек блокчейнде жок.',
            'no_available' => 'Учурда бул сумма үчүн жеткиликтүү дарек жок.',
            'invalid_balance' => 'Баланс терс эмес ондук сап болушу керек.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Максималдуу сумма :amount USDT ашпоого тийиш.',
    ],
    'blockchain' => [
        'only_tron' => 'Бул операция үчүн TRON тармагы гана колдоого алынат.',
        'only_usdt' => 'Бул операция үчүн USDT (TRC20) гана колдоого алынат.',
        'malformed_trongrid_data' => 'TronGrid жообу туура эмес: data массив эмес.',
        'malformed_trongrid_events' => 'TronGrid окуялар жообу туура эмес: data массив эмес.',
        'trc20_transfer_missing' => 'Көрсөтүлгөн txid үчүн TRC20 Transfer окуясы табылган жок.',
        'malformed_wallet_nowblock' => 'Tron wallet nowblock жообу туура эмес: объект эмес.',
        'malformed_trongrid_account' => 'TronGrid аккаунт жообу туура эмес: объект эмес.',
        'account_not_found' => 'Аккаунт боюнча маалымат табылган жок.',
        'malformed_trongrid_trc20' => 'TronGrid аккаунт жообу туура эмес: trc20 массив эмес.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Суроолор лимити ашып кетти.',
        'request_failed' => 'Суроо :status статусу менен аяктады.',
    ],
    'api' => [
        'callback_failed' => 'Callback-суроо :status статусу менен аяктады.',
    ],
    'money' => [
        'empty_string' => 'Сома сап бош.',
        'currency_mismatch' => 'Валюталар дал келиши керек.',
    ],
];


