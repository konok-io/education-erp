<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AssignPermissionsToRolesSeeder::class,
            CampusSeeder::class,
            AcademicLevelSeeder::class,
            DepartmentSeeder::class,
            EmploymentTypeSeeder::class,
            DesignationSeeder::class,
            SalaryGradeSeeder::class,
            LeaveTypeSeeder::class,
            HolidaySeeder::class,
            HrmSeeder::class, // Phase 034 - HRM Seeder
            CrmSeeder::class, // Phase 035 - CRM Seeder
            InventorySeeder::class, // Phase 036 - Asset/Inventory Seeder
            LibrarySeeder::class, // Phase 037 - Library Seeder
            HostelSeeder::class, // Phase 038 - Hostel Seeder
            TransportSeeder::class, // Phase 038 - Transport Seeder
            FacilitySeeder::class, // Phase 038 - Facility Seeder
            ExamSeeder::class, // Phase 039 - Examination Seeder
            BookCategorySeeder::class,
            SubjectSeeder::class,
            ProductCategorySeeder::class,
            ProductUnitSeeder::class,
            WarehouseSeeder::class,
            SettingSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
