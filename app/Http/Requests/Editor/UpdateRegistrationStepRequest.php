<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistrationStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->requirements)) {
            $lines = array_filter(
                array_map('trim', explode("\n", $this->requirements)),
                fn($line) => $line !== ''
            );
            $this->merge([
                'requirements' => array_values($lines),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['required', 'string'],
            'requirements'       => ['nullable', 'array'],
            'requirements.*'     => ['string', 'max:255'],
            'icon'               => ['nullable', 'string', 'max:100'],
            'illustration_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:5120'],
            'sort_order'         => ['nullable', 'integer', 'min:0'],
            'status'             => ['required', 'in:draft,published'],
        ];
    }

    public function messages(): array
    {
        return [
            'illustration_image.mimes' => 'Gambar ilustrasi harus berupa format: png, jpg, jpeg, webp, atau svg.',
            'illustration_image.max'   => 'Ukuran gambar ilustrasi tidak boleh melebihi 5 MB.',
        ];
    }
}
