<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('is_brief_final');
            $table->text('blocked_reason')->nullable()->after('is_blocked');
            $table->foreignId('blocked_by')->nullable()->constrained('users')->nullOnDelete()->after('blocked_reason');
            $table->timestamp('blocked_at')->nullable()->after('blocked_by');
            $table->boolean('is_briefed')->default(false)->after('blocked_at');
            $table->foreignId('briefed_by')->nullable()->constrained('users')->nullOnDelete()->after('is_briefed');
            $table->timestamp('briefed_at')->nullable()->after('briefed_by');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn([
                'is_blocked', 'blocked_reason', 'blocked_by', 'blocked_at',
                'is_briefed', 'briefed_by', 'briefed_at',
            ]);
        });
    }
};
