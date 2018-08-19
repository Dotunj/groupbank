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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            $plan->identifier = uniqid(true);
        });
    }

    public function getRouteKeyName()
    {
        return 'identifier';
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
