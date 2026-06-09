<?php

namespace App\Content\Observers;

use App\Content\Models\Content;
use App\Content\Services\ContentCodeGenerator;
use Illuminate\Support\Facades\Log;

class ContentObserver
{
    public function creating(Content $content): void
    {
        if (empty($content->content_code)) {
            $platform = $content->platform;
            $content->content_code = ContentCodeGenerator::generate($platform);

            Log::info('Content code generated', [
                'content_code' => $content->content_code,
                'platform' => $platform->code,
            ]);
        }
    }

    public function updating(Content $content): void
    {
        if ($content->isDirty('content_code')) {
            $original = $content->getOriginal('content_code');

            Log::warning('Attempt to change immutable content_code blocked', [
                'content_id' => $content->id,
                'original' => $original,
                'attempted' => $content->content_code,
            ]);

            $content->content_code = $original;
        }
    }

    public function created(Content $content): void
    {
        Log::info('Content created', ['content_code' => $content->content_code]);
    }
}
