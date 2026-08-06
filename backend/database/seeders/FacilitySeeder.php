<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Facility\FacilityType;
use App\Models\Facility\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        // Create Facility Types
        $types = [
            [
                'name' => 'Auditorium',
                'name_bn' => 'অডিটোরিয়াম',
                'code' => 'AUD',
                'description' => 'Large auditorium for events and gatherings',
                'capacity' => 500,
                'hourly_rate' => 5000,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Conference Room',
                'name_bn' => 'সম্মেলন কক্ষ',
                'code' => 'CONF',
                'description' => 'Conference room for meetings and seminars',
                'capacity' => 50,
                'hourly_rate' => 1500,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Seminar Hall',
                'name_bn' => 'সেমিনার হল',
                'code' => 'SEM',
                'description' => 'Seminar hall for academic events',
                'capacity' => 150,
                'hourly_rate' => 2000,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Computer Lab',
                'name_bn' => 'কম্পিউটার ল্যাব',
                'code' => 'LAB',
                'description' => 'Computer laboratory for practical sessions',
                'capacity' => 40,
                'hourly_rate' => 1000,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sports Ground',
                'name_bn' => 'খেলার মাঠ',
                'code' => 'SPT',
                'description' => 'Sports ground for outdoor activities',
                'capacity' => 200,
                'hourly_rate' => 2000,
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Canteen',
                'name_bn' => 'ক্যান্টিন',
                'code' => 'CNT',
                'description' => 'Cafeteria and canteen facility',
                'capacity' => 100,
                'hourly_rate' => 0,
                'requires_approval' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Prayer Room',
                'name_bn' => 'নামাজ কক্ষ',
                'code' => 'PRY',
                'description' => 'Prayer and meditation room',
                'capacity' => 50,
                'hourly_rate' => 0,
                'requires_approval' => false,
                'is_active' => true,
            ],
        ];

        $facilityTypes = [];
        foreach ($types as $typeData) {
            $type = FacilityType::create(array_merge($typeData, [
                'uuid' => (string) Str::uuid(),
            ]));
            $facilityTypes[] = $type;
        }

        // Create Facilities
        $facilities = [
            // Auditorium
            [
                'name' => 'Main Auditorium',
                'name_bn' => 'প্রধান অডিটোরিয়াম',
                'facility_type_id' => 1,
                'code' => 'F001',
                'location' => 'Academic Building, Ground Floor',
                'capacity' => 500,
                'status' => 'available',
            ],
            // Conference Rooms
            [
                'name' => 'Conference Room A',
                'name_bn' => 'সম্মেলন কক্ষ এ',
                'facility_type_id' => 2,
                'code' => 'F002',
                'location' => 'Admin Building, 2nd Floor',
                'capacity' => 30,
                'status' => 'available',
            ],
            [
                'name' => 'Conference Room B',
                'name_bn' => 'সম্মেলন কক্ষ বি',
                'facility_type_id' => 2,
                'code' => 'F003',
                'location' => 'Admin Building, 2nd Floor',
                'capacity' => 50,
                'status' => 'available',
            ],
            // Seminar Halls
            [
                'name' => 'Seminar Hall 1',
                'name_bn' => 'সেমিনার হল ১',
                'facility_type_id' => 3,
                'code' => 'F004',
                'location' => 'Academic Block A',
                'capacity' => 100,
                'status' => 'available',
            ],
            // Computer Labs
            [
                'name' => 'Computer Lab 1',
                'name_bn' => 'কম্পিউটার ল্যাব ১',
                'facility_type_id' => 4,
                'code' => 'F005',
                'location' => 'ICT Building, 1st Floor',
                'capacity' => 40,
                'status' => 'available',
            ],
            [
                'name' => 'Computer Lab 2',
                'name_bn' => 'কম্পিউটার ল্যাব ২',
                'facility_type_id' => 4,
                'code' => 'F006',
                'location' => 'ICT Building, 2nd Floor',
                'capacity' => 40,
                'status' => 'available',
            ],
            // Sports
            [
                'name' => 'Main Playground',
                'name_bn' => 'প্রধান মাঠ',
                'facility_type_id' => 5,
                'code' => 'F007',
                'location' => 'Sports Complex',
                'capacity' => 200,
                'status' => 'available',
            ],
            // Others
            [
                'name' => 'Central Canteen',
                'name_bn' => 'কেন্দ্রীয় ক্যান্টিন',
                'facility_type_id' => 6,
                'code' => 'F008',
                'location' => 'Near Admin Building',
                'capacity' => 100,
                'status' => 'available',
            ],
            [
                'name' => 'Main Prayer Room',
                'name_bn' => 'প্রধান নামাজ কক্ষ',
                'facility_type_id' => 7,
                'code' => 'F009',
                'location' => 'Academic Building, Basement',
                'capacity' => 50,
                'status' => 'available',
            ],
        ];

        foreach ($facilities as $facilityData) {
            Facility::create(array_merge($facilityData, [
                'uuid' => (string) Str::uuid(),
                'equipment' => ['projector' => true, 'ac' => true, 'wifi' => true],
                'available_from' => '08:00:00',
                'available_to' => '20:00:00',
            ]));
        }
    }
}
