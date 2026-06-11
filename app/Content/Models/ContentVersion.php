<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContentVersion extends Model
{
    protected $fillable = [
        'content_id',
        'version',
        'data',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
