<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'Não autorizado.',
    ],
    'impersonation' => [
        'forbidden' => 'Você não tem permissão para se passar por outro usuário.',
        'self' => 'Você não pode se passar pela própria conta.',
        'admin_forbidden' => 'Você não pode se passar por um administrador.',
        'logged_in_as' => 'Você agora está logado como :email',
        'session_inactive' => 'O modo de impersonação não está ativo.',
        'session_missing' => 'Sessão original não encontrada, faça login novamente.',
        'returned_to_admin' => 'Você voltou para a conta de admin.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrador',
            'admin_description' => 'Administrador',
            'user' => 'Usuário',
            'user_description' => 'Usuário comum',
        ],
        'created' => 'Usuário criado',
        'updated' => 'Usuário atualizado',
        'cannot_change_own_role' => 'Você não pode alterar seu próprio papel.',
    ],
    'invoices' => [
        'created' => 'Fatura criada',
        'updated' => 'Fatura atualizada',
        'create_failed' => 'Não foi possível criar a fatura.',
        'update_failed' => 'Não foi possível atualizar a fatura.',
        'already_finalized' => 'Fatura já finalizada.',
        'already_expired' => 'Fatura já expirada.',
        'amount_below_min' => 'Valor abaixo do mínimo permitido.',
        'amount_above_max' => 'Valor acima do máximo permitido.',
    ],
    'addresses' => [
        'added' => 'Endereço adicionado',
        'updated' => 'Endereço atualizado',
        'add_failed' => 'Não foi possível adicionar o endereço.',
        'errors' => [
            'not_found_blockchain' => 'O endereço especificado não foi encontrado na blockchain.',
            'duplicate' => 'O endereço já existe para a rede e moeda selecionadas.',
            'unsupported_currency' => 'Moeda não suportada.',
            'unsupported_currency_value' => 'Moeda não suportada: :currency',
            'unsupported_network' => 'Rede não suportada.',
            'unsupported_network_value' => 'Rede não suportada: :network',
            'currency_mismatch' => 'Esta moeda não está disponível na rede selecionada.',
            'currency_mismatch_value' => 'A moeda :currency não está disponível na rede :network.',
            'not_owner' => 'O endereço não pertence ao usuário atual.',
            'not_exist_blockchain' => 'O endereço não existe na blockchain para a moeda/rede especificada.',
            'no_available' => 'Não há endereço disponível para o valor informado no momento.',
            'invalid_balance' => 'O saldo deve ser uma string decimal não negativa.',
        ],
    ],
    'merchants' => [
        'created' => 'Comerciante criado',
        'updated' => 'Comerciante atualizado',
        'create_failed' => 'Não foi possível criar o comerciante.',
        'update_failed' => 'Não foi possível atualizar o comerciante.',
        'errors' => [
            'logo_invalid' => 'Não foi possível ler o arquivo do logotipo.',
            'logo_not_square' => 'O logotipo deve ser quadrado.',
            'logo_too_large' => 'O logotipo não deve exceder 500x500 pixels.',
            'not_owner' => 'O comerciante não pertence ao usuário atual.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'O valor máximo não pode exceder :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'Apenas a rede TRON é suportada para esta operação.',
        'only_usdt' => 'Apenas USDT (TRC20) é suportado para esta operação.',
        'malformed_trongrid_data' => 'Resposta TronGrid malformada: data não é um array.',
        'malformed_trongrid_events' => 'Resposta de eventos TronGrid malformada: data não é um array.',
        'trc20_transfer_missing' => 'Evento TRC20 Transfer não encontrado para o txid fornecido.',
        'malformed_wallet_nowblock' => 'Resposta nowblock da carteira Tron malformada: não é um objeto.',
        'malformed_trongrid_account' => 'Resposta TronGrid de conta malformada: não é um objeto.',
        'account_not_found' => 'Dados da conta não encontrados.',
        'malformed_trongrid_trc20' => 'Resposta TronGrid de conta malformada: trc20 não é um array.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'Cota excedida.',
        'request_failed' => 'Requisição falhou com status :status.',
    ],
    'api' => [
        'callback_failed' => 'Requisição de callback falhou com status :status.',
    ],
    'money' => [
        'empty_string' => 'String de valor vazia.',
        'currency_mismatch' => 'As moedas devem coincidir.',
    ],
];


