<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => json_encode('Education ERP'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Website name',
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Website logo path',
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Favicon path',
            ],
            [
                'key' => 'email',
                'value' => json_encode('info@educationerp.com'),
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Contact email',
            ],
            [
                'key' => 'phone',
                'value' => json_encode('+880 1XXX-XXXXXX'),
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Contact phone',
            ],
            [
                'key' => 'address',
                'value' => json_encode('123 Education Street, Dhaka'),
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Contact address',
            ],
            [
                'key' => 'timezone',
                'value' => json_encode('Asia/Dhaka'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'System timezone',
            ],
            [
                'key' => 'currency',
                'value' => json_encode('BDT'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Currency code',
            ],
            [
                'key' => 'currency_symbol',
                'value' => json_encode('৳'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Currency symbol',
            ],
            [
                'key' => 'language',
                'value' => json_encode('en'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default language',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => json_encode(false),
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable maintenance mode',
            ],
            [
                'key' => 'registration_open',
                'value' => json_encode(true),
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Allow student registration',
            ],
            [
                'key' => 'attendance_auto_close',
                'value' => json_encode(true),
                'type' => 'boolean',
                'group' => 'attendance',
                'description' => 'Auto close attendance after marked',
            ],
            [
                'key' => 'attendance_marking_time',
                'value' => json_encode('10:00'),
                'type' => 'string',
                'group' => 'attendance',
                'description' => 'Last attendance marking time',
            ],
            [
                'key' => 'fee_due_charge',
                'value' => json_encode(0),
                'type' => 'number',
                'group' => 'fees',
                'description' => 'Daily due charge amount',
            ],
        ];

        $settings = array_map(function ($setting) {
            return array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $settings);

        DB::table('settings')->insert($settings);

        $this->command->info('Settings seeded successfully!');
    }
}
