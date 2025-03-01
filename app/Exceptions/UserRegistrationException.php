<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Exception;

class UserRegistrationException extends Exception
{
    public static function failedToCreate(): self
    {
        return new self('Erro ao cadastrar o usuário. Verifique os dados fornecidos.', 422);
    }
}
