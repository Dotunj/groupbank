<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'plan_id',
        'start_date',
        'end_date'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

}
