<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            $table->string('content_code', 30)->unique();

            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();

            $table->string('theme', 200);
            $table->text('caption')->nullable();
            $table->text('description')->nullable();

            $table->string('status', 30)->default('draft');

            $table->string('content_type', 20)->nullable();
            $table->string('format', 20)->nullable();
            $table->string('priority', 10)->default('rutin');

            $table->string('tiktok_subtype', 30)->nullable();

            $table->string('final_asset_link')->nullable();
            $table->string('thumbnail_link')->nullable();

            $table->boolean('has_claim')->default(false);
            $table->boolean('is_sensitive')->default(false);

            $table->date('publish_date')->nullable();
            $table->time('publish_time')->nullable();
            $table->date('deadline_produksi')->nullable();
            $table->date('deadline_approval')->nullable();

            $table->foreignId('pic_copy_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pic_visual_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pic_video_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('revision_note')->nullable();
            $table->integer('version')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['platform_id', 'status']);
            $table->index(['publish_date', 'status']);
            $table->index('deadline_produksi');
            $table->index('status');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
