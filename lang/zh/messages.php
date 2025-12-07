<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => '未授权。',
    ],
    'impersonation' => [
        'forbidden' => '您没有权限冒充其他用户。',
        'self' => '您不能冒充自己的账户。',
        'admin_forbidden' => '您不能冒充管理员。',
        'logged_in_as' => '您现在已作为 :email 登录',
        'session_inactive' => '冒充模式未激活。',
        'session_missing' => '未找到原始会话，请重新登录。',
        'returned_to_admin' => '您已返回管理员账户。',
    ],
    'users' => [
        'roles' => [
            'admin' => '管理员',
            'admin_description' => '管理员',
            'user' => '用户',
            'user_description' => '普通用户',
        ],
        'created' => '用户已创建',
        'updated' => '用户已更新',
        'cannot_change_own_role' => '您不能修改自己的角色。',
    ],
    'invoices' => [
        'created' => '发票已创建',
        'updated' => '发票已更新',
        'create_failed' => '创建发票失败。',
        'update_failed' => '更新发票失败。',
        'already_finalized' => '发票已完成。',
        'already_expired' => '发票已过期。',
        'amount_below_min' => '金额低于允许的最小值。',
        'amount_above_max' => '金额超过允许的最大值。',
    ],
    'addresses' => [
        'added' => '地址已添加',
        'updated' => '地址已更新',
        'add_failed' => '添加地址失败。',
        'errors' => [
            'not_found_blockchain' => '在区块链上未找到指定地址。',
            'duplicate' => '所选网络和币种的地址已存在。',
            'unsupported_currency' => '不支持的币种。',
            'unsupported_currency_value' => '不支持的币种：:currency',
            'unsupported_network' => '不支持的网络。',
            'unsupported_network_value' => '不支持的网络：:network',
            'currency_mismatch' => '该币种在所选网络上不可用。',
            'currency_mismatch_value' => '币种 :currency 在网络 :network 上不可用。',
            'not_owner' => '地址不属于当前用户。',
            'not_exist_blockchain' => '指定币种/网络的地址在链上不存在。',
            'no_available' => '当前没有适合该金额的可用地址。',
            'invalid_balance' => '余额必须是非负的小数字符串。',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => '最大金额不能超过 :amount USDT。',
    ],
    'blockchain' => [
        'only_tron' => '此操作仅支持 TRON 网络。',
        'only_usdt' => '此操作仅支持 USDT (TRC20)。',
        'malformed_trongrid_data' => 'TronGrid 响应格式错误：data 不是数组。',
        'malformed_trongrid_events' => 'TronGrid 事件响应格式错误：data 不是数组。',
        'trc20_transfer_missing' => '未找到给定 txid 的 TRC20 Transfer 事件。',
        'malformed_wallet_nowblock' => 'Tron 钱包 nowblock 响应格式错误：不是对象。',
        'malformed_trongrid_account' => 'TronGrid 账户响应格式错误：不是对象。',
        'account_not_found' => '未找到账户数据。',
        'malformed_trongrid_trc20' => 'TronGrid 账户响应格式错误：trc20 不是数组。',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => '配额超出。',
        'request_failed' => '请求失败，状态码 :status。',
    ],
    'api' => [
        'callback_failed' => '回调请求失败，状态码 :status。',
    ],
    'money' => [
        'empty_string' => '金额字符串为空。',
        'currency_mismatch' => '币种必须一致。',
    ],
];


