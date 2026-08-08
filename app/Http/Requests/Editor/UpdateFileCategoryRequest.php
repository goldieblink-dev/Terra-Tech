<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFileCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('file_category') ? $this->route('file_category')->id : null;

        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('file_categories', 'name')->ignore($categoryId)],
            'description' => ['nullable', 'string'],
        ];
    }
}
