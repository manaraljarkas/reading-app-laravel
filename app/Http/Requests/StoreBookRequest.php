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
            'number_of_pages' => 'required|integer',
            'size_category_id' => 'required|integer|exists:size_categories,id',
            'summary.en' => 'sometimes|string',
            'summary.ar' => 'sometimes|string',
            'book_file' => 'required|file',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'points' => 'required|integer'
        ];
    }
}
