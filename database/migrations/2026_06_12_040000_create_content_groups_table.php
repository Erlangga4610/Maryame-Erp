<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->foreignId('content_group_id')
                ->nullable()
                ->after('campaign_id')
                ->constrained('content_groups')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropForeign(['content_group_id']);
            $table->dropColumn('content_group_id');
        });

        Schema::dropIfExists('content_groups');
    }
};
