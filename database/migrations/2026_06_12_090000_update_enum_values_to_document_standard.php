<?php

use App\Content\Enums\ContentFormat;
use App\Content\Enums\ContentPriority;
use App\Content\Enums\ContentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update ContentType: old (feed, reels, story, carousel) → new (edukasi, jualan, testimoni, trending, ugc, campaign)
        DB::statement("UPDATE contents SET content_type = 'edukasi' WHERE content_type IN ('feed')");
        DB::statement("UPDATE contents SET content_type = 'jualan' WHERE content_type IN ('reels')");
        DB::statement("UPDATE contents SET content_type = 'testimoni' WHERE content_type IN ('story')");
        DB::statement("UPDATE contents SET content_type = 'campaign' WHERE content_type IN ('carousel')");

        // Update ContentPriority: old (high, medium, low) → new (rutin, campaign, spontan)
        DB::statement("UPDATE contents SET priority = 'campaign' WHERE priority IN ('high')");
        DB::statement("UPDATE contents SET priority = 'rutin' WHERE priority IN ('medium')");
        DB::statement("UPDATE contents SET priority = 'spontan' WHERE priority IN ('low')");

        // Update ContentFormat: old (image, video, carousel) → new (photo, video, carousel, stories, listing, blog, broadcast)
        DB::statement("UPDATE contents SET format = 'photo' WHERE format IN ('image')");

        // Update default value for priority column
        DB::statement("ALTER TABLE contents ALTER COLUMN priority SET DEFAULT 'rutin'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE contents ALTER COLUMN priority SET DEFAULT 'medium'");

        DB::statement("UPDATE contents SET format = 'image' WHERE format IN ('photo')");

        DB::statement("UPDATE contents SET priority = 'high' WHERE priority IN ('campaign')");
        DB::statement("UPDATE contents SET priority = 'medium' WHERE priority IN ('rutin')");
        DB::statement("UPDATE contents SET priority = 'low' WHERE priority IN ('spontan')");

        DB::statement("UPDATE contents SET content_type = 'feed' WHERE content_type IN ('edukasi')");
        DB::statement("UPDATE contents SET content_type = 'reels' WHERE content_type IN ('jualan')");
        DB::statement("UPDATE contents SET content_type = 'story' WHERE content_type IN ('testimoni')");
        DB::statement("UPDATE contents SET content_type = 'carousel' WHERE content_type IN ('campaign')");
    }
};
