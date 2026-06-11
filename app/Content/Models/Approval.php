<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'content_id',
        'approver_id',
        'stage',
        'status',
        'notes',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
