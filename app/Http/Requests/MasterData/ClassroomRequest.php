<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class ClassroomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'school_year_id'      => 'required|exists:school_years,id',
            'grade_level_id'      => 'required|exists:grade_levels,id',
            'name'                => 'required|string|max:100',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
            'major_id'            => 'nullable|exists:majors,id',
            'capacity'            => 'required|integer|min:1|max:100',
            'is_active'           => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'school_year_id.required'  => 'Tahun ajaran wajib dipilih.',
            'grade_level_id.required'  => 'Tingkat kelas wajib dipilih.',
            'name.required'            => 'Nama kelas wajib diisi.',
            'capacity.required'        => 'Kapasitas wajib diisi.',
        ];
    }
}
