<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Constants\PositionConstant;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan diberi jarak supaya jabatan baru bisa disisipkan tanpa menomori ulang.
        $positions = [
            ['name' => 'Kepala Sekolah',       'type' => PositionConstant::PIMPINAN, 'order' => 1],
            ['name' => 'Wakil Kepala Sekolah', 'type' => PositionConstant::PIMPINAN, 'order' => 2],
            ['name' => 'Guru Kelas',           'type' => PositionConstant::GURU,     'order' => 10],
            ['name' => 'Guru Mata Pelajaran',  'type' => PositionConstant::GURU,     'order' => 11],
            ['name' => 'Guru BK',              'type' => PositionConstant::GURU,     'order' => 12],
            ['name' => 'Staff TU',             'type' => PositionConstant::STAFF,    'order' => 20],
            ['name' => 'Bendahara',            'type' => PositionConstant::STAFF,    'order' => 21],
            ['name' => 'Pustakawan',           'type' => PositionConstant::STAFF,    'order' => 22],
            ['name' => 'Satpam',               'type' => PositionConstant::STAFF,    'order' => 23],
            ['name' => 'Penjaga Sekolah',      'type' => PositionConstant::STAFF,    'order' => 24],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['name' => $position['name']],
                $position
            );
        }
    }
}
