<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Brief extends Model
{
    protected $fillable = [
        'content_id',
        'angle', 'positioning', 'target_audience', 'key_message', 'tone', 'copy_brief',
        'aspect_ratio', 'resolution', 'duration', 'format_file', 'hashtag',
        'audio_guidance', 'originality_instruction', 'thumbnail_note',
        'visual_brief', 'video_brief',
        'is_final', 'finalized_at', 'finalized_by',
    ];

    protected $casts = [
        'is_final' => 'boolean',
        'finalized_at' => 'datetime',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function finalizer()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}
