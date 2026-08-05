<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['uuid' => generate_uuid(), 'name' => 'Pre-Primary', 'code' => 'PRE', 'level' => 1],
            ['uuid' => generate_uuid(), 'name' => 'Primary', 'code' => 'PRI', 'level' => 2],
            ['uuid' => generate_uuid(), 'name' => 'Secondary', 'code' => 'SEC', 'level' => 3],
            ['uuid' => generate_uuid(), 'name' => 'Higher Secondary', 'code' => 'HSC', 'level' => 4],
        ];

        $levels = array_map(function ($level) {
            return array_merge($level, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $levels);

        DB::table('academic_levels')->insert($levels);

        $this->command->info('Academic levels seeded successfully!');
    }
}
