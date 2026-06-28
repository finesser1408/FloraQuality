<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Initial Super Admin (Forces password change on first login)
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'test@example.com',
            'role' => 'super_admin',
            'status' => 'active',
            'require_password_change' => true,
        ]);

        // Default Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'status' => 'active',
            'require_password_change' => false,
        ]);

        // Default Staff / Inspector User
        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'role' => 'staff',
            'status' => 'active',
            'require_password_change' => false,
        ]);
    }
}
