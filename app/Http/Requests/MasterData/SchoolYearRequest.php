<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class SchoolYearRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $schoolId = active_school()?->id;
        $id = $this->route('school_year');
        $uniqueRule = "unique:school_years,year,{$id},id,school_id,{$schoolId},semester," . $this->semester;

        return [
            'curriculum_id' => 'required|exists:curriculums,id',
            'year'          => ['required', 'regex:/^\d{4}\/\d{4}$/', $uniqueRule],
            'semester'      => 'required|in:1,2',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after:start_date',
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
