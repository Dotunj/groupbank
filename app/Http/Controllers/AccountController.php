<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\User;
use JWTAuth;
use App\Http\Controllers\PaymentController as Payment;

class AccountController extends Controller
{

    public function verifyAccountNumber(Request $request)
    {
        $bank_code = $request->bank_code;
        
        $acct_no = $request->acct_no;

        $payment = new Payment();

        if($payment->addAccountNumber($acct_no, "$bank_code")) 
        {
           $acct_name = $result['data']['account_name'];

           $result = [
               'status'=>true,
               'message'=>'account resolved successfully',
               'data'=>$acct_name
           ];

           return response()->json($result, 200);
        } else {
            $result = [
               'status'=>false,
               'message'=>'Account does not exist',
            ];
           return response()->json($result, 422);
        }
    }


    public function addAccount(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        $acct_details =[
            'account_name'=>$request_account_name,
            'account_no'=> $request->account_no,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code
        ];

        $acct = $user->accounts()->create($acct_details);

        $result = [
            'status'=>true,
            'message'=>'Account has been added successfully',
            'data'=>$acct
        ];

        return response()->json($result, 201);
    }

    public function fetchAllBanks()
    {
        $payment = new Payment();

        $banks = $payment->fetchAllBanks(); //fetch all banks


        $result = [
            'status'=>true,
            'message'=> 'Retrieved all banks',
            'data'=> $banks
        ];

        return response()->json($result, 200);
    
   }
}
