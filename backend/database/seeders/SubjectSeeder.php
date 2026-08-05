<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Get category IDs
        $categories = DB::table('book_categories')->pluck('id', 'code');
        
        $subjects = [
            ['name' => 'Physics', 'code' => 'PHY_GEN', 'category_code' => 'PHY'],
            ['name' => 'Chemistry', 'code' => 'CHM_GEN', 'category_code' => 'CHM'],
            ['name' => 'Biology', 'code' => 'BIO_GEN', 'category_code' => 'BIO'],
            ['name' => 'Mathematics', 'code' => 'MAT_GEN', 'category_code' => 'MAT'],
            ['name' => 'Computer Science', 'code' => 'CIT_CS', 'category_code' => 'CIT'],
            ['name' => 'Programming', 'code' => 'CIT_PRG', 'category_code' => 'CIT'],
            ['name' => 'Networking', 'code' => 'CIT_NET', 'category_code' => 'CIT'],
            ['name' => 'Database', 'code' => 'CIT_DB', 'category_code' => 'CIT'],
            ['name' => 'Accounting', 'code' => 'COM_ACC', 'category_code' => 'COM'],
            ['name' => 'Finance', 'code' => 'COM_FIN', 'category_code' => 'COM'],
            ['name' => 'Marketing', 'code' => 'COM_MKT', 'category_code' => 'COM'],
            ['name' => 'Management', 'code' => 'COM_MGT', 'category_code' => 'COM'],
            ['name' => 'Bengali Poetry', 'code' => 'BNG_POE', 'category_code' => 'BNG'],
            ['name' => 'Bengali Prose', 'code' => 'BNG_PRO', 'category_code' => 'BNG'],
            ['name' => 'English Poetry', 'code' => 'ENG_POE', 'category_code' => 'ENG'],
            ['name' => 'English Drama', 'code' => 'ENG_DRA', 'category_code' => 'ENG'],
            ['name' => 'Bangladesh History', 'code' => 'HIS_BD', 'category_code' => 'HIS'],
            ['name' => 'World History', 'code' => 'HIS_WLD', 'category_code' => 'HIS'],
            ['name' => 'Islamic History', 'code' => 'ISR_HIS', 'category_code' => 'ISR'],
            ['name' => 'Quran Studies', 'code' => 'ISR_QRN', 'category_code' => 'ISR'],
        ];

        foreach ($subjects as $subject) {
            $categoryId = $categories[$subject['category_code']] ?? null;
            
            DB::table('subjects')->updateOrInsert(
                ['code' => $subject['code']],
                [
                    'uuid' => Str::uuid()->toString(),
                    'name' => $subject['name'],
                    'name_bn' => null,
                    'category_id' => $categoryId,
                    'description' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
