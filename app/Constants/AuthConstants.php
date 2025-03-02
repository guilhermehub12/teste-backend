<?php

declare(strict_types = 1);

namespace App\Constants;

class AuthConstants
{
    public const TOKEN_NAME = 'SoffiaAuthApp';

    public const MESSAGES = [
        'LOGIN_SUCCESS'       => 'Login realizado com sucesso',
        'LOGIN_FAILED'        => 'Credenciais inválidas',
        'REGISTER_SUCCESS'    => 'Usuário registrado com sucesso',
        'INVALID_CREDENTIALS' => 'Credenciais inválidas. Verifique seu email e senha.',
        'LIST_SUCCESS'        => 'Lista de usuários recuperada com sucesso',
        'USER_FOUND'          => 'Usuário encontrado',
        'USER_NOT_FOUND'      => 'Usuário não encontrado',
        'USER_UPDATED'        => 'Usuário atualizado com sucesso',
        'USER_UPDATED_FAILED' => 'Usuário não pôde ser atualizado, tente novamente.',
        'USER_DELETED'        => 'Usuário deletado com sucesso',
        'PROFILE_FOUND'       => 'Perfil encontrado',
        'PROFILE_FAILED'      => 'Perfil não encontrado',
        'LOGOUT_SUCCESS'      => 'Logout realizado com sucesso',
        'LOGOUT_FAILED'       => 'Logout falhou',
    ];
}
