<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->text('angle')->nullable();
            $table->text('positioning')->nullable();
            $table->text('target_audience')->nullable();
            $table->text('key_message')->nullable();
            $table->text('tone')->nullable();
            $table->string('aspect_ratio', 20)->nullable();
            $table->string('resolution', 20)->nullable();
            $table->string('duration', 20)->nullable();
            $table->string('format_file', 50)->nullable();
            $table->text('hashtag')->nullable();
            $table->text('audio_guidance')->nullable();
            $table->text('originality_instruction')->nullable();
            $table->text('thumbnail_note')->nullable();
            $table->boolean('is_brief_final')->default(false);
            $table->timestamp('brief_finalized_at')->nullable();
            $table->foreignId('brief_finalized_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn([
                'angle', 'positioning', 'target_audience', 'key_message', 'tone',
                'aspect_ratio', 'resolution', 'duration', 'format_file', 'hashtag',
                'audio_guidance', 'originality_instruction', 'thumbnail_note',
                'is_brief_final', 'brief_finalized_at', 'brief_finalized_by',
            ]);
        });
    }
};
