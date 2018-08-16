<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
        'amount',
        'date_of_collection'
    ];

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }
    
}
