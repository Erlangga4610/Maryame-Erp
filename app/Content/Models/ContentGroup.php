<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentGroup extends Model
{
    protected $fillable = [
        'name',
        'notes',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'content_group_id');
    }
}
