<?php

declare(strict_types = 1);

namespace App\Http\Requests\Api\Post;

class UpdatePostRequest extends BasePostRequest
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
            'title'   => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string|max:255',
            'tags'    => 'sometimes|required|array|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
        ];
    }
}
