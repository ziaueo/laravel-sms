<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\SchoolProfile;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $paud = SchoolType::where('name', 'PAUD')->first();
        $sd   = SchoolType::where('name', 'SD')->first();

        // Sekolah 1 — PAUD
        $paudSchool = School::firstOrCreate(
            ['slug' => 'paud-bppi-cisaat'],
            [
                'school_type_id' => $paud->id,
                'name'           => 'PAUD BPPI Cisaat',
                'slug'           => 'paud-bppi-cisaat',
                'npsn'           => null,
                'address'        => 'Kp. Cisaat, Kec. Cicurug, Kab. Sukabumi',
                'phone'          => null,
                'email'          => null,
                'is_active'      => true,
            ]
        );

        SchoolProfile::firstOrCreate(
            ['school_id' => $paudSchool->id],
            [
                'school_id'      => $paudSchool->id,
                'tagline'        => 'Membentuk Generasi Cerdas & Berkarakter',
                'vision'         => 'Menjadi lembaga pendidikan anak usia dini yang unggul',
                'mission'        => 'Memberikan pendidikan terbaik untuk anak usia dini',
            ]
        );

        // Sekolah 2 — SD
        $sdSchool = School::firstOrCreate(
            ['slug' => 'mi-bppi-cisaat'],
            [
                'school_type_id' => $sd->id,
                'name'           => 'MI BPPI Cisaat',
                'slug'           => 'mi-bppi-cisaat',
                'npsn'           => null,
                'address'        => 'Kp. Cisaat, Kec. Cicurug, Kab. Sukabumi',
                'phone'          => null,
                'email'          => null,
                'is_active'      => true,
            ]
        );

        SchoolProfile::firstOrCreate(
            ['school_id' => $sdSchool->id],
            [
                'school_id'      => $sdSchool->id,
                'tagline'        => 'Unggul dalam Ilmu, Mulia dalam Akhlak',
                'vision'         => 'Menjadi madrasah ibtidaiyah yang unggul dan berkarakter islami',
                'mission'        => 'Mendidik siswa yang berilmu, beriman, dan bertakwa',
            ]
        );
    }
}
