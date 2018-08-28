<?php

namespace App\Exceptions;

use Exception;

class PaymentVerificationFailedException  extends Exception
{
    public function render()
    {
        return response()->json(['message'=>'Invalid Transaction Reference'], 404);
    }
}