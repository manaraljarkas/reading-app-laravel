<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'bio'        => 'sometimes|nullable|string',
            'nickname'   => 'sometimes|nullable|string|max:255',
            'quote'      => 'sometimes|nullable|string',
            'picture'    => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }
}
