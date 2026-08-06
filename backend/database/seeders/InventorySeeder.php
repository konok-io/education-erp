<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Inventory\AssetCategory;
use App\Models\Inventory\InventoryCategory;
use App\Models\Inventory\InventoryLocation;
use App\Models\Inventory\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAssetCategories();
        $this->seedInventoryCategories();
        $this->seedInventoryLocations();
        $this->seedVendors();
    }

    private function seedAssetCategories(): void
    {
        $categories = [
            ['name' => 'Furniture', 'code' => 'FURN', 'depreciation_rate' => 10, 'useful_life_years' => 10],
            ['name' => 'Electronics', 'code' => 'ELEC', 'depreciation_rate' => 20, 'useful_life_years' => 5],
            ['name' => 'Vehicles', 'code' => 'VEHI', 'depreciation_rate' => 15, 'useful_life_years' => 10],
            ['name' => 'Office Equipment', 'code' => 'OFEQ', 'depreciation_rate' => 15, 'useful_life_years' => 7],
            ['name' => 'Computer Hardware', 'code' => 'COMP', 'depreciation_rate' => 25, 'useful_life_years' => 4],
            ['name' => 'Laboratory Equipment', 'code' => 'LABP', 'depreciation_rate' => 15, 'useful_life_years' => 7],
            ['name' => 'Sports Equipment', 'code' => 'SPOR', 'depreciation_rate' => 20, 'useful_life_years' => 5],
            ['name' => 'Audio/Video Equipment', 'code' => 'AVEQ', 'depreciation_rate' => 20, 'useful_life_years' => 5],
            ['name' => 'Kitchen Equipment', 'code' => 'KITE', 'depreciation_rate' => 15, 'useful_life_years' => 7],
            ['name' => 'Security Equipment', 'code' => 'SECU', 'depreciation_rate' => 15, 'useful_life_years' => 7],
        ];

        foreach ($categories as $category) {
            AssetCategory::firstOrCreate(
                ['code' => $category['code']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $category['name'],
                    'code' => $category['code'],
                    'depreciation_rate' => $category['depreciation_rate'],
                    'depreciation_method' => 'straight_line',
                    'useful_life_years' => $category['useful_life_years'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Asset categories seeded successfully!');
    }

    private function seedInventoryCategories(): void
    {
        $categories = [
            ['name' => 'Stationery', 'code' => 'STAT'],
            ['name' => 'Cleaning Supplies', 'code' => 'CLSP'],
            ['name' => 'Electrical Supplies', 'code' => 'ELSP'],
            ['name' => 'Plumbing Supplies', 'code' => 'PLSP'],
            ['name' => 'Hardware Tools', 'code' => 'HWTL'],
            ['name' => 'Sports & Recreation', 'code' => 'SPRE'],
            ['name' => 'First Aid & Medical', 'code' => 'FAMR'],
            ['name' => 'IT Accessories', 'code' => 'ITAC'],
            ['name' => 'Laboratory Supplies', 'code' => 'LABS'],
            ['name' => 'Catering Supplies', 'code' => 'CATS'],
        ];

        foreach ($categories as $category) {
            InventoryCategory::firstOrCreate(
                ['code' => $category['code']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $category['name'],
                    'code' => $category['code'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Inventory categories seeded successfully!');
    }

    private function seedInventoryLocations(): void
    {
        $locations = [
            ['name' => 'Main Store', 'code' => 'MAIN', 'type' => 'warehouse'],
            ['name' => 'Academic Block A', 'code' => 'ACA-A', 'type' => 'office'],
            ['name' => 'Academic Block B', 'code' => 'ACA-B', 'type' => 'office'],
            ['name' => 'Admin Building', 'code' => 'ADMN', 'type' => 'office'],
            ['name' => 'Library', 'code' => 'LIBR', 'type' => 'library'],
            ['name' => 'Computer Lab', 'code' => 'COMP', 'type' => 'lab'],
            ['name' => 'Science Lab', 'code' => 'SCIL', 'type' => 'lab'],
            ['name' => 'Sports Room', 'code' => 'SPOR', 'type' => 'sports'],
            ['name' => 'Canteen', 'code' => 'CANT', 'type' => 'cafeteria'],
            ['name' => 'Hostel Block 1', 'code' => 'HST1', 'type' => 'hostel'],
        ];

        foreach ($locations as $location) {
            InventoryLocation::firstOrCreate(
                ['code' => $location['code']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $location['name'],
                    'code' => $location['code'],
                    'type' => $location['type'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Inventory locations seeded successfully!');
    }

    private function seedVendors(): void
    {
        $vendors = [
            [
                'name' => 'Tech Solutions Ltd',
                'contact_person' => 'Mr. Rahman',
                'mobile' => '01712345678',
                'email' => 'info@techsolutions.com',
                'vendor_type' => 'supplier',
            ],
            [
                'name' => 'Office World',
                'contact_person' => 'Ms. Begum',
                'mobile' => '01812345678',
                'email' => 'sales@officeworld.com',
                'vendor_type' => 'supplier',
            ],
            [
                'name' => 'Furniture Palace',
                'contact_person' => 'Mr. Khan',
                'mobile' => '01912345678',
                'email' => 'info@furniturepalace.com',
                'vendor_type' => 'supplier',
            ],
            [
                'name' => 'IT Hub Bangladesh',
                'contact_person' => 'Mr. Alam',
                'mobile' => '01612345678',
                'email' => 'support@ithubbd.com',
                'vendor_type' => 'service_provider',
            ],
            [
                'name' => 'Clean Pro Services',
                'contact_person' => 'Ms. Aktar',
                'mobile' => '01512345678',
                'email' => 'info@cleanpro.com',
                'vendor_type' => 'service_provider',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(
                ['name' => $vendor['name']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $vendor['name'],
                    'contact_person' => $vendor['contact_person'],
                    'mobile' => $vendor['mobile'],
                    'email' => $vendor['email'],
                    'vendor_type' => $vendor['vendor_type'],
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('Vendors seeded successfully!');
    }
}
