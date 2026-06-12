<?php

namespace App\Content\Models;

use App\Content\Enums\CalendarEntryStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CalendarEntry extends Model
{
    protected $fillable = [
        'calendar_id',
        'content_id',
        'scheduled_date',
        'status',
        'sort_order',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'status' => CalendarEntryStatus::class,
    ];

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
