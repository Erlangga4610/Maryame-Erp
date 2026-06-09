<?php

// File: database/migrations/2026_01_01_000001_create_platforms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('code', 5)->unique(); // TKM, IGF, IGR, FB, dll
            $table->boolean('originality_strict')->default(true); // Cross-Platform Originality rule berlaku?
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
