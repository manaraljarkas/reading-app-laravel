<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Reader;

class ProfileRequest extends FormRequest
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
        $userId = Auth::id();
        $hasProfile = Reader::where('user_id', $userId)->exists();

        return [
            'first_name' => ($hasProfile ? 'sometimes' : 'required') . '|string|max:255',
            'last_name'  => ($hasProfile ? 'sometimes' : 'required') . '|string|max:255',
            'bio'        => 'nullable|string',
            'nickname'   => 'nullable|string|max:255',
            'quote'      => 'nullable|string',
            'picture'    => ($hasProfile ? 'sometimes' : 'nullable') . '|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ];
    }
}
