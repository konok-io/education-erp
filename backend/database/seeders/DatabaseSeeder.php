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
            BookCategorySeeder::class,
            SubjectSeeder::class,
            SettingSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
