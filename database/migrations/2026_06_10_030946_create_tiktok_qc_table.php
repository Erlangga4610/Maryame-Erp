<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiktok_qc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->boolean('hook_strong')->default(false);
            $table->boolean('cta_clear')->default(false);
            $table->boolean('audio_clear')->default(false);
            $table->boolean('visual_quality')->default(false);
            $table->boolean('caption_complete')->default(false);
            $table->boolean('product_visible')->default(false);
            $table->boolean('duration_appropriate')->default(false);
            $table->boolean('branding_included')->default(false);
            $table->boolean('no_sensitive_content')->default(false);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('passed');
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiktok_qc');
    }
};
