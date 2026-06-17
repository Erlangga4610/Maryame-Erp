<?php

namespace App\Content\Models;

use Illuminate\Database\Eloquent\Model;

class QcCriteriaResult extends Model
{
    protected $table = 'qc_criteria_results';

    protected $fillable = [
        'qc_check_id',
        'criteria_no',
        'result',
        'note',
    ];

    public function qcCheck()
    {
        return $this->belongsTo(TiktokQc::class, 'qc_check_id');
    }
}
