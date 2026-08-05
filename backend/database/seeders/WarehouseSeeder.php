<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Main Store',
                'code' => 'MAIN',
                'type' => 'main',
                'description' => 'Central warehouse for all items',
            ],
            [
                'name' => 'IT Department Store',
                'code' => 'IT',
                'type' => 'it',
                'description' => 'IT equipment storage',
            ],
            [
                'name' => 'Library Store',
                'code' => 'LIB',
                'type' => 'library',
                'description' => 'Library books and materials',
            ],
            [
                'name' => 'Laboratory Store',
                'code' => 'LAB',
                'type' => 'laboratory',
                'description' => 'Laboratory equipment and supplies',
            ],
            [
                'name' => 'Stationery Store',
                'code' => 'STA',
                'type' => 'department',
                'description' => 'Office stationery storage',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->updateOrInsert(
                ['code' => $warehouse['code']],
                [
                    'name' => $warehouse['name'],
                    'type' => $warehouse['type'],
                    'description' => $warehouse['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
