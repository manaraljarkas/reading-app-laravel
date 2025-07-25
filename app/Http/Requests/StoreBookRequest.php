<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'author_id' => 'required|integer|exists:authors,id',
            'description.en' => 'required|string',
            'description.ar' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'publish_date' => 'required|date',
            'number_of_pages' => 'integer|required',
            'size_category_id' => 'integer|required|exists:size_categories,id',
            'summary.en' => 'sometimes|string',
            'summary.ar' => 'sometimes|string',
            'book_file' => 'required|file',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'challenge_duration' => 'required|integer',
            'challenge_points' => 'required|integer',
            'description_BookChallenge.en' => 'required|string',
            'description_BookChallenge.ar' => 'required|string',
        ];
    }
}
