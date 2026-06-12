<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCapacitySetting extends Model
{
    protected $fillable = [
        'user_id',
        'max_hours',
        'effective_from',
    ];

    protected $casts = [
        'effective_from' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
