<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'card_id'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
