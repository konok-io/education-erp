<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'code' => 'PCS', 'short_code' => 'pc'],
            ['name' => 'Box', 'code' => 'BOX', 'short_code' => 'box'],
            ['name' => 'Packet', 'code' => 'PKT', 'short_code' => 'pkt'],
            ['name' => 'Set', 'code' => 'SET', 'short_code' => 'set'],
            ['name' => 'Meter', 'code' => 'MTR', 'short_code' => 'm'],
            ['name' => 'Liter', 'code' => 'LTR', 'short_code' => 'L'],
            ['name' => 'Kilogram', 'code' => 'KG', 'short_code' => 'kg'],
            ['name' => 'Dozen', 'code' => 'DOZ', 'short_code' => 'dz'],
            ['name' => 'Bundle', 'code' => 'BDL', 'short_code' => 'bdl'],
            ['name' => 'Roll', 'code' => 'ROL', 'short_code' => 'rol'],
            ['name' => 'Ream', 'code' => 'RM', 'short_code' => 'rm'],
            ['name' => 'Pair', 'code' => 'PR', 'short_code' => 'pr'],
            ['name' => 'Unit', 'code' => 'UNT', 'short_code' => 'unit'],
            ['name' => 'Carton', 'code' => 'CTN', 'short_code' => 'ctn'],
        ];

        foreach ($units as $unit) {
            DB::table('product_units')->updateOrInsert(
                ['code' => $unit['code']],
                [
                    'name' => $unit['name'],
                    'short_code' => $unit['short_code'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
