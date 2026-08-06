<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Transport\TransportVehicle;
use App\Models\Transport\TransportDriver;
use App\Models\Transport\TransportRoute;
use App\Models\Transport\TransportStop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransportSeeder extends Seeder
{
    public function run(): void
    {
        // Create Vehicles
        $vehicles = [
            [
                'vehicle_number' => 'DH Metro-001',
                'registration_no' => 'DHAKA-METRO-001',
                'vehicle_type' => 'bus',
                'brand' => 'Hino',
                'model' => 'AK1J',
                'capacity' => 50,
                'color' => 'White',
                'fuel_type' => 'diesel',
                'status' => 'active',
            ],
            [
                'vehicle_number' => 'DH Metro-002',
                'registration_no' => 'DHAKA-METRO-002',
                'vehicle_type' => 'bus',
                'brand' => 'Hino',
                'model' => 'AK1J',
                'capacity' => 50,
                'color' => 'White',
                'fuel_type' => 'diesel',
                'status' => 'active',
            ],
            [
                'vehicle_number' => 'DH Mini-001',
                'registration_no' => 'DHAKA-MINI-001',
                'vehicle_type' => 'mini_bus',
                'brand' => 'Toyota',
                'model' => 'HiAce',
                'capacity' => 20,
                'color' => 'Silver',
                'fuel_type' => 'petrol',
                'status' => 'active',
            ],
            [
                'vehicle_number' => 'DH Van-001',
                'registration_no' => 'DHAKA-VAN-001',
                'vehicle_type' => 'van',
                'brand' => 'Toyota',
                'model' => 'Hiace',
                'capacity' => 15,
                'color' => 'White',
                'fuel_type' => 'cng',
                'status' => 'active',
            ],
        ];

        $createdVehicles = [];
        foreach ($vehicles as $vehicleData) {
            $vehicle = TransportVehicle::create(array_merge($vehicleData, [
                'uuid' => (string) Str::uuid(),
            ]));
            $createdVehicles[] = $vehicle;
        }

        // Create Drivers
        $drivers = [
            [
                'driver_id' => 'DRV/2024/0001',
                'name' => 'Kamal Ahmed',
                'father_name' => 'Rahim Uddin',
                'date_of_birth' => '1985-05-15',
                'gender' => 'male',
                'phone' => '01712345601',
                'license_no' => 'DL-001234567',
                'license_type' => 'Heavy Vehicle',
                'license_expiry' => '2026-12-31',
                'experience_years' => 10,
                'joining_date' => '2020-01-15',
                'salary' => 25000,
                'status' => 'active',
            ],
            [
                'driver_id' => 'DRV/2024/0002',
                'name' => 'Jamal Hossain',
                'father_name' => 'Kabir Ahmed',
                'date_of_birth' => '1988-08-20',
                'gender' => 'male',
                'phone' => '01712345602',
                'license_no' => 'DL-001234568',
                'license_type' => 'Heavy Vehicle',
                'license_expiry' => '2026-06-30',
                'experience_years' => 8,
                'joining_date' => '2021-03-10',
                'salary' => 25000,
                'status' => 'active',
            ],
            [
                'driver_id' => 'DRV/2024/0003',
                'name' => 'Rahim Khan',
                'father_name' => 'Azizur Rahman',
                'date_of_birth' => '1990-03-10',
                'gender' => 'male',
                'phone' => '01712345603',
                'license_no' => 'DL-001234569',
                'license_type' => 'Light Vehicle',
                'license_expiry' => '2027-03-15',
                'experience_years' => 5,
                'joining_date' => '2022-06-01',
                'salary' => 22000,
                'status' => 'active',
            ],
        ];

        $createdDrivers = [];
        foreach ($drivers as $driverData) {
            $driver = TransportDriver::create(array_merge($driverData, [
                'uuid' => (string) Str::uuid(),
            ]));
            $createdDrivers[] = $driver;
        }

        // Create Routes
        $routes = [
            [
                'route_code' => 'R001',
                'name' => 'Uttara Route',
                'distance' => 15.5,
                'distance_unit' => 'km',
                'estimated_time' => 45,
                'status' => 'active',
            ],
            [
                'route_code' => 'R002',
                'name' => 'Dhanmondi Route',
                'distance' => 12.0,
                'distance_unit' => 'km',
                'estimated_time' => 40,
                'status' => 'active',
            ],
            [
                'route_code' => 'R003',
                'name' => 'Mirpur Route',
                'distance' => 8.5,
                'distance_unit' => 'km',
                'estimated_time' => 30,
                'status' => 'active',
            ],
            [
                'route_code' => 'R004',
                'name' => 'Gulshan Route',
                'distance' => 10.0,
                'distance_unit' => 'km',
                'estimated_time' => 35,
                'status' => 'active',
            ],
        ];

        foreach ($routes as $index => $routeData) {
            $route = TransportRoute::create(array_merge($routeData, [
                'uuid' => (string) Str::uuid(),
                'vehicle_id' => $createdVehicles[$index % count($createdVehicles)]->id ?? null,
                'driver_id' => $createdDrivers[$index % count($createdDrivers)]->id ?? null,
            ]));

            // Add stops to route
            $this->createRouteStops($route, $routeData['name']);
        }
    }

    private function createRouteStops(TransportRoute $route, string $routeName): void
    {
        $stopNames = [
            'Uttara Route' => ['Uttara Sector 1', 'Uttara Sector 3', 'Uttara Sector 7', 'Bashundhara', 'Campus'],
            'Dhanmondi Route' => ['Dhanmondi 27', 'Dhanmondi 32', 'Jigatola', 'Muhammadpur', 'Campus'],
            'Mirpur Route' => ['Mirpur 1', 'Mirpur 10', 'Mirpur 14', 'Kazipara', 'Campus'],
            'Gulshan Route' => ['Gulshan 1', 'Gulshan 2', 'Banani', 'Mohakhali', 'Campus'],
        ];

        $stops = $stopNames[$routeName] ?? ['Stop 1', 'Stop 2', 'Stop 3', 'Campus'];

        foreach ($stops as $index => $stopName) {
            $pickupHour = 7 + ($index * 10 / 60);
            $dropHour = 17 + ($index * 10 / 60);

            TransportStop::create([
                'uuid' => (string) Str::uuid(),
                'name' => $stopName,
                'route_id' => $route->id,
                'address' => $stopName . ', Dhaka',
                'pickup_time' => sprintf('%02d:%02d:00', (int) $pickupHour, (int) (($pickupHour - (int) $pickupHour) * 60)),
                'drop_time' => sprintf('%02d:%02d:00', (int) $dropHour, (int) (($dropHour - (int) $dropHour) * 60)),
                'extra_fee' => $index === 0 ? 0 : ($index * 100),
                'stop_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
}
