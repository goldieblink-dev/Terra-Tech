<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCompanyProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:250'],
            'description' => ['nullable', 'string', 'max:2000'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:1024'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'logo.max' => 'Ukuran file logo tidak boleh melebihi 2MB (2048 KB).',
            'logo.mimes' => 'Format logo harus berupa file gambar png, jpg, jpeg, webp, atau svg.',
            'favicon.max' => 'Ukuran file favicon tidak boleh melebihi 1MB (1024 KB).',
            'favicon.mimes' => 'Format favicon harus berupa file png, jpg, jpeg, webp, svg, atau ico.',
        ];
    }
}
