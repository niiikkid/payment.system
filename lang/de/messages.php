<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Nicht autorisiert.',
    ],
    'impersonation' => [
        'forbidden' => 'Sie haben keine Berechtigung, einen anderen Benutzer zu imitieren.',
        'self' => 'Sie können Ihr eigenes Konto nicht imitieren.',
        'admin_forbidden' => 'Sie können keinen Administrator imitieren.',
        'logged_in_as' => 'Sie sind jetzt angemeldet als :email',
        'session_inactive' => 'Impersonation-Modus ist nicht aktiv.',
        'session_missing' => 'Originalsitzung nicht gefunden, bitte melden Sie sich erneut an.',
        'returned_to_admin' => 'Sie sind zum Adminkonto zurückgekehrt.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrator',
            'admin_description' => 'Administrator',
            'user' => 'Benutzer',
            'user_description' => 'Standardbenutzer',
        ],
        'created' => 'Benutzer erstellt',
        'updated' => 'Benutzer aktualisiert',
        'cannot_change_own_role' => 'Sie können Ihre eigene Rolle nicht ändern.',
    ],
    'invoices' => [
        'created' => 'Rechnung erstellt',
        'updated' => 'Rechnung aktualisiert',
        'create_failed' => 'Rechnung konnte nicht erstellt werden.',
        'update_failed' => 'Rechnung konnte nicht aktualisiert werden.',
        'already_finalized' => 'Rechnung ist bereits finalisiert.',
        'already_expired' => 'Rechnung ist bereits abgelaufen.',
        'amount_below_min' => 'Der Betrag liegt unter dem erlaubten Minimum.',
        'amount_above_max' => 'Der Betrag überschreitet das erlaubte Maximum.',
    ],
    'addresses' => [
        'added' => 'Adresse hinzugefügt',
        'updated' => 'Adresse aktualisiert',
        'add_failed' => 'Adresse konnte nicht hinzugefügt werden.',
        'errors' => [
            'not_found_blockchain' => 'Die angegebene Adresse wurde in der Blockchain nicht gefunden.',
            'duplicate' => 'Die Adresse existiert bereits für das gewählte Netzwerk und die Währung.',
            'unsupported_currency' => 'Nicht unterstützte Währung.',
            'unsupported_currency_value' => 'Nicht unterstützte Währung: :currency',
            'unsupported_network' => 'Nicht unterstütztes Netzwerk.',
            'unsupported_network_value' => 'Nicht unterstütztes Netzwerk: :network',
            'currency_mismatch' => 'Diese Währung ist im ausgewählten Netzwerk nicht verfügbar.',
            'currency_mismatch_value' => 'Währung :currency ist im Netzwerk :network nicht verfügbar.',
            'not_owner' => 'Adresse gehört nicht dem aktuellen Benutzer.',
            'not_exist_blockchain' => 'Adresse existiert für die angegebene Währung/das Netzwerk nicht in der Blockchain.',
            'no_available' => 'Derzeit keine verfügbare Adresse für den angegebenen Betrag.',
            'invalid_balance' => 'Der Kontostand muss eine nicht negative Dezimalzeichenkette sein.',
        ],
    ],
    'merchants' => [
        'created' => 'Händler erstellt',
        'updated' => 'Händler aktualisiert',
        'create_failed' => 'Händler konnte nicht erstellt werden.',
        'update_failed' => 'Händler konnte nicht aktualisiert werden.',
        'errors' => [
            'logo_invalid' => 'Logodatei kann nicht gelesen werden.',
            'logo_not_square' => 'Das Logo muss quadratisch sein.',
            'logo_too_large' => 'Das Logo darf 500x500 Pixel nicht überschreiten.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Der Höchstbetrag darf :amount USDT nicht überschreiten.',
    ],
    'blockchain' => [
        'only_tron' => 'Für diese Operation wird nur das TRON-Netzwerk unterstützt.',
        'only_usdt' => 'Für diese Operation wird nur USDT (TRC20) unterstützt.',
        'malformed_trongrid_data' => 'Fehlerhafte TronGrid-Antwort: data ist kein Array.',
        'malformed_trongrid_events' => 'Fehlerhafte TronGrid-Events-Antwort: data ist kein Array.',
        'trc20_transfer_missing' => 'TRC20-Transfer-Ereignis für die angegebene TXID nicht gefunden.',
        'malformed_wallet_nowblock' => 'Fehlerhafte Tron-Wallet-nowblock-Antwort: kein Objekt.',
        'malformed_trongrid_account' => 'Fehlerhafte TronGrid-Account-Antwort: kein Objekt.',
        'account_not_found' => 'Kontodaten nicht gefunden.',
        'malformed_trongrid_trc20' => 'Fehlerhafte TronGrid-Account-Antwort: trc20 ist kein Array.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Kontingent überschritten.',
        'request_failed' => 'Anfrage fehlgeschlagen mit Status :status.',
    ],
    'api' => [
        'callback_failed' => 'Callback-Anfrage fehlgeschlagen mit Status :status.',
    ],
    'money' => [
        'empty_string' => 'Leerer Geldwert.',
        'currency_mismatch' => 'Währungen müssen übereinstimmen.',
    ],
];

