<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't easily support enum changes, so we drop and recreate the column if needed,
        // or since it's sqlite, we can modify the schema by dropping the old column and adding the new string columns.
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->string('status')->default('active')->after('role'); // active, inactive
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->boolean('require_password_change')->default(false)->after('last_login_at');
        });

        // Let's modify role column to support super_admin, admin, staff. 
        // In sqlite, we can just treat it as a string column. Let's change the role field to string.
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('staff')->change();
        });

        // Add user_agent to activity_logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'status', 'last_login_at', 'require_password_change']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('user_agent');
        });
    }
};
