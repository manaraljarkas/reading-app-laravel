<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'bio'        => 'nullable|string',
            'nickname'   => 'nullable|string|max:255',
            'quote'      => 'nullable|string',
            'picture'    => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }
}
