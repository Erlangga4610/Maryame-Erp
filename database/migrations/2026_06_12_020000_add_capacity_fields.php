<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->decimal('est_copy_hours', 5, 1)->nullable()->after('thumbnail_link');
            $table->decimal('est_visual_hours', 5, 1)->nullable()->after('est_copy_hours');
            $table->decimal('est_video_hours', 5, 1)->nullable()->after('est_visual_hours');
        });

        Schema::create('user_capacity_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('max_hours', 5, 1)->default(40);
            $table->date('effective_from');
            $table->timestamps();

            $table->unique(['user_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['est_copy_hours', 'est_visual_hours', 'est_video_hours']);
        });

        Schema::dropIfExists('user_capacity_settings');
    }
};
