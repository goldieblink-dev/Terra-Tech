<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateInformationPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor']);
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'category_id'         => ['required', 'integer', 'exists:information_categories,id'],
            'excerpt'             => ['nullable', 'string', 'max:500'],
            'content'             => ['required', 'string'],
            'featured_image'      => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'featured_image_alt'  => ['nullable', 'string', 'max:255'],
            'meta_title'          => ['nullable', 'string', 'max:255'],
            'meta_description'    => ['nullable', 'string', 'max:500'],
            'status'              => ['required', 'in:draft,published'],
        ];
    }

    public function messages(): array
    {
        return [
            'featured_image.mimes' => 'Gambar sampul harus berformat PNG, JPG, JPEG, atau WEBP.',
            'featured_image.max'   => 'Ukuran gambar sampul tidak boleh melebihi 2MB.',
        ];
    }
}
