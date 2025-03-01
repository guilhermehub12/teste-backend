<?php

declare(strict_types = 1);

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class BaseUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'Nome',
            'email'    => 'E-mail',
            'password' => 'Senha',
            'telefone' => 'Telefone',
            'is_admin' => 'Administrador',
            'is_valid' => 'Ativo',
        ];
    }
}
