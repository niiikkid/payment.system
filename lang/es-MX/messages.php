<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'No autorizado.',
    ],
    'impersonation' => [
        'forbidden' => 'No tienes permiso para suplantar a otro usuario.',
        'self' => 'No puedes suplantar tu propia cuenta.',
        'admin_forbidden' => 'No puedes suplantar a un administrador.',
        'logged_in_as' => 'Ahora has iniciado sesión como :email',
        'session_inactive' => 'El modo de suplantación no está activo.',
        'session_missing' => 'Sesión original no encontrada, inicia sesión de nuevo.',
        'returned_to_admin' => 'Has vuelto a la cuenta de administrador.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrador',
            'admin_description' => 'Administrador',
            'user' => 'Usuario',
            'user_description' => 'Usuario habitual',
        ],
        'created' => 'Usuario creado',
        'updated' => 'Usuario actualizado',
        'cannot_change_own_role' => 'No puedes cambiar tu propio rol.',
    ],
    'invoices' => [
        'created' => 'Factura creada',
        'updated' => 'Factura actualizada',
        'create_failed' => 'No se pudo crear la factura.',
        'update_failed' => 'No se pudo actualizar la factura.',
        'already_finalized' => 'La factura ya está finalizada.',
        'already_expired' => 'La factura ya está vencida.',
        'amount_below_min' => 'El monto está por debajo del mínimo permitido.',
        'amount_above_max' => 'El monto supera el máximo permitido.',
    ],
    'addresses' => [
        'added' => 'Dirección agregada',
        'updated' => 'Dirección actualizada',
        'add_failed' => 'No se pudo agregar la dirección.',
        'errors' => [
            'not_found_blockchain' => 'La dirección especificada no se encontró en la blockchain.',
            'duplicate' => 'La dirección ya existe para la red y moneda seleccionadas.',
            'unsupported_currency' => 'Moneda no admitida.',
            'unsupported_currency_value' => 'Moneda no admitida: :currency',
            'unsupported_network' => 'Red no admitida.',
            'unsupported_network_value' => 'Red no admitida: :network',
            'currency_mismatch' => 'Esta moneda no está disponible en la red seleccionada.',
            'currency_mismatch_value' => 'La moneda :currency no está disponible en la red :network.',
            'not_owner' => 'La dirección no pertenece al usuario actual.',
            'not_exist_blockchain' => 'La dirección no existe en la blockchain para la moneda/red especificada.',
            'no_available' => 'No hay dirección disponible para el monto indicado ahora mismo.',
            'invalid_balance' => 'El balance debe ser una cadena decimal no negativa.',
        ],
    ],
    'merchants' => [
        'created' => 'Comercio creado',
        'updated' => 'Comercio actualizado',
        'create_failed' => 'No se pudo crear el comercio.',
        'update_failed' => 'No se pudo actualizar el comercio.',
        'errors' => [
            'logo_invalid' => 'No se puede leer el archivo del logo.',
            'logo_not_square' => 'El logo debe ser cuadrado.',
            'logo_too_large' => 'El logo no debe exceder 500x500 píxeles.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'El monto máximo no puede exceder :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Solo se admite la red TRON para esta operación.',
        'only_usdt' => 'Solo se admite USDT (TRC20) para esta operación.',
        'malformed_trongrid_data' => 'Respuesta TronGrid malformada: data no es un array.',
        'malformed_trongrid_events' => 'Respuesta de eventos TronGrid malformada: data no es un array.',
        'trc20_transfer_missing' => 'Evento TRC20 Transfer no encontrado para el txid indicado.',
        'malformed_wallet_nowblock' => 'Respuesta nowblock de Tron wallet malformada: no es un objeto.',
        'malformed_trongrid_account' => 'Respuesta TronGrid de la cuenta malformada: no es un objeto.',
        'account_not_found' => 'Datos de la cuenta no encontrados.',
        'malformed_trongrid_trc20' => 'Respuesta TronGrid de la cuenta malformada: trc20 no es un array.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Cuota excedida.',
        'request_failed' => 'La solicitud falló con el estado :status.',
    ],
    'api' => [
        'callback_failed' => 'Solicitud de callback falló con el estado :status.',
    ],
    'money' => [
        'empty_string' => 'Cadena de dinero vacía.',
        'currency_mismatch' => 'Las monedas deben coincidir.',
    ],
];

