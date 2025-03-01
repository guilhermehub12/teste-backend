<?php

namespace App\Constants;

class AuthConstants
{
    public const TOKEN_NAME = 'SoffiaAuthApp';
    
    public const MESSAGES = [
        'login_success' => 'Login realizado com sucesso',
        'login_failed' => 'Credenciais inválidas',
        'register_success' => 'Usuário registrado com sucesso',
        'logout_success' => 'Logout realizado com sucesso',
        'invalid_credentials' => 'Credenciais inválidas. Verifique seu email e senha.',
        'logout_success' => 'Logout realizado com sucesso',
        'logout_failed' => 'Logout falhou',
    ];
}
