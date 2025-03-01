<?php

declare(strict_types = 1);

namespace App\Constants;

class AuthConstants
{
    public const TOKEN_NAME = 'SoffiaAuthApp';

    public const MESSAGES = [
        'login_success'       => 'Login realizado com sucesso',
        'login_failed'        => 'Credenciais inválidas',
        'register_success'    => 'Usuário registrado com sucesso',
        'logout_success'      => 'Logout realizado com sucesso',
        'invalid_credentials' => 'Credenciais inválidas. Verifique seu email e senha.',
        'logout_success'      => 'Logout realizado com sucesso',
        'logout_failed'       => 'Logout falhou',
        'list_success'        => 'Lista de usuários recuperada com sucesso',
        'user_not_found'      => 'Usuário não encontrado',
        'user_found'          => 'Usuário encontrado',
        'user_updated'        => 'Usuário atualizado com sucesso',
        'user_updated_failed' => 'Usuário não pôde ser atualizado, tente novamente.',
        'user_deleted'        => 'Usuário deletado com sucesso',
    ];
}
