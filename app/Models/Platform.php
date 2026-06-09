<?php

namespace App\Models;

use App\Content\Models\Content;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = ['name', 'code', 'originality_strict', 'is_active'];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
