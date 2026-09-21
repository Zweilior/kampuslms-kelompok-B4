<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Ganti dengan Policy pada minggu 7.
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'code')->ignore($this->route('course')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sks' => ['required', 'integer', 'between:1,6'],
            'lecturer_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', 'in:draft,active,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode mata kuliah wajib diisi.',
            'code.string' => 'Kode mata kuliah harus berupa teks.',
            'code.max' => 'Kode mata kuliah maksimal 20 karakter.',
            'code.unique' => 'Kode mata kuliah tersebut sudah digunakan oleh mata kuliah lain.',

            'name.required' => 'Nama mata kuliah wajib diisi.',
            'name.string' => 'Nama mata kuliah harus berupa teks.',
            'name.max' => 'Nama mata kuliah maksimal 255 karakter.',

            'description.string' => 'Deskripsi mata kuliah harus berupa teks.',

            'sks.required' => 'Jumlah SKS wajib diisi.',
            'sks.integer' => 'Jumlah SKS harus berupa angka bulat.',
            'sks.between' => 'Jumlah SKS harus berada antara 1 sampai 6.',

            'lecturer_id.required' => 'Dosen pengampu wajib dipilih.',
            'lecturer_id.integer' => 'ID dosen harus berupa angka.',
            'lecturer_id.exists' => 'Dosen yang dipilih tidak ditemukan.',

            'status.required' => 'Status mata kuliah wajib dipilih.',
            'status.in' => 'Status mata kuliah harus berupa draft, active, atau archived.',
        ];
    }
}