<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'uuid' => generate_uuid(),
                'name' => 'Science',
                'code' => 'SCI',
                'type' => 'academic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'Arts',
                'code' => 'ART',
                'type' => 'academic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'Commerce',
                'code' => 'COM',
                'type' => 'academic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'Administration',
                'code' => 'ADMIN',
                'type' => 'administrative',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => generate_uuid(),
                'name' => 'Finance',
                'code' => 'FIN',
                'type' => 'administrative',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('departments')->insert($departments);

        $this->command->info('Departments seeded successfully!');
    }
}
