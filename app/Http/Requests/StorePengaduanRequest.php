<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengaduanRequest extends FormRequest
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
            'nama_pengirim' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'kategori' => 'required|in:saran,kritik,keluhan,pertanyaan,laporan,informasi, lainnya',
            'subjek' => 'nullable|string|max:255',
            'pesan' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pengirim.required' => 'Nama pengirim wajib diisi',
            'email.email' => 'Format email tidak valid',
            'kategori.required' => 'Kategori pesan wajib dipilih',
            'kategori.in' => 'Kategori yang dipilih tidak valid',
            'pesan.required' => 'Pesan wajib diisi',
            'pesan.min' => 'Pesan minimal 10 karakter',
        ];
    }
}
