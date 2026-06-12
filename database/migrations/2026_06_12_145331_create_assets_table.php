<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('final');
            $table->string('link_or_path');
            $table->string('version', 20)->nullable();
            $table->string('review_status', 20)->default('pending');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('content_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
