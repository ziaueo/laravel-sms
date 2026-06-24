<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'grade_level_id' => 'required|exists:grade_levels,id',
            'major_id'       => 'nullable|exists:majors,id',
            'name'           => 'required|string|max:255',
            'code'           => 'nullable|string|max:20',
            'is_active'      => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'grade_level_id.required' => 'Tingkat kelas wajib dipilih.',
            'name.required'           => 'Nama mata pelajaran wajib diisi.',
        ];
    }
}
