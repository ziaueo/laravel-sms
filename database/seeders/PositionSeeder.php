<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Constants\PositionConstant;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Guru Kelas',        'type' => PositionConstant::GURU],
            ['name' => 'Guru Mata Pelajaran','type' => PositionConstant::GURU],
            ['name' => 'Guru BK',           'type' => PositionConstant::GURU],
            ['name' => 'Kepala Sekolah',     'type' => PositionConstant::STAFF],
            ['name' => 'Wakil Kepala Sekolah','type' => PositionConstant::STAFF],
            ['name' => 'Staff TU',           'type' => PositionConstant::STAFF],
            ['name' => 'Bendahara',          'type' => PositionConstant::STAFF],
            ['name' => 'Pustakawan',         'type' => PositionConstant::STAFF],
            ['name' => 'Satpam',             'type' => PositionConstant::STAFF],
            ['name' => 'Penjaga Sekolah',    'type' => PositionConstant::STAFF],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(
                ['name' => $position['name']],
                $position
            );
        }
    }
}
