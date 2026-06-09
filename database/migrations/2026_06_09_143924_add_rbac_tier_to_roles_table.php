<?php

// File: database/migrations/2026_01_01_000005_add_rbac_tier_to_roles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->tinyInteger('rbac_tier')->default(3)->after('guard_name');
            // rbac_tier: 1=edit (full access), 2=comment (review only), 3=view (read only)
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('rbac_tier');
        });
    }
};
