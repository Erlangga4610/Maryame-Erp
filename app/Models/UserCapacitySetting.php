<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCapacitySetting extends Model
{
    protected $fillable = [
        'user_id',
        'max_hours',
        'effective_from',
        'resolution_step',
        'resolution_notes',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
