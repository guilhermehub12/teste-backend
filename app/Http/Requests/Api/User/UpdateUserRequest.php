<?php

declare(strict_types = 1);

namespace App\Http\Requests\Api\User;

class UpdateUserRequest extends BaseUserRequest
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
            'name'     => 'sometimes|required|min:4',
            'email'    => 'sometimes|required|email',
            'password' => 'sometimes|required|min:8',
            'telefone' => 'sometimes|required|min:10|max:20',
            'is_admin' => 'sometimes|boolean',
            'is_valid' => 'sometimes|boolean',
        ];
    }
}
