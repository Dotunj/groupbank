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

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function subscription()
    {
      return $this->hasMany(Subscription::class);
    }
    
}
