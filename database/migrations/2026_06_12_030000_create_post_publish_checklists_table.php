<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_publish_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->boolean('link_works')->default(false);
            $table->boolean('thumbnail_visible')->default(false);
            $table->boolean('caption_accurate')->default(false);
            $table->boolean('hashtags_included')->default(false);
            $table->boolean('cta_functional')->default(false);
            $table->boolean('product_tagged')->default(false);
            $table->boolean('no_typo')->default(false);
            $table->boolean('audio_sync')->default(false);
            $table->text('notes')->nullable();
            $table->string('live_url')->nullable();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->string('live_url')->nullable()->after('thumbnail_link');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('live_url');
        });

        Schema::dropIfExists('post_publish_checklists');
    }
};
