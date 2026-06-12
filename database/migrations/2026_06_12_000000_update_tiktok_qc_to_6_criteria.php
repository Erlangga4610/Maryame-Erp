<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiktok_qc', function (Blueprint $table) {
            $table->dropColumn([
                'hook_strong',
                'cta_clear',
                'audio_clear',
                'visual_quality',
                'caption_complete',
                'product_visible',
                'duration_appropriate',
                'branding_included',
                'no_sensitive_content',
            ]);

            $table->string('k1_audio_original', 10)->nullable()->after('content_id');
            $table->string('k2_demo_penggunaan', 10)->nullable()->after('k1_audio_original');
            $table->string('k3_produk_visible', 10)->nullable()->after('k2_demo_penggunaan');
            $table->string('k4_manfaat_verbal', 10)->nullable()->after('k3_produk_visible');
            $table->string('k5_tambahan', 10)->nullable()->after('k4_manfaat_verbal');
            $table->string('k6_tambahan', 10)->nullable()->after('k5_tambahan');
            $table->boolean('has_shopping_cart')->default(false)->after('k6_tambahan');
            $table->text('k5_label')->nullable()->after('has_shopping_cart');
            $table->text('k6_label')->nullable()->after('k5_label');
        });
    }

    public function down(): void
    {
        Schema::table('tiktok_qc', function (Blueprint $table) {
            $table->boolean('hook_strong')->default(false);
            $table->boolean('cta_clear')->default(false);
            $table->boolean('audio_clear')->default(false);
            $table->boolean('visual_quality')->default(false);
            $table->boolean('caption_complete')->default(false);
            $table->boolean('product_visible')->default(false);
            $table->boolean('duration_appropriate')->default(false);
            $table->boolean('branding_included')->default(false);
            $table->boolean('no_sensitive_content')->default(false);

            $table->dropColumn([
                'k1_audio_original',
                'k2_demo_penggunaan',
                'k3_produk_visible',
                'k4_manfaat_verbal',
                'k5_tambahan',
                'k6_tambahan',
                'has_shopping_cart',
                'k5_label',
                'k6_label',
            ]);
        });
    }
};
