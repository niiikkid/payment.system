<?php

declare(strict_types=1);

return [
    'layout' => [
        'page_titles' => [
            'dashboard' => 'Главная',
            'addresses' => 'Адреса',
            'invoices' => 'Инвойсы',
            'callback_logs' => 'Callback логи',
            'app_settings' => 'Глобальные настройки',
            'api_docs' => 'API и документация',
            'users' => 'Пользователи',
        ],
    ],
    'dashboard' => [
        'stats' => [
            'total' => 'Всего инвойсов',
            'active' => 'Активных: {count}',
            'paid' => 'Оплачено',
            'success' => 'Успех: {rate}%',
            'expired' => 'Истёкших',
            'addresses' => 'Адресов: {count}',
        ],
    ],
    'addresses' => [
        'list' => [
            'title' => 'Список адресов',
        ],
        'modals' => [
            'create' => [
                'title' => 'Добавить новый адрес',
                'description' => 'Заполните данные для добавления нового адреса',
            ],
        ],
        'actions' => [
            'add' => 'Добавить адрес',
            'create' => 'Создать адрес',
        ],
        'fields' => [
            'currency' => 'Валюта',
            'currency_placeholder' => 'Выберите валюту',
            'network' => 'Сеть',
            'network_placeholder' => 'Выберите сеть',
            'address' => 'Адрес',
            'address_placeholder' => 'Например: T...',
        ],
    ],
    'invoices' => [
        'modals' => [
            'create' => [
                'title' => 'Создать инвойс',
                'description' => 'Заполните данные для выставления инвойса',
            ],
            'edit' => [
                'title' => 'Редактировать инвойс',
                'description' => 'Сменить статус и указать TXID при необходимости',
            ],
        ],
        'fields' => [
            'currency' => 'Валюта',
            'currency_hint' => 'Напр.: BTC, ETH, USDT',
            'currency_placeholder' => 'Выберите валюту',
            'network' => 'Сеть',
            'network_hint' => 'Выберите сеть для валюты',
            'network_placeholder' => 'Выберите сеть',
            'amount' => 'Сумма',
            'amount_placeholder' => 'Например: 12.34',
            'amount_hint' => 'Десятичный формат',
            'external_id' => 'Внешний ID (опц.)',
            'callback_url' => 'Callback URL (опц.)',
            'tag' => 'Тег (опц.)',
            'metadata' => 'Metadata (JSON, опц.)',
            'metadata_placeholder' => '{"key":"value"}',
            'status' => 'Статус',
        ],
        'actions' => [
            'create' => 'Создать',
            'save' => 'Сохранить',
        ],
    ],
    'nav' => [
        'dashboard' => 'Главная',
        'addresses' => 'Адреса',
        'invoices' => 'Инвойсы',
        'callback_logs' => 'Callback логи',
        'users' => 'Пользователи',
        'api_docs' => 'API и документация',
        'app_settings' => 'Настройки',
        'profile' => 'Профиль',
        'impersonation_title' => 'Режим пользователя',
        'impersonation_description' => 'Вы просматриваете интерфейс как другой пользователь.',
        'impersonation_return' => 'Вернуться в админку',
    ],
    'common' => [
        'close' => 'Закрыть',
        'copy' => 'Скопировать',
        'copied' => 'Скопировано',
        'copy_failed' => 'Не удалось скопировать',
        'confirmation' => 'Подтверждение',
        'confirm_message' => 'Вы уверены, что хотите выполнить это действие?',
        'confirm' => 'Подтвердить',
        'cancel' => 'Отмена',
        'logout' => 'Выйти',
        'id' => 'ID',
        'address' => 'Адрес',
        'balance' => 'Баланс',
        'currency' => 'Валюта',
        'active' => 'Активен',
    ],
    'settings' => [
        'title' => 'Настройки аккаунта',
        'nav' => [
            'profile' => 'Профиль',
            'password' => 'Пароль',
            'two_factor' => '2FA авторизация',
            'login_history' => 'История входов',
            'appearance' => 'Внешний вид',
        ],
        'logout' => [
            'title' => 'Выйти из аккаунта',
            'message' => 'Вы действительно хотите выйти?',
            'confirm' => 'Выйти',
        ],
    ],
];

