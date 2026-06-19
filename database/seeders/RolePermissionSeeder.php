<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Buat semua Permission ──────────────────────────
        $permissions = [
            // Users
            'users.view', 'users.create', 'users.edit', 'users.delete',

            // Schools
            'schools.view', 'schools.create', 'schools.edit', 'schools.delete',

            // Students
            'students.view', 'students.create', 'students.edit', 'students.delete',

            // Teachers
            'teachers.view', 'teachers.create', 'teachers.edit', 'teachers.delete',

            // Classrooms
            'classrooms.view', 'classrooms.create', 'classrooms.edit', 'classrooms.delete',

            // Subjects
            'subjects.view', 'subjects.create', 'subjects.edit', 'subjects.delete',

            // Schedules
            'schedules.view', 'schedules.create', 'schedules.edit', 'schedules.delete',

            // Attendances
            'attendances.view', 'attendances.create', 'attendances.edit',

            // Scores
            'scores.view', 'scores.create', 'scores.edit',

            // Report Cards
            'report_cards.view', 'report_cards.publish',

            // PPDB
            'ppdb.view', 'ppdb.create', 'ppdb.approve', 'ppdb.reject',

            // Announcements
            'announcements.view', 'announcements.create',
            'announcements.edit', 'announcements.delete', 'announcements.publish',

            // CMS
            'cms.view', 'cms.create', 'cms.edit', 'cms.delete',

            // Reports
            'reports.view', 'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Buat Roles & assign Permission ────────────────

        // Super Admin — semua permission
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Kepala Sekolah — semua kecuali manage schools global
        $kepalaSekolah = Role::firstOrCreate(['name' => 'kepala_sekolah', 'guard_name' => 'web']);
        $kepalaSekolah->syncPermissions([
            'users.view', 'users.create', 'users.edit',
            'students.view', 'students.create', 'students.edit', 'students.delete',
            'teachers.view', 'teachers.create', 'teachers.edit', 'teachers.delete',
            'classrooms.view', 'classrooms.create', 'classrooms.edit', 'classrooms.delete',
            'subjects.view', 'subjects.create', 'subjects.edit', 'subjects.delete',
            'schedules.view', 'schedules.create', 'schedules.edit', 'schedules.delete',
            'attendances.view', 'attendances.create', 'attendances.edit',
            'scores.view', 'scores.create', 'scores.edit',
            'report_cards.view', 'report_cards.publish',
            'ppdb.view', 'ppdb.create', 'ppdb.approve', 'ppdb.reject',
            'announcements.view', 'announcements.create', 'announcements.edit',
            'announcements.delete', 'announcements.publish',
            'cms.view', 'cms.create', 'cms.edit', 'cms.delete',
            'reports.view', 'reports.export',
        ]);

        // Guru
        $guru = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $guru->syncPermissions([
            'students.view',
            'classrooms.view',
            'subjects.view',
            'schedules.view',
            'attendances.view', 'attendances.create', 'attendances.edit',
            'scores.view', 'scores.create', 'scores.edit',
            'report_cards.view',
            'announcements.view', 'announcements.create',
            'reports.view',
        ]);

        // Staff
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'students.view',
            'teachers.view',
            'classrooms.view',
            'schedules.view',
            'attendances.view',
            'ppdb.view', 'ppdb.create',
            'announcements.view',
            'cms.view', 'cms.create', 'cms.edit',
            'reports.view',
        ]);

        // Siswa
        $siswa = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);
        $siswa->syncPermissions([
            'schedules.view',
            'attendances.view',
            'scores.view',
            'report_cards.view',
            'announcements.view',
        ]);

        // Orang Tua
        $orangTua = Role::firstOrCreate(['name' => 'orang_tua', 'guard_name' => 'web']);
        $orangTua->syncPermissions([
            'attendances.view',
            'scores.view',
            'report_cards.view',
            'announcements.view',
        ]);
    }
}
