<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmploymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Permanent', 'code' => 'PERMANENT', 'sort_order' => 1],
            ['name' => 'Contract', 'code' => 'CONTRACT', 'sort_order' => 2],
            ['name' => 'Part-Time', 'code' => 'PART_TIME', 'sort_order' => 3],
            ['name' => 'Temporary', 'code' => 'TEMPORARY', 'sort_order' => 4],
            ['name' => 'Guest Faculty', 'code' => 'GUEST_FACULTY', 'sort_order' => 5],
            ['name' => 'Visiting Faculty', 'code' => 'VISITING', 'sort_order' => 6],
            ['name' => 'Intern', 'code' => 'INTERN', 'sort_order' => 7],
        ];

        foreach ($types as $type) {
            DB::table('employment_types')->updateOrInsert(
                ['code' => $type['code']],
                [
                    'uuid' => Str::uuid()->toString(),
                    'name' => $type['name'],
                    'name_bn' => null,
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $type['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
