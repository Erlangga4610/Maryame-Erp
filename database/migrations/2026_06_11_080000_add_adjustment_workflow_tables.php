<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->string('adjustment_type', 20)->default('minor')->after('new_value');
            $table->text('adjustment_reason')->nullable()->after('adjustment_type');
        });

        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // major, reactive
            $table->text('reason');
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reviewer_notes')->nullable();
            $table->json('changed_fields')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjustments');

        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->dropColumn(['adjustment_type', 'adjustment_reason']);
        });
    }
};
