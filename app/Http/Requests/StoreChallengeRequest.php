<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChallengeRequest extends FormRequest
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
            'title.en' => 'required|string',
            'title.ar' => 'required|string',
            'description.en' => 'required|string',
            'description.ar' => 'required|string',
            'points' => 'required|integer',
            'number_of_books' => 'required|integer',
            'duration' => 'required|integer',
            'category_id' => 'required|integer|exists:categories,id',
            'size_category_id' => 'required|integer|exists:size_categories,id',
            'ids_books' => 'sometimes|array',
            'ids_books.*' => 'integer|exists:books,id'
        ];
    }
}
