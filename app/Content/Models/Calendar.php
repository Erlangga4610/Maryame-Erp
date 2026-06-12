<?php

namespace App\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $fillable = [
        'month',
        'year',
        'label',
        'notes',
        'status',
        'created_by',
    ];

    public function entries()
    {
        return $this->hasMany(CalendarEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }
}
