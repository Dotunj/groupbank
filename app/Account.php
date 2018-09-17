<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{

   use SoftDeletes;

   protected $fiilable = [
       'user_id',
       'account_name',
       'account_no',
       'bank_name',
       'bank_code',
   ];

   public function user()
   {
       return $this->belongsTo(User::class);
   }
}
