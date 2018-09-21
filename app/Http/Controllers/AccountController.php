<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\User;
use JWTAuth;
use App\Http\Controllers\PaymentController as Payment;

class AccountController extends Controller
{
    protected $payment;

    public function __construct(Payment $payment) //pass the Payment class to the constructor
    {
        $this->payment = $payment;
    }

    public function verifyAccountNumber(Request $request)
    {
        $bank_code = $request->bank_code;
        
        $acct_no = $request->acct_no;

        $result = $this->payment->addAccountNumber($acct_no, "$bank_code");

        if($result) 
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

        $bank_code = $request->bank_code;

        $bank_name = $this->payment->fetchBankName("$bank_code");

        $account_no = $request->account_no;

        if(Account::where('account_no', $account_no)->exists()){
            
          $result = [
            'status'=>true,
            'message'=>'Account has already been added',
          ];
        
          return response()->json($result, 200);
        }

        $acct_details =[
            'account_name'=>$request->account_name,
            'account_no'=> $account_no,
            'bank_name' => $bank_name,
            'bank_code' => $bank_code
        ];

        $acct = $user->accounts()->create($acct_details);  //store the user's account details

        $result = [
            'status'=>true,
            'message'=>'Account has been added successfully',
            'data'=>$acct
        ];

        return response()->json($result, 201);
    }

    public function fetchAllBanks()
    {

        $banks = $this->payment->fetchAllBanks(); //fetch all banks


        $result = [
            'status'=>true,
            'message'=> 'Retrieved all banks',
            'data'=> $banks
        ];

       return response()->json($result, 200);
    
   }

   public function fetchUserAccounts()
   {
       $user = JWTAuth::parseToken()->toUser();

       $user_accounts = $user->accounts; //fetch user accounts
       
       $result = [
        'status'=>true,
        'message'=> 'Retrieved all accounts',
        'data'=> $user_accounts
       ];

       return response()->json($result, 200);
   }

   public function deleteAccount(Account $account)
   {
     $this->authorize('touch', $account);
    
     $account->delete();

     $result = [
        'status'=>true,
        'message'=>'Account has been deleted',
    ];

     return response()->json($result, 200);

   }
}
