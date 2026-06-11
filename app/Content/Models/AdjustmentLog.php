<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AdjustmentLog extends Model
{
    protected $fillable = [
        'content_id',
        'user_id',
        'field',
        'old_value',
        'new_value',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
