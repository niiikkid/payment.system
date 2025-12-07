<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Brak autoryzacji.',
    ],
    'impersonation' => [
        'forbidden' => 'Nie masz uprawnień do podszywania się pod innego użytkownika.',
        'self' => 'Nie możesz podszyć się pod własne konto.',
        'admin_forbidden' => 'Nie możesz podszywać się pod administratora.',
        'logged_in_as' => 'Zalogowano jako :email',
        'session_inactive' => 'Tryb podszywania jest nieaktywny.',
        'session_missing' => 'Nie znaleziono oryginalnej sesji, zaloguj się ponownie.',
        'returned_to_admin' => 'Powróciłeś do konta administratora.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrator',
            'admin_description' => 'Administrator',
            'user' => 'Użytkownik',
            'user_description' => 'Zwykły użytkownik',
        ],
        'created' => 'Użytkownik utworzony',
        'updated' => 'Użytkownik zaktualizowany',
        'cannot_change_own_role' => 'Nie możesz zmienić własnej roli.',
    ],
    'invoices' => [
        'created' => 'Faktura utworzona',
        'updated' => 'Faktura zaktualizowana',
        'create_failed' => 'Nie udało się utworzyć faktury.',
        'update_failed' => 'Nie udało się zaktualizować faktury.',
        'already_finalized' => 'Faktura jest już sfinalizowana.',
        'already_expired' => 'Faktura już wygasła.',
        'amount_below_min' => 'Kwota jest poniżej dozwolonego minimum.',
        'amount_above_max' => 'Kwota przekracza dozwolone maksimum.',
    ],
    'addresses' => [
        'added' => 'Adres dodany',
        'updated' => 'Adres zaktualizowany',
        'add_failed' => 'Nie udało się dodać adresu.',
        'errors' => [
            'not_found_blockchain' => 'Podany adres nie został znaleziony w blockchainie.',
            'duplicate' => 'Adres już istnieje dla wybranej sieci i waluty.',
            'unsupported_currency' => 'Nieobsługiwana waluta.',
            'unsupported_currency_value' => 'Nieobsługiwana waluta: :currency',
            'unsupported_network' => 'Nieobsługiwana sieć.',
            'unsupported_network_value' => 'Nieobsługiwana sieć: :network',
            'currency_mismatch' => 'Ta waluta nie jest dostępna w wybranej sieci.',
            'currency_mismatch_value' => 'Waluta :currency nie jest dostępna w sieci :network.',
            'not_owner' => 'Adres nie należy do bieżącego użytkownika.',
            'not_exist_blockchain' => 'Adres nie istnieje w blockchainie dla podanej waluty/sieci.',
            'no_available' => 'Brak dostępnego adresu dla podanej kwoty w tej chwili.',
            'invalid_balance' => 'Saldo musi być nieujemnym łańcuchem dziesiętnym.',
        ],
    ],
    'merchants' => [
        'created' => 'Merchant utworzony',
        'updated' => 'Merchant zaktualizowany',
        'create_failed' => 'Nie udało się utworzyć merchanta.',
        'update_failed' => 'Nie udało się zaktualizować merchanta.',
        'errors' => [
            'logo_invalid' => 'Nie można odczytać pliku logo.',
            'logo_not_square' => 'Logo musi być kwadratowe.',
            'logo_too_large' => 'Logo nie może przekraczać 500x500 pikseli.',
            'not_owner' => 'Merchant nie należy do bieżącego użytkownika.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Maksymalna kwota nie może przekraczać :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Dla tej operacji obsługiwana jest tylko sieć TRON.',
        'only_usdt' => 'Dla tej operacji obsługiwany jest wyłącznie USDT (TRC20).',
        'malformed_trongrid_data' => 'Nieprawidłowa odpowiedź TronGrid: data nie jest tablicą.',
        'malformed_trongrid_events' => 'Nieprawidłowa odpowiedź TronGrid events: data nie jest tablicą.',
        'trc20_transfer_missing' => 'Nie znaleziono zdarzenia TRC20 Transfer dla podanego txid.',
        'malformed_wallet_nowblock' => 'Nieprawidłowa odpowiedź portfela Tron nowblock: nie jest obiektem.',
        'malformed_trongrid_account' => 'Nieprawidłowa odpowiedź konta TronGrid: nie jest obiektem.',
        'account_not_found' => 'Dane konta nie znalezione.',
        'malformed_trongrid_trc20' => 'Nieprawidłowa odpowiedź konta TronGrid: trc20 nie jest tablicą.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Limit zapytań został przekroczony.',
        'request_failed' => 'Żądanie zakończyło się statusem :status.',
    ],
    'api' => [
        'callback_failed' => 'Żądanie callback zwróciło status :status.',
    ],
    'money' => [
        'empty_string' => 'Pusta wartość kwoty.',
        'currency_mismatch' => 'Waluty muszą być zgodne.',
    ],
];


