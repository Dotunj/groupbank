<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function hasCard()
    {
        if(count($this->cards)){
            return true;
          }else {
            return false;
        }
    }

}
