<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TiktokQc extends Model
{
    protected $table = 'tiktok_qc';

    protected $fillable = [
        'content_id',
        'hook_strong',
        'cta_clear',
        'audio_clear',
        'visual_quality',
        'caption_complete',
        'product_visible',
        'duration_appropriate',
        'branding_included',
        'no_sensitive_content',
        'notes',
        'status',
        'checked_by',
        'checked_at',
    ];

    protected $casts = [
        'hook_strong' => 'boolean',
        'cta_clear' => 'boolean',
        'audio_clear' => 'boolean',
        'visual_quality' => 'boolean',
        'caption_complete' => 'boolean',
        'product_visible' => 'boolean',
        'duration_appropriate' => 'boolean',
        'branding_included' => 'boolean',
        'no_sensitive_content' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
