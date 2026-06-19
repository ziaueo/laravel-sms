<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolType;

class SchoolTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'PAUD',  'order' => 1, 'is_active' => true],
            ['name' => 'TK',    'order' => 2, 'is_active' => true],
            ['name' => 'SD',    'order' => 3, 'is_active' => true],
            ['name' => 'SMP',   'order' => 4, 'is_active' => true],
            ['name' => 'SMA',   'order' => 5, 'is_active' => true],
            ['name' => 'SMK',   'order' => 6, 'is_active' => true],
        ];

        foreach ($types as $type) {
            SchoolType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
