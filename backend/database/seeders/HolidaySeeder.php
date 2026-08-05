<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;
        
        $holidays = [
            [
                'name' => 'Weekly Holiday (Friday)',
                'holiday_date' => "$year-01-03",
                'holiday_type' => 'weekly',
                'is_repeating' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Language Martyrs Day',
                'holiday_date' => "$year-02-21",
                'holiday_type' => 'national',
                'is_repeating' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Independence Day',
                'holiday_date' => "$year-03-26",
                'holiday_type' => 'national',
                'is_repeating' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Bangla New Year',
                'holiday_date' => "$year-04-14",
                'holiday_type' => 'national',
                'is_repeating' => true,
                'is_active' => true,
            ],
            [
                'name' => 'May Day',
                'holiday_date' => "$year-05-01",
                'holiday_type' => 'national',
                'is_repeating' => true,
                'is_active' => true,
            ],
            [
                'name' => 'National Mourning Day',
                'holiday_date' => "$year-08-15",
                'holiday_type' => 'national',
                'is_repeating' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Eid-ul-Fitr',
                'holiday_date' => "$year-03-30",
                'holiday_type' => 'religious',
                'is_repeating' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Eid-ul-Fitr (Holiday)',
                'holiday_date' => "$year-03-31",
                'holiday_type' => 'religious',
                'is_repeating' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Eid-ul-Fitr (Holiday)',
                'holiday_date' => "$year-04-01",
                'holiday_type' => 'religious',
                'is_repeating' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Eid-ul-Adha',
                'holiday_date' => "$year-06-17",
                'holiday_type' => 'religious',
                'is_repeating' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Eid-ul-Adha (Holiday)',
                'holiday_date' => "$year-06-18",
                'holiday_type' => 'religious',
                'is_repeating' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Durga Puja',
                'holiday_date' => "$year-10-11",
                'holiday_type' => 'religious',
                'is_repeating' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Durga Puja (Holiday)',
                'holiday_date' => "$year-10-12",
                'holiday_type' => 'religious',
                'is_repeating' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Christmas Day',
                'holiday_date' => "$year-12-25",
                'holiday_type' => 'religious',
                'is_repeating' => true,
                'is_active' => true,
            ],
        ];

        foreach ($holidays as $holiday) {
            DB::table('holidays')->updateOrInsert(
                [
                    'name' => $holiday['name'],
                    'holiday_date' => $holiday['holiday_date'],
                ],
                [
                    'uuid' => Str::uuid()->toString(),
                    'name_bn' => null,
                    'holiday_type' => $holiday['holiday_type'],
                    'is_repeating' => $holiday['is_repeating'],
                    'is_active' => $holiday['is_active'],
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
