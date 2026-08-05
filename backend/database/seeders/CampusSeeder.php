<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campuses = [
            [
                'uuid' => generate_uuid(),
                'name' => 'Main Campus',
                'code' => 'MAIN',
                'address' => '123 Education Street, Dhaka',
                'phone' => '+880 1XXX-XXXXXX',
                'email' => 'info@educationerp.com',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('campuses')->insert($campuses);

        $this->command->info('Campus seeded successfully!');
    }
}
