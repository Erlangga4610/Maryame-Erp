<?php

// File: database/migrations/2026_01_01_000003_create_campaigns_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['seasonal', 'launching', 'tactical']);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('objective')->nullable(); // Tujuan campaign
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
