<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Не авторизован.',
    ],
    'impersonation' => [
        'forbidden' => 'Нет прав для входа под другим пользователем.',
        'self' => 'Нельзя войти под своей учётной записью.',
        'admin_forbidden' => 'Нельзя входить под администратором.',
        'logged_in_as' => 'Вы вошли как :email',
        'session_inactive' => 'Режим входа под пользователем не активен.',
        'session_missing' => 'Исходная сессия не найдена, войдите снова.',
        'returned_to_admin' => 'Вы вернулись в админскую учётную запись.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Администратор',
            'admin_description' => 'Администратор',
            'user' => 'Пользователь',
            'user_description' => 'Обычный пользователь',
        ],
        'created' => 'Пользователь создан',
        'updated' => 'Пользователь обновлён',
        'cannot_change_own_role' => 'Нельзя менять собственную роль.',
    ],
    'invoices' => [
        'created' => 'Инвойс создан',
        'updated' => 'Инвойс обновлён',
        'create_failed' => 'Не удалось создать инвойс.',
        'update_failed' => 'Не удалось обновить инвойс.',
        'already_finalized' => 'Инвойс уже финализирован.',
        'already_expired' => 'Инвойс уже просрочен.',
        'amount_below_min' => 'Сумма ниже минимально допустимой.',
        'amount_above_max' => 'Сумма выше максимально допустимой.',
    ],
    'addresses' => [
        'added' => 'Адрес добавлен',
        'updated' => 'Адрес обновлён',
        'add_failed' => 'Не удалось добавить адрес.',
        'errors' => [
            'not_found_blockchain' => 'Указанный адрес не найден в блокчейне.',
            'duplicate' => 'Адрес уже существует для выбранной сети и валюты.',
            'unsupported_currency' => 'Неподдерживаемая валюта.',
            'unsupported_currency_value' => 'Неподдерживаемая валюта: :currency',
            'unsupported_network' => 'Неподдерживаемая сеть.',
            'unsupported_network_value' => 'Неподдерживаемая сеть: :network',
            'currency_mismatch' => 'Эта валюта недоступна в выбранной сети.',
            'currency_mismatch_value' => 'Валюта :currency недоступна в сети :network.',
            'not_owner' => 'Адрес не принадлежит текущему пользователю.',
            'not_exist_blockchain' => 'Адрес отсутствует в блокчейне для указанной валюты/сети.',
            'no_available' => 'Нет доступного адреса под эту сумму.',
            'invalid_balance' => 'Баланс должен быть неотрицательной десятичной строкой.',
        ],
    ],
    'merchants' => [
        'created' => 'Мерчант добавлен',
        'updated' => 'Мерчант обновлён',
        'create_failed' => 'Не удалось создать мерчанта.',
        'update_failed' => 'Не удалось обновить мерчанта.',
        'errors' => [
            'logo_invalid' => 'Не удалось определить файл логотипа.',
            'logo_not_square' => 'Логотип должен быть квадратным.',
            'logo_too_large' => 'Размер логотипа не должен превышать 500x500 пикселей.',
            'not_owner' => 'Мерчант не принадлежит текущему пользователю.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Максимальная сумма не может превышать :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Для этой операции поддерживается только сеть TRON.',
        'only_usdt' => 'Для этой операции поддерживается только USDT (TRC20).',
        'malformed_trongrid_data' => 'Некорректный ответ TronGrid: data не является массивом.',
        'malformed_trongrid_events' => 'Некорректный ответ TronGrid по событиям: data не является массивом.',
        'trc20_transfer_missing' => 'Событие TRC20 Transfer не найдено для указанного txid.',
        'malformed_wallet_nowblock' => 'Некорректный ответ Tron wallet nowblock: не объект.',
        'malformed_trongrid_account' => 'Некорректный ответ TronGrid по аккаунту: не объект.',
        'account_not_found' => 'Данные аккаунта не найдены.',
        'malformed_trongrid_trc20' => 'Некорректный ответ TronGrid по аккаунту: trc20 не является массивом.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Превышен лимит запросов.',
        'request_failed' => 'Запрос завершился со статусом :status.',
    ],
    'api' => [
        'callback_failed' => 'Callback-запрос завершился со статусом :status.',
    ],
    'money' => [
        'empty_string' => 'Пустая строка суммы.',
        'currency_mismatch' => 'Валюты должны совпадать.',
    ],
];

