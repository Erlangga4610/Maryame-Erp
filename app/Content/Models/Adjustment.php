<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    protected $fillable = [
        'content_id',
        'type',
        'reason',
        'status',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'reviewer_notes',
        'changed_fields',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
