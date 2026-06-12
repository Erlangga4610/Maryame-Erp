<?php

use App\Content\Models\Content;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('briefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete()->unique();

            // Strategic section (CSP only)
            $table->text('angle')->nullable();
            $table->text('positioning')->nullable();
            $table->text('target_audience')->nullable();
            $table->text('key_message')->nullable();
            $table->text('tone')->nullable();
            $table->text('copy_brief')->nullable();

            // Technical section (SMS only)
            $table->string('aspect_ratio', 20)->nullable();
            $table->string('resolution', 20)->nullable();
            $table->string('duration', 20)->nullable();
            $table->string('format_file', 50)->nullable();
            $table->text('hashtag')->nullable();
            $table->text('audio_guidance')->nullable();
            $table->text('originality_instruction')->nullable();
            $table->text('thumbnail_note')->nullable();
            $table->text('visual_brief')->nullable();
            $table->text('video_brief')->nullable();

            // Schedule & ownership (shared)
            $table->boolean('is_final')->default(false);
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        // Migrate existing data from contents to briefs
        Content::withTrashed()->chunk(100, function ($contents) {
            foreach ($contents as $c) {
                DB::table('briefs')->insert([
                    'content_id' => $c->id,
                    'angle' => $c->angle,
                    'positioning' => $c->positioning,
                    'target_audience' => $c->target_audience,
                    'key_message' => $c->key_message,
                    'tone' => $c->tone,
                    'copy_brief' => $c->copy_brief,
                    'aspect_ratio' => $c->aspect_ratio,
                    'resolution' => $c->resolution,
                    'duration' => $c->duration,
                    'format_file' => $c->format_file,
                    'hashtag' => $c->hashtag,
                    'audio_guidance' => $c->audio_guidance,
                    'originality_instruction' => $c->originality_instruction,
                    'thumbnail_note' => $c->thumbnail_note,
                    'visual_brief' => $c->visual_brief,
                    'video_brief' => $c->video_brief,
                    'is_final' => $c->is_brief_final,
                    'finalized_at' => $c->brief_finalized_at,
                    'finalized_by' => $c->brief_finalized_by,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('briefs');
    }
};
