<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChallengeRequest extends FormRequest
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
            'title.en' => 'sometimes|string',
            'title.ar' => 'sometimes|string',
            'description.en' => 'sometimes|string',
            'description.ar' => 'sometimes|string',
            'points' => 'sometimes|integer',
            'duration' => 'sometimes|integer',
            'number_of_books' => 'sometimes|integer',
            'size_category_id' => 'sometimes|exists:size_categories,id',
            'category_id' => 'sometimes|exists:categories,id',
        ];
    }
}
