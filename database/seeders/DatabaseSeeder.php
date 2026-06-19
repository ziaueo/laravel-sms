<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SchoolTypeSeeder::class,
            CurriculumSeeder::class,
            PositionSeeder::class,
            SuperAdminSeeder::class,
            SchoolSeeder::class,
        ]);
    }
}
