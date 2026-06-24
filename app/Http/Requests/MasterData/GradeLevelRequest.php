<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class GradeLevelRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:100',
            'order' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama tingkat wajib diisi.',
            'order.required' => 'Urutan wajib diisi.',
        ];
    }
}
