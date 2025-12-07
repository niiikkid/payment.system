<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Non autorisé.',
    ],
    'impersonation' => [
        'forbidden' => "Vous n'avez pas l'autorisation d'emprunter l'identité d'un autre utilisateur.",
        'self' => "Vous ne pouvez pas vous connecter en tant que vous-même.",
        'admin_forbidden' => "Vous ne pouvez pas emprunter l'identité d'un administrateur.",
        'logged_in_as' => "Vous êtes maintenant connecté en tant que :email",
        'session_inactive' => "Le mode d'emprunt d'identité n'est pas actif.",
        'session_missing' => "Session d'origine introuvable, veuillez vous reconnecter.",
        'returned_to_admin' => "Vous êtes revenu sur le compte administrateur.",
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrateur',
            'admin_description' => 'Administrateur',
            'user' => 'Utilisateur',
            'user_description' => 'Utilisateur standard',
        ],
        'created' => 'Utilisateur créé',
        'updated' => 'Utilisateur mis à jour',
        'cannot_change_own_role' => 'Vous ne pouvez pas changer votre propre rôle.',
    ],
    'invoices' => [
        'created' => 'Facture créée',
        'updated' => 'Facture mise à jour',
        'create_failed' => "Échec de la création de la facture.",
        'update_failed' => "Échec de la mise à jour de la facture.",
        'already_finalized' => 'Facture déjà finalisée.',
        'already_expired' => 'Facture déjà expirée.',
        'amount_below_min' => 'Le montant est inférieur au minimum autorisé.',
        'amount_above_max' => 'Le montant dépasse le maximum autorisé.',
    ],
    'addresses' => [
        'added' => 'Adresse ajoutée',
        'updated' => 'Adresse mise à jour',
        'add_failed' => "Échec de l'ajout de l'adresse.",
        'errors' => [
            'not_found_blockchain' => "L'adresse indiquée n'a pas été trouvée sur la blockchain.",
            'duplicate' => "L'adresse existe déjà pour ce réseau et cette devise.",
            'unsupported_currency' => 'Devise non prise en charge.',
            'unsupported_currency_value' => 'Devise non prise en charge : :currency',
            'unsupported_network' => 'Réseau non pris en charge.',
            'unsupported_network_value' => 'Réseau non pris en charge : :network',
            'currency_mismatch' => "Cette devise n'est pas disponible sur le réseau sélectionné.",
            'currency_mismatch_value' => "La devise :currency n'est pas disponible sur le réseau :network.",
            'not_owner' => "L'adresse n'appartient pas à l'utilisateur actuel.",
            'not_exist_blockchain' => "L'adresse n'existe pas sur la blockchain pour cette devise/réseau.",
            'no_available' => "Aucune adresse disponible pour le montant indiqué pour l'instant.",
            'invalid_balance' => 'Le solde doit être une chaîne décimale non négative.',
        ],
    ],
    'merchants' => [
        'created' => 'Commerçant créé',
        'updated' => 'Commerçant mis à jour',
        'create_failed' => 'Échec de la création du commerçant.',
        'update_failed' => 'Échec de la mise à jour du commerçant.',
        'errors' => [
            'logo_invalid' => 'Impossible de lire le fichier du logo.',
            'logo_not_square' => 'Le logo doit être carré.',
            'logo_too_large' => 'Le logo ne doit pas dépasser 500x500 pixels.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'Le montant maximum ne peut pas dépasser :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Seul le réseau TRON est pris en charge pour cette opération.',
        'only_usdt' => "Seul l'USDT (TRC20) est pris en charge pour cette opération.",
        'malformed_trongrid_data' => "Réponse TronGrid mal formée : data n'est pas un tableau.",
        'malformed_trongrid_events' => "Réponse des événements TronGrid mal formée : data n'est pas un tableau.",
        'trc20_transfer_missing' => 'Événement TRC20 Transfer introuvable pour le txid indiqué.',
        'malformed_wallet_nowblock' => "Réponse nowblock du portefeuille Tron mal formée : ce n'est pas un objet.",
        'malformed_trongrid_account' => "Réponse TronGrid account mal formée : ce n'est pas un objet.",
        'account_not_found' => 'Données du compte introuvables.',
        'malformed_trongrid_trc20' => "Réponse TronGrid account mal formée : trc20 n'est pas un tableau.",
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Quota dépassé.',
        'request_failed' => 'La requête a échoué avec le statut :status.',
    ],
    'api' => [
        'callback_failed' => 'La requête de callback a échoué avec le statut :status.',
    ],
    'money' => [
        'empty_string' => 'Chaîne monétaire vide.',
        'currency_mismatch' => 'Les devises doivent correspondre.',
    ],
];

