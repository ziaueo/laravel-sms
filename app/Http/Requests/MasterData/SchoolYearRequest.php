<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolYearRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $schoolId = active_school()?->id;

        // Ambil ID dengan aman — null saat store, object/int saat update
        $routeParam = $this->route('schoolYear');
        $ignoreId   = is_object($routeParam) ? $routeParam->id : ($routeParam ?: null);

        return [
            'curriculum_id' => 'required|exists:curriculums,id',
            'year'          => [
                'required',
                'string',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('school_years')
                    ->where('school_id', $schoolId)
                    ->where('semester', (int) $this->semester)
                    ->when($ignoreId, fn($rule) => $rule->ignore($ignoreId)),
            ],
            'semester'   => 'required|in:1,2',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'curriculum_id.required' => 'Kurikulum wajib dipilih.',
            'year.required'          => 'Tahun ajaran wajib diisi.',
            'year.regex'             => 'Format tahun ajaran harus: 2024/2025.',
            'year.unique'            => 'Semester ini sudah ada untuk tahun ajaran tersebut.',
            'semester.required'      => 'Semester wajib dipilih.',
            'start_date.required'    => 'Tanggal mulai wajib diisi.',
            'end_date.required'      => 'Tanggal selesai wajib diisi.',
            'end_date.after'         => 'Tanggal selesai harus setelah tanggal mulai.',
        ];
    }
}
