<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $studentId = hashid_decode($this->route('student'), \App\Models\Student::class);

        return [
            'full_name'      => 'required|string|max:255',
            'gender'         => 'required|integer|in:1,2',
            'nisn'           => ['nullable', 'string', 'max:20', Rule::unique('students', 'nisn')->ignore($studentId)],
            'nis'            => 'nullable|string|max:20',
            'birth_place'    => 'nullable|string|max:100',
            'birth_date'     => 'nullable|date',
            'religion'       => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'phone'          => 'nullable|string|max:20',
            'blood_type'     => 'nullable|string|max:5',
            'entry_year'     => 'nullable|integer|min:1990|max:2100',
            'entry_class_id' => 'nullable|exists:classrooms,id',
            'status'         => 'required|integer|in:1,2,3,4',
            'exit_date'      => 'nullable|date',
            'exit_reason'    => 'nullable|string',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required'    => 'Jenis kelamin wajib dipilih.',
            'nisn.unique'        => 'NISN ini sudah terdaftar.',
            'status.required'    => 'Status siswa wajib dipilih.',
        ];
    }
}
