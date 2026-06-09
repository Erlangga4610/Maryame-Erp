<?php

// File: database/migrations/2026_01_01_000004_add_erp_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id', 20)->unique()->nullable();
            $table->string('position', 100)->nullable(); // Jabatan
            $table->string('department', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'position', 'department', 'phone', 'is_active']);
        });
    }
};
