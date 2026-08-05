<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate permissions table
        DB::table('permissions')->truncate();

        // Define permissions by module
        $permissions = [
            // Users & Roles
            ['name' => 'users.view', 'group' => 'users'],
            ['name' => 'users.create', 'group' => 'users'],
            ['name' => 'users.edit', 'group' => 'users'],
            ['name' => 'users.delete', 'group' => 'users'],
            
            // Students
            ['name' => 'students.view', 'group' => 'students'],
            ['name' => 'students.create', 'group' => 'students'],
            ['name' => 'students.edit', 'group' => 'students'],
            ['name' => 'students.delete', 'group' => 'students'],
            ['name' => 'students.import', 'group' => 'students'],
            ['name' => 'students.export', 'group' => 'students'],
            
            // Teachers
            ['name' => 'teachers.view', 'group' => 'teachers'],
            ['name' => 'teachers.create', 'group' => 'teachers'],
            ['name' => 'teachers.edit', 'group' => 'teachers'],
            ['name' => 'teachers.delete', 'group' => 'teachers'],
            
            // Staff
            ['name' => 'staffs.view', 'group' => 'staffs'],
            ['name' => 'staffs.create', 'group' => 'staffs'],
            ['name' => 'staffs.edit', 'group' => 'staffs'],
            ['name' => 'staffs.delete', 'group' => 'staffs'],
            
            // Academic
            ['name' => 'academics.view', 'group' => 'academics'],
            ['name' => 'academics.create', 'group' => 'academics'],
            ['name' => 'academics.edit', 'group' => 'academics'],
            ['name' => 'academics.delete', 'group' => 'academics'],
            
            // Attendance
            ['name' => 'attendance.view', 'group' => 'attendance'],
            ['name' => 'attendance.create', 'group' => 'attendance'],
            ['name' => 'attendance.edit', 'group' => 'attendance'],
            ['name' => 'attendance.delete', 'group' => 'attendance'],
            
            // Results
            ['name' => 'results.view', 'group' => 'results'],
            ['name' => 'results.create', 'group' => 'results'],
            ['name' => 'results.edit', 'group' => 'results'],
            ['name' => 'results.delete', 'group' => 'results'],
            ['name' => 'results.publish', 'group' => 'results'],
            
            // Routine
            ['name' => 'routines.view', 'group' => 'routines'],
            ['name' => 'routines.create', 'group' => 'routines'],
            ['name' => 'routines.edit', 'group' => 'routines'],
            ['name' => 'routines.delete', 'group' => 'routines'],
            
            // Fees
            ['name' => 'fees.view', 'group' => 'fees'],
            ['name' => 'fees.create', 'group' => 'fees'],
            ['name' => 'fees.edit', 'group' => 'fees'],
            ['name' => 'fees.delete', 'group' => 'fees'],
            ['name' => 'fees.collect', 'group' => 'fees'],
            
            // Library
            ['name' => 'library.view', 'group' => 'library'],
            ['name' => 'library.create', 'group' => 'library'],
            ['name' => 'library.edit', 'group' => 'library'],
            ['name' => 'library.delete', 'group' => 'library'],
            ['name' => 'library.issue', 'group' => 'library'],
            
            // Hostel
            ['name' => 'hostel.view', 'group' => 'hostel'],
            ['name' => 'hostel.create', 'group' => 'hostel'],
            ['name' => 'hostel.edit', 'group' => 'hostel'],
            ['name' => 'hostel.delete', 'group' => 'hostel'],
            
            // Transport
            ['name' => 'transport.view', 'group' => 'transport'],
            ['name' => 'transport.create', 'group' => 'transport'],
            ['name' => 'transport.edit', 'group' => 'transport'],
            ['name' => 'transport.delete', 'group' => 'transport'],
            
            // CMS
            ['name' => 'cms.view', 'group' => 'cms'],
            ['name' => 'cms.create', 'group' => 'cms'],
            ['name' => 'cms.edit', 'group' => 'cms'],
            ['name' => 'cms.delete', 'group' => 'cms'],
            
            // Reports
            ['name' => 'reports.view', 'group' => 'reports'],
            ['name' => 'reports.export', 'group' => 'reports'],
            
            // Settings
            ['name' => 'settings.view', 'group' => 'settings'],
            ['name' => 'settings.edit', 'group' => 'settings'],
        ];

        $now = now();
        $permissions = array_map(function ($permission) use ($now) {
            return [
                'uuid' => generate_uuid(),
                'name' => $permission['name'],
                'guard_name' => 'api',
                'group' => $permission['group'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $permissions);

        // Insert permissions
        DB::table('permissions')->insert($permissions);

        $this->command->info('Permissions seeded successfully!');
    }
}
