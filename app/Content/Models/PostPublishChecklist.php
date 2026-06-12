<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostPublishChecklist extends Model
{
    protected $table = 'post_publish_checklists';

    protected $fillable = [
        'content_id',
        'link_works',
        'thumbnail_visible',
        'caption_accurate',
        'hashtags_included',
        'cta_functional',
        'product_tagged',
        'no_typo',
        'audio_sync',
        'notes',
        'live_url',
        'checked_by',
        'checked_at',
    ];

    protected $casts = [
        'link_works' => 'boolean',
        'thumbnail_visible' => 'boolean',
        'caption_accurate' => 'boolean',
        'hashtags_included' => 'boolean',
        'cta_functional' => 'boolean',
        'product_tagged' => 'boolean',
        'no_typo' => 'boolean',
        'audio_sync' => 'boolean',
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
