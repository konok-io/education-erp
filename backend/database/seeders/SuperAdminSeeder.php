<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminEmail = config('auth.super_admin_email', 'admin@konok.io');
        
        if (User::where('email', $superAdminEmail)->exists()) {
            $this->command->info('Super Admin already exists!');
            return;
        }

        // Create super-admin role if not exists
        $roleId = DB::table('roles')->updateOrInsert(
            ['name' => 'super-admin'],
            [
                'display_name' => 'Super Admin',
                'description' => 'Full system access',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ) ? DB::table('roles')->where('name', 'super-admin')->first()->id : 1;

        $role = DB::table('roles')->where('name', 'super-admin')->first();
        if ($role) {
            $roleId = $role->id;
        } else {
            $this->command->error('Failed to create super-admin role!');
            return;
        }

        // Get first campus
        $campus = DB::table('campuses')->first();

        User::create([
            'name' => 'Super Admin',
            'email' => $superAdminEmail,
            'password' => Hash::make('@rsm@k@1A'),
            'role_id' => $roleId,
            'campus_id' => $campus->id ?? null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: ' . $superAdminEmail);
        $this->command->warn('Password: @rsm@k@1A');
    }
}
