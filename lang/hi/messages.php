<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'अनुमति नहीं है.',
    ],
    'impersonation' => [
        'forbidden' => 'आपको किसी अन्य उपयोगकर्ता के रूप में लॉग इन करने की अनुमति नहीं है।',
        'self' => 'आप अपने ही खाते का प्रतिरूपण नहीं कर सकते।',
        'admin_forbidden' => 'आप प्रशासक का प्रतिरूपण नहीं कर सकते।',
        'logged_in_as' => 'अब आप :email के रूप में लॉग इन हैं।',
        'session_inactive' => 'इम्पर्सनेशन मोड सक्रिय नहीं है।',
        'session_missing' => 'मूल सत्र नहीं मिला, कृपया दोबारा लॉग इन करें।',
        'returned_to_admin' => 'आप एडमिन खाते पर वापस लौट आए हैं।',
    ],
    'users' => [
        'roles' => [
            'admin' => 'प्रशासक',
            'admin_description' => 'प्रशासक',
            'user' => 'उपयोगकर्ता',
            'user_description' => 'साधारण उपयोगकर्ता',
        ],
        'created' => 'उपयोगकर्ता बनाया गया',
        'updated' => 'उपयोगकर्ता अपडेट किया गया',
        'cannot_change_own_role' => 'आप अपनी भूमिका नहीं बदल सकते।',
    ],
    'invoices' => [
        'created' => 'चालान बनाया गया',
        'updated' => 'चालान अपडेट किया गया',
        'create_failed' => 'चालान बनाना असफल रहा।',
        'update_failed' => 'चालान अपडेट करना असफल रहा।',
        'already_finalized' => 'चालान पहले ही अंतिम रूप दिया जा चुका है।',
        'already_expired' => 'चालान की समय सीमा समाप्त हो चुकी है।',
        'amount_below_min' => 'राशि अनुमत न्यूनतम से कम है।',
        'amount_above_max' => 'राशि अनुमत अधिकतम से अधिक है।',
    ],
    'addresses' => [
        'added' => 'पता जोड़ा गया',
        'updated' => 'पता अपडेट किया गया',
        'add_failed' => 'पता जोड़ना असफल रहा।',
        'errors' => [
            'not_found_blockchain' => 'निर्दिष्ट पता ब्लॉकचेन पर नहीं मिला।',
            'duplicate' => 'यह पता चयनित नेटवर्क और मुद्रा के लिए पहले से मौजूद है।',
            'unsupported_currency' => 'असमर्थित मुद्रा।',
            'unsupported_currency_value' => 'असमर्थित मुद्रा: :currency',
            'unsupported_network' => 'असमर्थित नेटवर्क।',
            'unsupported_network_value' => 'असमर्थित नेटवर्क: :network',
            'currency_mismatch' => 'यह मुद्रा चयनित नेटवर्क पर उपलब्ध नहीं है।',
            'currency_mismatch_value' => 'मुद्रा :currency नेटवर्क :network पर उपलब्ध नहीं है।',
            'not_owner' => 'पता वर्तमान उपयोगकर्ता का नहीं है।',
            'not_exist_blockchain' => 'निर्दिष्ट मुद्रा/नेटवर्क के लिए पता ब्लॉकचेन पर मौजूद नहीं है।',
            'no_available' => 'इस राशि के लिए अभी कोई उपलब्ध पता नहीं है।',
            'invalid_balance' => 'बैलेंस एक गैर-नकारात्मक दशमलव स्ट्रिंग होना चाहिए।',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'अधिकतम राशि :amount USDT से अधिक नहीं हो सकती।',
    ],
    'blockchain' => [
        'only_tron' => 'इस ऑपरेशन के लिए केवल TRON नेटवर्क समर्थित है।',
        'only_usdt' => 'इस ऑपरेशन के लिए केवल USDT (TRC20) समर्थित है।',
        'malformed_trongrid_data' => 'TronGrid प्रतिक्रिया गलत है: data एक array नहीं है।',
        'malformed_trongrid_events' => 'TronGrid events प्रतिक्रिया गलत है: data एक array नहीं है।',
        'trc20_transfer_missing' => 'दिए गए txid के लिए TRC20 Transfer घटना नहीं मिली।',
        'malformed_wallet_nowblock' => 'Tron wallet nowblock प्रतिक्रिया गलत है: object नहीं है।',
        'malformed_trongrid_account' => 'TronGrid account प्रतिक्रिया गलत है: object नहीं है।',
        'account_not_found' => 'अकाउंट डेटा नहीं मिला।',
        'malformed_trongrid_trc20' => 'TronGrid account प्रतिक्रिया गलत है: trc20 एक array नहीं है।',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'कोटा समाप्त हो गया।',
        'request_failed' => 'अनुरोध स्थिति :status के साथ विफल हुआ।',
    ],
    'api' => [
        'callback_failed' => 'कॉलбек अनुरोध स्थिति :status के साथ विफल हुआ।',
    ],
    'money' => [
        'empty_string' => 'Money स्ट्रिंग खाली है।',
        'currency_mismatch' => 'मुद्राएं मेल खानी चाहिए।',
    ],
];

