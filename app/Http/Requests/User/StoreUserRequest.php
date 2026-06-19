<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Constants\RoleConstant;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'role'        => 'required|integer|in:' . implode(',', array_keys(RoleConstant::getAll())),
            'school_ids'  => 'required|array|min:1',
            'school_ids.*'=> 'exists:schools,id',
            'phone'       => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Nama wajib diisi.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah digunakan.',
            'role.required'       => 'Role wajib dipilih.',
            'school_ids.required' => 'Sekolah wajib dipilih minimal 1.',
            'school_ids.min'      => 'Sekolah wajib dipilih minimal 1.',
        ];
    }
}
