<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_type_id' => 'required|exists:school_types,id',
            'name'           => 'required|string|max:255',
            'npsn'           => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'accreditation'  => 'nullable|string|max:10',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'      => 'nullable|boolean',

            // Profil
            'tagline'         => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'vision'          => 'nullable|string',
            'mission'         => 'nullable|string',
            'history'         => 'nullable|string',
            'principal_name'  => 'nullable|string|max:255',
            'founded_year'    => 'nullable|integer|min:1900|max:' . date('Y'),
            'facebook_url'    => 'nullable|string|max:255',
            'instagram_url'   => 'nullable|string|max:255',
            'youtube_url'     => 'nullable|string|max:255',
            'tiktok_url'      => 'nullable|string|max:255',
            'maps_embed'      => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'school_type_id.required' => 'Tipe sekolah wajib dipilih.',
            'name.required'           => 'Nama sekolah wajib diisi.',
            'logo.image'              => 'File harus berupa gambar.',
            'logo.mimes'              => 'Format logo harus jpg, jpeg, png, atau webp.',
            'logo.max'                => 'Ukuran logo maksimal 2MB.',
        ];
    }
}
