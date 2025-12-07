<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Не авторизовано.',
    ],
    'impersonation' => [
        'forbidden' => 'Немає прав входити під іншим користувачем.',
        'self' => 'Не можна увійти під власним обліковим записом.',
        'admin_forbidden' => 'Не можна входити під адміністратором.',
        'logged_in_as' => 'Ви увійшли як :email',
        'session_inactive' => 'Режим наслідування користувача не активний.',
        'session_missing' => 'Початкова сесія не знайдена, увійдіть ще раз.',
        'returned_to_admin' => 'Ви повернулися до адмінського облікового запису.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Адміністратор',
            'admin_description' => 'Адміністратор',
            'user' => 'Користувач',
            'user_description' => 'Звичайний користувач',
        ],
        'created' => 'Користувача створено',
        'updated' => 'Користувача оновлено',
        'cannot_change_own_role' => 'Не можна змінювати власну роль.',
    ],
    'invoices' => [
        'created' => 'Рахунок створено',
        'updated' => 'Рахунок оновлено',
        'create_failed' => 'Не вдалося створити рахунок.',
        'update_failed' => 'Не вдалося оновити рахунок.',
        'already_finalized' => 'Рахунок уже фіналізовано.',
        'already_expired' => 'Рахунок уже прострочено.',
        'amount_below_min' => 'Сума нижча за мінімально дозволену.',
        'amount_above_max' => 'Сума перевищує максимально дозволену.',
    ],
    'addresses' => [
        'added' => 'Адресу додано',
        'updated' => 'Адресу оновлено',
        'add_failed' => 'Не вдалося додати адресу.',
        'errors' => [
            'not_found_blockchain' => 'Вказану адресу не знайдено в блокчейні.',
            'duplicate' => 'Адреса вже існує для вибраної мережі та валюти.',
            'unsupported_currency' => 'Непідтримувана валюта.',
            'unsupported_currency_value' => 'Непідтримувана валюта: :currency',
            'unsupported_network' => 'Непідтримувана мережа.',
            'unsupported_network_value' => 'Непідтримувана мережа: :network',
            'currency_mismatch' => 'Ця валюта недоступна у вибраній мережі.',
            'currency_mismatch_value' => 'Валюта :currency недоступна в мережі :network.',
            'not_owner' => 'Адреса не належить поточному користувачу.',
            'not_exist_blockchain' => 'Адреси немає в блокчейні для вказаної валюти/мережі.',
            'no_available' => 'Немає доступної адреси під цю суму.',
            'invalid_balance' => 'Баланс має бути невід’ємним десятковим рядком.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Максимальна сума не може перевищувати :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Для цієї операції підтримується лише мережа TRON.',
        'only_usdt' => 'Для цієї операції підтримується лише USDT (TRC20).',
        'malformed_trongrid_data' => 'Некоректна відповідь TronGrid: data не є масивом.',
        'malformed_trongrid_events' => 'Некоректна відповідь TronGrid по подіях: data не є масивом.',
        'trc20_transfer_missing' => 'Подію TRC20 Transfer не знайдено для зазначеного txid.',
        'malformed_wallet_nowblock' => 'Некоректна відповідь Tron wallet nowblock: не об’єкт.',
        'malformed_trongrid_account' => 'Некоректна відповідь TronGrid по акаунту: не об’єкт.',
        'account_not_found' => 'Дані акаунту не знайдено.',
        'malformed_trongrid_trc20' => 'Некоректна відповідь TronGrid по акаунту: trc20 не є масивом.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Ліміт запитів вичерпано.',
        'request_failed' => 'Запит завершився зі статусом :status.',
    ],
    'api' => [
        'callback_failed' => 'Callback-запит завершився зі статусом :status.',
    ],
    'money' => [
        'empty_string' => 'Порожній рядок суми.',
        'currency_mismatch' => 'Валюти мають збігатися.',
    ],
];

