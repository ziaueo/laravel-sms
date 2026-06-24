<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class SubjectKkmRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'subject_id'     => 'required|exists:subjects,id',
            'school_year_id' => 'required|exists:school_years,id',
            'kkm_score'      => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required'     => 'Mata pelajaran wajib dipilih.',
            'school_year_id.required' => 'Tahun ajaran wajib dipilih.',
            'kkm_score.required'      => 'Nilai KKM wajib diisi.',
            'kkm_score.min'           => 'KKM minimal 0.',
            'kkm_score.max'           => 'KKM maksimal 100.',
        ];
    }
}
