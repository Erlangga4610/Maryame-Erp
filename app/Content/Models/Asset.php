<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_id',
        'type',
        'link_or_path',
        'version',
        'review_status',
        'uploaded_by',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
