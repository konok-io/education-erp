<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Science', 'code' => 'SCI', 'sort_order' => 1],
            ['name' => 'Commerce', 'code' => 'COM', 'sort_order' => 2],
            ['name' => 'Arts', 'code' => 'ART', 'sort_order' => 3],
            ['name' => 'Computer & IT', 'code' => 'CIT', 'sort_order' => 4],
            ['name' => 'Mathematics', 'code' => 'MAT', 'sort_order' => 5],
            ['name' => 'Physics', 'code' => 'PHY', 'sort_order' => 6],
            ['name' => 'Chemistry', 'code' => 'CHM', 'sort_order' => 7],
            ['name' => 'Biology', 'code' => 'BIO', 'sort_order' => 8],
            ['name' => 'Bangla Literature', 'code' => 'BNG', 'sort_order' => 9],
            ['name' => 'English Literature', 'code' => 'ENG', 'sort_order' => 10],
            ['name' => 'History', 'code' => 'HIS', 'sort_order' => 11],
            ['name' => 'Islamic Studies', 'code' => 'ISR', 'sort_order' => 12],
            ['name' => 'Reference', 'code' => 'REF', 'sort_order' => 13],
            ['name' => 'Magazine & Journal', 'code' => 'MGZ', 'sort_order' => 14],
            ['name' => 'Research', 'code' => 'RES', 'sort_order' => 15],
            ['name' => 'Children', 'code' => 'CHD', 'sort_order' => 16],
        ];

        foreach ($categories as $category) {
            DB::table('book_categories')->updateOrInsert(
                ['code' => $category['code']],
                [
                    'uuid' => Str::uuid()->toString(),
                    'name' => $category['name'],
                    'name_bn' => null,
                    'description' => null,
                    'icon' => null,
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
