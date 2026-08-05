<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Office Equipment', 'code' => 'OFF', 'sort_order' => 1],
            ['name' => 'Computer & IT', 'code' => 'CIT', 'sort_order' => 2],
            ['name' => 'Networking', 'code' => 'NET', 'sort_order' => 3],
            ['name' => 'Furniture', 'code' => 'FUR', 'sort_order' => 4],
            ['name' => 'Stationery', 'code' => 'STA', 'sort_order' => 5],
            ['name' => 'Electrical', 'code' => 'ELE', 'sort_order' => 6],
            ['name' => 'Cleaning', 'code' => 'CLN', 'sort_order' => 7],
            ['name' => 'Laboratory', 'code' => 'LAB', 'sort_order' => 8],
            ['name' => 'Sports', 'code' => 'SPT', 'sort_order' => 9],
            ['name' => 'Medical', 'code' => 'MED', 'sort_order' => 10],
            ['name' => 'Audio Visual', 'code' => 'AVP', 'sort_order' => 11],
            ['name' => 'Safety & Security', 'code' => 'SFT', 'sort_order' => 12],
            ['name' => 'Catering', 'code' => 'CAT', 'sort_order' => 13],
            ['name' => 'Printing & Binding', 'code' => 'PRN', 'sort_order' => 14],
            ['name' => 'Others', 'code' => 'OTH', 'sort_order' => 15],
        ];

        foreach ($categories as $category) {
            DB::table('product_categories')->updateOrInsert(
                ['code' => $category['code']],
                [
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
