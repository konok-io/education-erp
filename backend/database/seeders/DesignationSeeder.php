<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            ['name' => 'Principal', 'code' => 'PRINCIPAL', 'level' => 'executive', 'sort_order' => 1],
            ['name' => 'Vice Principal', 'code' => 'VP', 'level' => 'executive', 'sort_order' => 2],
            ['name' => 'Head Of Department', 'code' => 'HOD', 'level' => 'management', 'sort_order' => 3],
            ['name' => 'Professor', 'code' => 'PROF', 'level' => 'faculty', 'sort_order' => 4],
            ['name' => 'Associate Professor', 'code' => 'ASSOC_PROF', 'level' => 'faculty', 'sort_order' => 5],
            ['name' => 'Assistant Professor', 'code' => 'ASST_PROF', 'level' => 'faculty', 'sort_order' => 6],
            ['name' => 'Lecturer', 'code' => 'LECTURER', 'level' => 'faculty', 'sort_order' => 7],
            ['name' => 'Teacher', 'code' => 'TEACHER', 'level' => 'faculty', 'sort_order' => 8],
            ['name' => 'Accountant', 'code' => 'ACCOUNTANT', 'level' => 'staff', 'sort_order' => 9],
            ['name' => 'Senior Officer', 'code' => 'SR_OFFICER', 'level' => 'staff', 'sort_order' => 10],
            ['name' => 'Officer', 'code' => 'OFFICER', 'level' => 'staff', 'sort_order' => 11],
            ['name' => 'Office Assistant', 'code' => 'OFFICE_ASST', 'level' => 'staff', 'sort_order' => 12],
            ['name' => 'Librarian', 'code' => 'LIBRARIAN', 'level' => 'staff', 'sort_order' => 13],
            ['name' => 'Driver', 'code' => 'DRIVER', 'level' => 'support', 'sort_order' => 14],
            ['name' => 'Cleaner', 'code' => 'CLEANER', 'level' => 'support', 'sort_order' => 15],
            ['name' => 'Security Guard', 'code' => 'SECURITY', 'level' => 'support', 'sort_order' => 16],
        ];

        foreach ($designations as $designation) {
            DB::table('designations')->updateOrInsert(
                ['code' => $designation['code']],
                [
                    'uuid' => Str::uuid()->toString(),
                    'name' => $designation['name'],
                    'name_bn' => null,
                    'level' => $designation['level'],
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $designation['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
