<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;
use Validator;
use Auth;
use JWTAuth;

class PlanController extends Controller
{
    public function create(Request $request)
    {
       $rules =  [
            'name'=>'required',
            'amount'=>'required|numeric',
            'date_of_collection'=>'required' 
       ];

       $validator = Validator::make($request->all(), $rules);
       if($validator->fails()){
           return response()->json(['error'=>$validator->messages()]);
       }

       $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

       //creates a new plan for a user
       $plan = new Plan([
           'name'=>$request->name,
           'amount'=>$request->amount,
           'user_id'=>$user->id,
           'date_of_collection'=>$request->date_of_collection
       ]);

       $plan->save();

       return response()->json(['data'=>$user, 'status'=>'successfully created plan!'], 201);
    }
}
