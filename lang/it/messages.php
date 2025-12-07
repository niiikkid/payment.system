<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Non autorizzato.',
    ],
    'impersonation' => [
        'forbidden' => 'Non hai i permessi per impersonare un altro utente.',
        'self' => 'Non puoi impersonare il tuo stesso account.',
        'admin_forbidden' => 'Non puoi impersonare un amministratore.',
        'logged_in_as' => 'Ora sei connesso come :email',
        'session_inactive' => 'La modalità di impersonazione non è attiva.',
        'session_missing' => 'Sessione originale non trovata, accedi di nuovo.',
        'returned_to_admin' => "Sei tornato all'account admin.",
    ],
    'users' => [
        'roles' => [
            'admin' => 'Amministratore',
            'admin_description' => 'Amministratore',
            'user' => 'Utente',
            'user_description' => 'Utente normale',
        ],
        'created' => 'Utente creato',
        'updated' => 'Utente aggiornato',
        'cannot_change_own_role' => 'Non puoi cambiare il tuo ruolo.',
    ],
    'invoices' => [
        'created' => 'Fattura creata',
        'updated' => 'Fattura aggiornata',
        'create_failed' => 'Impossibile creare la fattura.',
        'update_failed' => 'Impossibile aggiornare la fattura.',
        'already_finalized' => 'La fattura è già finalizzata.',
        'already_expired' => 'La fattura è già scaduta.',
        'amount_below_min' => 'L\'importo è inferiore al minimo consentito.',
        'amount_above_max' => 'L\'importo supera il massimo consentito.',
    ],
    'addresses' => [
        'added' => 'Indirizzo aggiunto',
        'updated' => 'Indirizzo aggiornato',
        'add_failed' => 'Impossibile aggiungere l\'indirizzo.',
        'errors' => [
            'not_found_blockchain' => 'L\'indirizzo specificato non è stato trovato sulla blockchain.',
            'duplicate' => 'L\'indirizzo esiste già per la rete e valuta selezionate.',
            'unsupported_currency' => 'Valuta non supportata.',
            'unsupported_currency_value' => 'Valuta non supportata: :currency',
            'unsupported_network' => 'Rete non supportata.',
            'unsupported_network_value' => 'Rete non supportata: :network',
            'currency_mismatch' => 'Questa valuta non è disponibile sulla rete selezionata.',
            'currency_mismatch_value' => 'La valuta :currency non è disponibile sulla rete :network.',
            'not_owner' => 'L\'indirizzo non appartiene all\'utente corrente.',
            'not_exist_blockchain' => 'L\'indirizzo non esiste sulla blockchain per la valuta/rete specificata.',
            'no_available' => 'Nessun indirizzo disponibile per l\'importo indicato in questo momento.',
            'invalid_balance' => 'Il saldo deve essere una stringa decimale non negativa.',
        ],
    ],
    'merchants' => [
        'created' => 'Merchant creato',
        'updated' => 'Merchant aggiornato',
        'create_failed' => 'Impossibile creare il merchant.',
        'update_failed' => 'Impossibile aggiornare il merchant.',
        'errors' => [
            'logo_invalid' => 'Impossibile leggere il file del logo.',
            'logo_not_square' => 'Il logo deve essere quadrato.',
            'logo_too_large' => 'Il logo non deve superare 500x500 pixel.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'L\'importo massimo non può superare :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Per questa operazione è supportata solo la rete TRON.',
        'only_usdt' => 'Per questa operazione è supportato solo USDT (TRC20).',
        'malformed_trongrid_data' => 'Risposta TronGrid non valida: data non è un array.',
        'malformed_trongrid_events' => 'Risposta eventi TronGrid non valida: data non è un array.',
        'trc20_transfer_missing' => 'Evento TRC20 Transfer non trovato per il txid indicato.',
        'malformed_wallet_nowblock' => 'Risposta nowblock del wallet Tron non valida: non è un oggetto.',
        'malformed_trongrid_account' => 'Risposta account TronGrid non valida: non è un oggetto.',
        'account_not_found' => 'Dati account non trovati.',
        'malformed_trongrid_trc20' => 'Risposta account TronGrid non valida: trc20 non è un array.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Quota superata.',
        'request_failed' => 'Richiesta non riuscita con stato :status.',
    ],
    'api' => [
        'callback_failed' => 'Richiesta di callback non riuscita con stato :status.',
    ],
    'money' => [
        'empty_string' => 'Stringa importo vuota.',
        'currency_mismatch' => 'Le valute devono coincidere.',
    ],
];

