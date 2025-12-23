<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Не авторизований.',
        'pending_approval' => 'Очікує схвалення адміністратором.',
    ],
    'impersonation' => [
        'forbidden' => 'Немає прав для входу під іншим користувачем.',
        'self' => 'Неможливо увійти під власним обліковим записом.',
        'admin_forbidden' => 'Неможливо увійти під адміністратором.',
        'logged_in_as' => 'Ви увійшли як :email',
        'session_inactive' => 'Режим входу під користувачем не активний.',
        'session_missing' => 'Початкова сесія не знайдена, увійдіть знову.',
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
        'cannot_change_own_role' => 'Неможливо змінювати власну роль.',
    ],
    'invoices' => [
        'created' => 'Інвойс створено',
        'updated' => 'Інвойс оновлено',
        'create_failed' => 'Не вдалося створити інвойс.',
        'update_failed' => 'Не вдалося оновити інвойс.',
        'already_finalized' => 'Інвойс вже фіналізовано.',
        'already_expired' => 'Інвойс вже прострочений.',
        'amount_below_min' => 'Сума нижча за мінімально допустиму.',
        'amount_above_max' => 'Сума вища за максимально допустиму.',
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
            'not_exist_blockchain' => 'Адреса відсутня в блокчейні для вказаної валюти/мережі.',
            'no_available' => 'Немає доступної адреси під цю суму.',
            'invalid_balance' => 'Баланс має бути невід’ємним десятковим рядком.',
        ],
    ],
    'merchants' => [
        'created' => 'Мерчанта додано',
        'updated' => 'Мерчанта оновлено',
        'create_failed' => 'Не вдалося створити мерчанта.',
        'update_failed' => 'Не вдалося оновити мерчанта.',
        'errors' => [
            'logo_invalid' => 'Не вдалося визначити файл логотипа.',
            'logo_not_square' => 'Логотип має бути квадратним.',
            'logo_too_large' => 'Розмір логотипа не має перевищувати 500x500 пікселів.',
            'not_owner' => 'Мерчант не належить поточному користувачу.',
        ],
    ],
    'clients' => [
        'created' => 'Клієнта створено',
        'updated' => 'Клієнта оновлено',
        'create_failed' => 'Не вдалося створити клієнта.',
        'update_failed' => 'Не вдалося оновити клієнта.',
        'errors' => [
            'duplicate_external_id' => 'Клієнт з таким Client ID вже існує.',
            'empty_external_id' => 'Client ID є обов’язковим.',
            'not_owner' => 'Клієнт не належить поточному користувачу.',
        ],
    ],
    'markets' => [
        'created' => 'Фіат додано',
        'updated' => 'Налаштування збережено',
        'refreshed' => 'Курси оновлено',
    ],
    'settings' => [
        'max_limit_exceeded' => 'Максимальна сума не може перевищувати :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Для цієї операції підтримується лише мережа TRON.',
        'only_usdt' => 'Для цієї операції підтримується лише USDT (TRC20).',
        'malformed_trongrid_data' => 'Некоректна відповідь TronGrid: data не є масивом.',
        'malformed_trongrid_events' => 'Некоректна відповідь TronGrid по подіях: data не є масивом.',
        'trc20_transfer_missing' => 'Подію TRC20 Transfer не знайдено для вказаного txid.',
        'malformed_wallet_nowblock' => 'Некоректна відповідь Tron wallet nowblock: не об’єкт.',
        'malformed_trongrid_account' => 'Некоректна відповідь TronGrid по акаунту: не об’єкт.',
        'account_not_found' => 'Дані акаунту не знайдено.',
        'malformed_trongrid_trc20' => 'Некоректна відповідь TronGrid по акаунту: trc20 не є масивом.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Перевищено ліміт запитів.',
        'request_failed' => 'Запит завершився зі статусом :status.',
    ],
    'api' => [
        'callback_failed' => 'Callback-запит завершився зі статусом :status.',
        'ip_not_allowed' => 'Запити з цієї IP-адреси заборонені для поточного токена.',
    ],
    'money' => [
        'empty_string' => 'Порожній рядок суми.',
        'currency_mismatch' => 'Валюти мають збігатися.',
    ],
    'notifications' => [
        'marked_read' => 'Сповіщення позначено прочитаним.',
        'marked_unread' => 'Сповіщення позначено непрочитаним.',
        'all_read' => 'Усі сповіщення позначено прочитаними.',
        'rule_created' => 'Правило сповіщень створено.',
        'rule_updated' => 'Правило сповіщень оновлено.',
        'rule_deleted' => 'Правило сповіщень видалено.',
        'events' => [
            'invoice_created' => 'Створення інвойсу',
            'invoice_status_changed' => 'Зміна статусу інвойсу',
        ],
        'channels' => [
            'in_app' => 'Внутрішні',
            'telegram' => 'Telegram',
        ],
        'delivery_statuses' => [
            'pending' => 'Очікує відправлення',
            'delivered' => 'Доставлено',
            'failed' => 'Не доставлено',
        ],
        'telegram' => [
            'link_refreshed' => 'Посилання для Telegram оновлено. Натисніть Start у боті, щоб увімкнути сповіщення.',
            'not_linked' => 'Telegram не підключено. Оновіть посилання та натисніть Start у боті.',
            'delivery_failed' => 'Не вдалося доставити повідомлення в Telegram.',
            'invalid_token' => 'Токен прив’язки недійсний. Згенеруйте нове посилання в панелі.',
            'start_success' => 'Бот запущено — тут ви будете отримувати сповіщення.',
            'start_error' => 'Не вдалося обробити команду. Спробуйте пізніше.',
            'start_missing_token' => 'Для прив’язки використайте посилання з особистого кабінету та натисніть Start.',
        ],
        'templates' => [
            'invoice_created' => [
                'title' => 'Створено інвойс',
                'body' => 'Створено новий інвойс на суму :amount :currency.',
            ],
            'invoice_status_changed' => [
                'title' => 'Статус інвойсу змінено',
                'body' => 'Статус змінено з :previous на :status для суми :amount :currency.',
            ],
        ],
    ],
];


