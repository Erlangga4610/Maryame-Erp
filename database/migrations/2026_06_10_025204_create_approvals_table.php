<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('stage', 20); // mc_bm, legal
            $table->string('status', 20)->default('pending'); // pending, approved, revision
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['content_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
