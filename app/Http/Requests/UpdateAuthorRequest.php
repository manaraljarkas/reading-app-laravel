<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorRequest extends FormRequest
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
            'name.en' => 'sometimes|string',
            'name.ar' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048',
            'country_id' => 'sometimes|exists:countries,id',
        ];
    }
}
