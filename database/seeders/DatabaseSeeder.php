<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with default demo users.
     */
    public function run(): void
    {
        // Super Administrator
        User::updateOrCreate(
            ["email" => "superadmin@floraquality.com"],
            [
                "name" => "Super Admin",
                "password" => Hash::make("Admin@1234"),
                "role" => "super_admin",
                "status" => "active",
                "email_verified_at" => now(),
                "require_password_change" => false,
            ],
        );

        // Administrator
        User::updateOrCreate(
            ["email" => "admin@floraquality.com"],
            [
                "name" => "Admin User",
                "password" => Hash::make("Admin@1234"),
                "role" => "admin",
                "status" => "active",
                "email_verified_at" => now(),
                "require_password_change" => false,
            ],
        );

        // Staff / Inspector
        User::updateOrCreate(
            ["email" => "staff@floraquality.com"],
            [
                "name" => "Staff User",
                "password" => Hash::make("Staff@1234"),
                "role" => "staff",
                "status" => "active",
                "email_verified_at" => now(),
                "require_password_change" => false,
            ],
        );
    }
}
