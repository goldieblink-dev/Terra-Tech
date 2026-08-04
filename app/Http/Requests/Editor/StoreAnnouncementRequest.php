<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor']);
    }

    public function rules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],
            'priority'   => ['required', 'in:normal,important,urgent'],
            'status'     => ['required', 'in:draft,published'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'attachment.mimes' => 'Lampiran harus berformat PDF, DOC, atau DOCX.',
            'attachment.max'   => 'Ukuran berkas lampiran tidak boleh melebihi 5MB.',
        ];
    }
}
