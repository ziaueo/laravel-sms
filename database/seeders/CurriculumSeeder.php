<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curriculum;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $curriculums = [
            [
                'name'        => 'Kurikulum 2013',
                'code'        => 'K13',
                'description' => 'Kurikulum nasional tahun 2013',
                'is_active'   => true,
            ],
            [
                'name'        => 'Kurikulum Merdeka',
                'code'        => 'KURMER',
                'description' => 'Kurikulum Merdeka Belajar',
                'is_active'   => true,
            ],
            [
                'name'        => 'KTSP',
                'code'        => 'KTSP',
                'description' => 'Kurikulum Tingkat Satuan Pendidikan',
                'is_active'   => true,
            ],
        ];

        foreach ($curriculums as $curriculum) {
            Curriculum::firstOrCreate(['code' => $curriculum['code']], $curriculum);
        }
    }
}
