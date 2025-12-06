<?php

declare(strict_types=1);

return [
    'layout' => [
        'page_titles' => [
            'dashboard' => 'Dashboard',
            'addresses' => 'Addresses',
            'invoices' => 'Invoices',
            'callback_logs' => 'Callback logs',
            'app_settings' => 'Global settings',
            'api_docs' => 'API & documentation',
            'users' => 'Users',
        ],
    ],
    'dashboard' => [
        'stats' => [
            'total' => 'Total invoices',
            'active' => 'Active: {count}',
            'paid' => 'Paid',
            'success' => 'Success: {rate}%',
            'expired' => 'Expired',
            'addresses' => 'Addresses: {count}',
        ],
    ],
    'addresses' => [
        'list' => [
            'title' => 'Addresses list',
        ],
        'modals' => [
            'create' => [
                'title' => 'Add new address',
                'description' => 'Fill in the details to add a new address',
            ],
        ],
        'actions' => [
            'add' => 'Add address',
            'create' => 'Create address',
        ],
        'fields' => [
            'currency' => 'Currency',
            'currency_placeholder' => 'Select currency',
            'network' => 'Network',
            'network_placeholder' => 'Select network',
            'address' => 'Address',
            'address_placeholder' => 'For example: T...',
        ],
    ],
    'invoices' => [
        'modals' => [
            'create' => [
                'title' => 'Create invoice',
                'description' => 'Fill in the details to issue an invoice',
            ],
            'edit' => [
                'title' => 'Edit invoice',
                'description' => 'Change status and provide TXID if needed',
            ],
        ],
        'fields' => [
            'currency' => 'Currency',
            'currency_hint' => 'E.g.: BTC, ETH, USDT',
            'currency_placeholder' => 'Select currency',
            'network' => 'Network',
            'network_hint' => 'Choose a network for the currency',
            'network_placeholder' => 'Select network',
            'amount' => 'Amount',
            'amount_placeholder' => 'For example: 12.34',
            'amount_hint' => 'Decimal format',
            'external_id' => 'External ID (optional)',
            'callback_url' => 'Callback URL (optional)',
            'tag' => 'Tag (optional)',
            'metadata' => 'Metadata (JSON, optional)',
            'metadata_placeholder' => '{"key":"value"}',
            'status' => 'Status',
        ],
        'actions' => [
            'create' => 'Create',
            'save' => 'Save',
        ],
    ],
    'nav' => [
        'dashboard' => 'Dashboard',
        'addresses' => 'Addresses',
        'invoices' => 'Invoices',
        'callback_logs' => 'Callback logs',
        'users' => 'Users',
        'api_docs' => 'API & documentation',
        'app_settings' => 'Settings',
        'profile' => 'Profile',
        'impersonation_title' => 'Impersonation mode',
        'impersonation_description' => 'You are viewing the interface as another user.',
        'impersonation_return' => 'Return to admin',
    ],
    'common' => [
        'close' => 'Close',
        'copy' => 'Copy',
        'copied' => 'Copied',
        'copy_failed' => 'Failed to copy',
        'confirmation' => 'Confirmation',
        'confirm_message' => 'Are you sure you want to perform this action?',
        'confirm' => 'Confirm',
        'cancel' => 'Cancel',
        'logout' => 'Log out',
        'id' => 'ID',
        'address' => 'Address',
        'balance' => 'Balance',
        'currency' => 'Currency',
        'active' => 'Active',
    ],
    'settings' => [
        'title' => 'Account settings',
        'nav' => [
            'profile' => 'Profile',
            'password' => 'Password',
            'two_factor' => '2FA authentication',
            'login_history' => 'Login history',
            'appearance' => 'Appearance',
        ],
        'logout' => [
            'title' => 'Log out',
            'message' => 'Are you sure you want to log out?',
            'confirm' => 'Log out',
        ],
    ],
];

