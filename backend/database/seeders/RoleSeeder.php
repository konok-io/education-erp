<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate the tables if they exist
        Schema::disableForeignKeyConstraints();
        DB::table('role_permission')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        // Define roles
        $roles = [
            [
                'uuid' => generate_uuid(),
                'name' => 'super-admin',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'admin',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'teacher',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'student',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'accountant',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'librarian',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'hostel-warden',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'transport-incharge',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'staff',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert roles
        DB::table('roles')->insert($roles);

        $this->command->info('Roles seeded successfully!');
    }
}
