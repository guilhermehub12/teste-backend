<?php

declare(strict_types = 1);

namespace App\Http\Requests\Api\Post;

use Illuminate\Foundation\Http\FormRequest;

class BasePostRequest extends FormRequest
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
            'title' => 'Título do post',
            'content' => 'Conteúdo do post',
            'tags' => 'Tags do post',
            'user_id' => 'ID do autor',
        ];
    }
}
