<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = hashid_decode($this->route('user'), \App\Models\User::class);

        return [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $userId,
            'school_ids'   => 'required|array|min:1',
            'school_ids.*' => 'exists:schools,id',
            'phone'        => 'nullable|string|max:20',
            'is_active'    => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Nama wajib diisi.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah digunakan.',
            'school_ids.required' => 'Sekolah wajib dipilih minimal 1.',
            'school_ids.min'      => 'Sekolah wajib dipilih minimal 1.',
        ];
    }
}
