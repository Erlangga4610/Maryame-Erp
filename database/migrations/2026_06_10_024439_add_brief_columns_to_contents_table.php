<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->text('copy_brief')->nullable()->after('description');
            $table->text('visual_brief')->nullable()->after('copy_brief');
            $table->text('video_brief')->nullable()->after('visual_brief');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['copy_brief', 'visual_brief', 'video_brief']);
        });
    }
};
