<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Unauthorized.',
    ],
    'impersonation' => [
        'forbidden' => 'You do not have permission to impersonate another user.',
        'self' => 'You cannot impersonate your own account.',
        'admin_forbidden' => 'You cannot impersonate an administrator.',
        'logged_in_as' => 'You are now logged in as :email',
        'session_inactive' => 'Impersonation mode is not active.',
        'session_missing' => 'Original session not found, please log in again.',
        'returned_to_admin' => 'You have returned to the admin account.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrator',
            'admin_description' => 'Administrator',
            'user' => 'User',
            'user_description' => 'Regular user',
        ],
        'created' => 'User created',
        'updated' => 'User updated',
        'cannot_change_own_role' => 'You cannot change your own role.',
    ],
    'invoices' => [
        'created' => 'Invoice created',
        'updated' => 'Invoice updated',
        'create_failed' => 'Failed to create invoice.',
        'update_failed' => 'Failed to update invoice.',
        'already_finalized' => 'Invoice already finalized.',
        'already_expired' => 'Invoice already expired.',
        'amount_below_min' => 'Amount is below the allowed minimum.',
        'amount_above_max' => 'Amount exceeds the allowed maximum.',
    ],
    'addresses' => [
        'added' => 'Address added',
        'updated' => 'Address updated',
        'add_failed' => 'Failed to add address.',
        'errors' => [
            'not_found_blockchain' => 'The specified address was not found on the blockchain.',
            'duplicate' => 'The address already exists for the selected network and currency.',
            'unsupported_currency' => 'Unsupported currency.',
            'unsupported_currency_value' => 'Unsupported currency: :currency',
            'unsupported_network' => 'Unsupported network.',
            'unsupported_network_value' => 'Unsupported network: :network',
            'currency_mismatch' => 'This currency is not available on the selected network.',
            'currency_mismatch_value' => 'Currency :currency is not available on network :network.',
            'not_owner' => 'Address does not belong to the current user.',
            'not_exist_blockchain' => 'Address does not exist on blockchain for the specified currency/network.',
            'no_available' => 'No available address for the given amount right now.',
            'invalid_balance' => 'Balance must be a non-negative decimal string.',
        ],
    ],
    'merchants' => [
        'created' => 'Merchant created',
        'updated' => 'Merchant updated',
        'create_failed' => 'Failed to create merchant.',
        'update_failed' => 'Failed to update merchant.',
        'errors' => [
            'logo_invalid' => 'Unable to read the logo file.',
            'logo_not_square' => 'Logo must be square.',
            'logo_too_large' => 'Logo must not exceed 500x500 pixels.',
            'not_owner' => 'Merchant does not belong to the current user.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'The maximum amount cannot exceed :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Only TRON network is supported for this operation.',
        'only_usdt' => 'Only USDT (TRC20) is supported for this operation.',
        'malformed_trongrid_data' => 'Malformed TronGrid response: data is not an array.',
        'malformed_trongrid_events' => 'Malformed TronGrid events response: data is not an array.',
        'trc20_transfer_missing' => 'TRC20 Transfer event not found for the given txid.',
        'malformed_wallet_nowblock' => 'Malformed Tron wallet nowblock response: not an object.',
        'malformed_trongrid_account' => 'Malformed TronGrid account response: not an object.',
        'account_not_found' => 'Account data not found.',
        'malformed_trongrid_trc20' => 'Malformed TronGrid account response: trc20 is not an array.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Quota exceeded.',
        'request_failed' => 'Request failed with status :status.',
    ],
    'api' => [
        'callback_failed' => 'Callback request failed with status :status.',
    ],
    'money' => [
        'empty_string' => 'Empty money string.',
        'currency_mismatch' => 'Currencies must match.',
    ],
];

