<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_capacity_settings', function (Blueprint $table) {
            $table->tinyInteger('resolution_step')->default(0)->after('max_hours');
            $table->text('resolution_notes')->nullable()->after('resolution_step');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete()->after('resolution_notes');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
        });
    }

    public function down(): void
    {
        Schema::table('user_capacity_settings', function (Blueprint $table) {
            $table->dropColumn(['resolution_step', 'resolution_notes', 'confirmed_by', 'confirmed_at']);
        });
    }
};
