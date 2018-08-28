<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'auth_code',
        'bin',
        'last_four',
        'card_type',
    ];

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        $this->hasMany(Subscription::class);
    }

}
