<?php

namespace App\Http\Requests\Api\User;

use Faker\Provider\Base;

class StoreUserRequest extends BaseUserRequest
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
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'telefone' => 'required|min:10|max:20',
            'is_admin' => 'boolean',
            'is_valid' => 'boolean',
        ];
    }
}
