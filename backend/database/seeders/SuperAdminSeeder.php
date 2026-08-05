<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        $superAdminEmail = config('auth.super_admin_email', 'admin@konok.io');
        
        if (User::where('email', $superAdminEmail)->exists()) {
            $this->command->info('Super Admin already exists!');
            return;
        }

        // Get super admin role
        $superAdminRole = DB::table('roles')
            ->where('name', 'super-admin')
            ->first();

        if (!$superAdminRole) {
            $this->command->error('Super Admin role not found! Please run RoleSeeder first.');
            return;
        }

        // Get first campus
        $campus = DB::table('campuses')->first();

        // Create super admin user
        $user = User::create([
            'uuid' => generate_uuid(),
            'campus_id' => $campus->id ?? null,
            'name' => 'Super Admin',
            'email' => $superAdminEmail,
            'password' => Hash::make('@rsm@k@1A'),
            'role_id' => $superAdminRole->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign super-admin role
        DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $user->id,
        ]);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: ' . $superAdminEmail);
        $this->command->warn('Password: @rsm@k@1A');
    }
}
