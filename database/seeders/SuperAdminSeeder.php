<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'superadmin@sms.sch.id'],
            [
                'name'                 => 'Super Admin',
                'password'             => Hash::make('password'),
                'is_active'            => true,
                'must_change_password' => false,
                'email_verified_at'    => now(),
            ]
        );

        $user->assignRole('super_admin');
    }
}
