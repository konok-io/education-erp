<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignPermissionsToRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $teacherRole = DB::table('roles')->where('name', 'teacher')->first();
        $studentRole = DB::table('roles')->where('name', 'student')->first();
        $accountantRole = DB::table('roles')->where('name', 'accountant')->first();
        $librarianRole = DB::table('roles')->where('name', 'librarian')->first();

        // Get permission IDs
        $permissions = DB::table('permissions')->pluck('id', 'name');

        // Admin gets all permissions
        $adminPermissions = array_keys($permissions);
        $adminRolePermissions = array_map(function ($permissionId) use ($adminRole) {
            return [
                'role_id' => $adminRole->id,
                'permission_id' => $permissionId,
            ];
        }, $adminPermissions);
        DB::table('role_permission')->insert($adminRolePermissions);

        // Teacher permissions
        $teacherPermissions = array_filter(array_keys($permissions), function ($perm) {
            $allowed = ['attendance', 'results', 'routines', 'academics'];
            foreach ($allowed as $group) {
                if (str_starts_with($perm, $group . '.')) {
                    return true;
                }
            }
            return false;
        });
        
        $teacherRolePermissions = array_map(function ($perm) use ($teacherRole, $permissions) {
            return [
                'role_id' => $teacherRole->id,
                'permission_id' => $permissions[$perm],
            ];
        }, $teacherPermissions);
        DB::table('role_permission')->insert($teacherRolePermissions);

        // Student permissions
        $studentPermissions = ['students.view', 'attendance.view', 'results.view', 'routines.view'];
        $studentRolePermissions = array_map(function ($perm) use ($studentRole, $permissions) {
            return [
                'role_id' => $studentRole->id,
                'permission_id' => $permissions[$perm],
            ];
        }, $studentPermissions);
        DB::table('role_permission')->insert($studentRolePermissions);

        // Accountant permissions
        $accountantPermissions = array_filter(array_keys($permissions), function ($perm) {
            return str_starts_with($perm, 'fees.') || str_starts_with($perm, 'reports.');
        });
        
        $accountantRolePermissions = array_map(function ($perm) use ($accountantRole, $permissions) {
            return [
                'role_id' => $accountantRole->id,
                'permission_id' => $permissions[$perm],
            ];
        }, $accountantPermissions);
        DB::table('role_permission')->insert($accountantRolePermissions);

        // Librarian permissions
        $librarianPermissions = array_filter(array_keys($permissions), function ($perm) {
            return str_starts_with($perm, 'library.');
        });
        
        $librarianRolePermissions = array_map(function ($perm) use ($librarianRole, $permissions) {
            return [
                'role_id' => $librarianRole->id,
                'permission_id' => $permissions[$perm],
            ];
        }, $librarianPermissions);
        DB::table('role_permission')->insert($librarianRolePermissions);

        $this->command->info('Permissions assigned to roles successfully!');
    }
}
