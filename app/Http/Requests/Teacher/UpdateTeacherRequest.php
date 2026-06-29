<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'          => 'required|string|max:255',
            'gender'             => 'required|integer|in:1,2',
            'nip'                => 'nullable|string|max:30',
            'birth_place'        => 'nullable|string|max:100',
            'birth_date'         => 'nullable|date',
            'religion'           => 'nullable|string|max:50',
            'address'            => 'nullable|string',
            'phone'              => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'join_date'          => 'nullable|date',
            'employment_status'  => 'nullable|integer|in:1,2,3,4',
            'position_id'        => 'nullable|exists:positions,id',
            'last_education'     => 'nullable|string|max:20',
            'major'              => 'nullable|string|max:100',
            'photo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'          => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required'    => 'Jenis kelamin wajib dipilih.',
            'photo.image'        => 'File harus berupa gambar.',
            'photo.mimes'        => 'Format foto harus jpg, jpeg, png, atau webp.',
            'photo.max'          => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
