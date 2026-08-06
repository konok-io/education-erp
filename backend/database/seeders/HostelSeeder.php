<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hostel\HostelBuilding;
use App\Models\Hostel\HostelRoom;
use App\Models\Hostel\HostelBed;
use App\Models\Hostel\HostelMessPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HostelSeeder extends Seeder
{
    public function run(): void
    {
        // Create Mess Plans
        $messPlans = [
            [
                'name' => 'Standard Mess',
                'name_bn' => 'স্ট্যান্ডার্ড মেস',
                'monthly_fee' => 3000,
                'include_breakfast' => true,
                'include_lunch' => true,
                'include_dinner' => true,
                'is_active' => true,
                'description' => 'Standard three meals per day',
            ],
            [
                'name' => 'Economy Mess',
                'name_bn' => 'ইকোনমি মেস',
                'monthly_fee' => 2500,
                'include_breakfast' => true,
                'include_lunch' => true,
                'include_dinner' => false,
                'is_active' => true,
                'description' => 'Breakfast and lunch only',
            ],
            [
                'name' => 'Premium Mess',
                'name_bn' => 'প্রিমিয়াম মেস',
                'monthly_fee' => 5000,
                'include_breakfast' => true,
                'include_lunch' => true,
                'include_dinner' => true,
                'is_active' => true,
                'description' => 'Premium three meals with special items',
            ],
        ];

        foreach ($messPlans as $plan) {
            HostelMessPlan::create(array_merge($plan, [
                'uuid' => (string) Str::uuid(),
            ]));
        }

        // Create Hostel Buildings
        $buildings = [
            [
                'building_code' => 'HB-2024-001',
                'name' => 'Boys Hostel A',
                'name_bn' => 'ছাত্র হোস্টেল এ',
                'gender' => 'male',
                'total_floors' => 4,
                'total_rooms' => 40,
                'total_beds' => 80,
                'address' => 'Main Campus, Block A',
                'status' => 'active',
            ],
            [
                'building_code' => 'HB-2024-002',
                'name' => 'Girls Hostel B',
                'name_bn' => 'ছাত্রী হোস্টেল বি',
                'gender' => 'female',
                'total_floors' => 3,
                'total_rooms' => 30,
                'total_beds' => 60,
                'address' => 'Main Campus, Block B',
                'status' => 'active',
            ],
            [
                'building_code' => 'HB-2024-003',
                'name' => 'International Students Hostel',
                'name_bn' => 'আন্তর্জাতিক ছাত্র হোস্টেল',
                'gender' => 'mixed',
                'total_floors' => 2,
                'total_rooms' => 20,
                'total_beds' => 40,
                'address' => 'International Wing',
                'status' => 'active',
            ],
        ];

        foreach ($buildings as $buildingData) {
            $building = HostelBuilding::create(array_merge($buildingData, [
                'uuid' => (string) Str::uuid(),
            ]));

            // Create rooms and beds for each building
            $this->createRoomsAndBeds($building);
        }
    }

    private function createRoomsAndBeds(HostelBuilding $building): void
    {
        $roomsPerFloor = (int) ($building->total_rooms / $building->total_floors);

        for ($floor = 1; $floor <= $building->total_floors; $floor++) {
            for ($room = 1; $room <= $roomsPerFloor; $room++) {
                $roomNumber = sprintf('%d%02d', $floor, $room);
                $bedsCount = rand(2, 4);

                $hostelRoom = HostelRoom::create([
                    'uuid' => (string) Str::uuid(),
                    'room_number' => $roomNumber,
                    'building_id' => $building->id,
                    'floor' => $floor,
                    'room_type' => $bedsCount === 2 ? 'double' : ($bedsCount === 3 ? 'triple' : 'four_seat'),
                    'capacity' => $bedsCount,
                    'current_occupancy' => 0,
                    'rent' => $bedsCount === 2 ? 4000 : ($bedsCount === 3 ? 3500 : 3000),
                    'status' => 'available',
                ]);

                for ($bed = 1; $bed <= $bedsCount; $bed++) {
                    HostelBed::create([
                        'uuid' => (string) Str::uuid(),
                        'bed_number' => "{$roomNumber}-B{$bed}",
                        'room_id' => $hostelRoom->id,
                        'position' => $bed === 1 ? 'lower' : ($bed === 2 ? 'upper' : 'middle'),
                        'status' => 'available',
                    ]);
                }
            }
        }
    }
}
