<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowedMimetypes = implode(',', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar',
            'application/vnd.rar',
            'application/x-rar-compressed',
            'application/octet-stream',
        ]);

        return [
            'category_id' => ['required', 'exists:file_categories,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'in:draft,published'],
            'file'        => [
                'nullable',
                'file',
                'max:20480', // 20 MB max
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar',
                'mimetypes:' . $allowedMimetypes,
                function ($attribute, $value, $fail) {
                    if ($value && $value->isValid()) {
                        $extension = strtolower($value->getClientOriginalExtension());
                        $forbiddenExtensions = ['exe', 'bat', 'cmd', 'sh', 'php', 'js', 'html', 'htm', 'phtml', 'cgi', 'pl', 'py'];
                        if (in_array($extension, $forbiddenExtensions, true)) {
                            $fail('Tipe file eksekusi dan skrip tidak diizinkan untuk diunggah.');
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes'     => 'Format file harus berupa: pdf, doc, docx, xls, xlsx, ppt, pptx, zip, atau rar.',
            'file.mimetypes' => 'Tipe MIME file tidak diizinkan untuk diunggah.',
            'file.max'       => 'Ukuran file tidak boleh melebihi 20 MB.',
        ];
    }
}
