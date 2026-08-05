<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalaryGradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            [
                'grade_name' => 'Grade 1 - Management',
                'basic_salary' => 150000,
                'house_rent_percent' => 30,
                'medical_percent' => 10,
                'transport_percent' => 10,
                'mobile_allowance' => 5000,
                'special_allowance' => 10000,
                'other_allowance' => 5000,
                'provident_fund_percent' => 10,
                'tax_percent' => 25,
            ],
            [
                'grade_name' => 'Grade 2 - Senior Management',
                'basic_salary' => 120000,
                'house_rent_percent' => 30,
                'medical_percent' => 10,
                'transport_percent' => 10,
                'mobile_allowance' => 4000,
                'special_allowance' => 8000,
                'other_allowance' => 4000,
                'provident_fund_percent' => 10,
                'tax_percent' => 20,
            ],
            [
                'grade_name' => 'Grade 3 - Mid Level',
                'basic_salary' => 80000,
                'house_rent_percent' => 30,
                'medical_percent' => 10,
                'transport_percent' => 10,
                'mobile_allowance' => 3000,
                'special_allowance' => 5000,
                'other_allowance' => 3000,
                'provident_fund_percent' => 10,
                'tax_percent' => 15,
            ],
            [
                'grade_name' => 'Grade 4 - Junior Level',
                'basic_salary' => 50000,
                'house_rent_percent' => 30,
                'medical_percent' => 10,
                'transport_percent' => 10,
                'mobile_allowance' => 2000,
                'special_allowance' => 3000,
                'other_allowance' => 2000,
                'provident_fund_percent' => 10,
                'tax_percent' => 10,
            ],
            [
                'grade_name' => 'Grade 5 - Entry Level',
                'basic_salary' => 30000,
                'house_rent_percent' => 30,
                'medical_percent' => 10,
                'transport_percent' => 10,
                'mobile_allowance' => 1000,
                'special_allowance' => 2000,
                'other_allowance' => 1000,
                'provident_fund_percent' => 10,
                'tax_percent' => 5,
            ],
            [
                'grade_name' => 'Grade 6 - Support Staff',
                'basic_salary' => 20000,
                'house_rent_percent' => 30,
                'medical_percent' => 10,
                'transport_percent' => 10,
                'mobile_allowance' => 500,
                'special_allowance' => 1000,
                'other_allowance' => 500,
                'provident_fund_percent' => 5,
                'tax_percent' => 0,
            ],
        ];

        foreach ($grades as $index => $grade) {
            DB::table('salary_grades')->updateOrInsert(
                ['grade_name' => $grade['grade_name']],
                [
                    'uuid' => Str::uuid()->toString(),
                    'basic_salary' => $grade['basic_salary'],
                    'house_rent_percent' => $grade['house_rent_percent'],
                    'medical_percent' => $grade['medical_percent'],
                    'transport_percent' => $grade['transport_percent'],
                    'mobile_allowance' => $grade['mobile_allowance'],
                    'special_allowance' => $grade['special_allowance'],
                    'other_allowance' => $grade['other_allowance'],
                    'provident_fund_percent' => $grade['provident_fund_percent'],
                    'tax_percent' => $grade['tax_percent'],
                    'is_active' => true,
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
