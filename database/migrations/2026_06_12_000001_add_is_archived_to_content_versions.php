<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_versions', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('data');
            $table->foreignId('archived_by')->nullable()->after('is_archived')->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable()->after('archived_by');
        });
    }

    public function down(): void
    {
        Schema::table('content_versions', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'archived_by', 'archived_at']);
        });
    }
};
