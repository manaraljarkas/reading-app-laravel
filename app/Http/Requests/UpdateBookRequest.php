<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
        'title.en'=>'sometimes|string',
        'title.ar'=>'sometimes|string',
        'description.en'=>'sometimes|string',
        'description.ar'=>'sometimes|string',
        'publish_date'=>'sometimes|date',
        'number_of_pages'=>'sometimes|integer',
        'summary.en'=>'sometimes|string',
        'summary.ar'=>'sometimes|string',
        'category_id'=>'sometimes|integer|exists:categories,id',
        'size_category_id'=>'sometimes|integer|exists:size_categories,id',
        'author_id'=>'sometimes|integer|exists:authors,id',
        'book_pdf'=>'sometimes|file',
        'cover_image'=>'sometimes|image|mimes:jpg,jpeg,png,gif,webp|max:2048'

        ];
    }
}
