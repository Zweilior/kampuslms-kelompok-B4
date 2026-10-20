<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File tugas wajib diunggah.',
            'file.file' => 'Unggahan harus berupa file yang valid.',
            'file.mimes' => 'Format file yang diperbolehkan hanya PDF, DOC, atau DOCX.',
            'file.max' => 'Ukuran file maksimal adalah 5 MB.',
            'note.max' => 'Catatan maksimal terdiri dari 1000 karakter.',
        ];
    }
}