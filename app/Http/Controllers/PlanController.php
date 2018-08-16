<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;
use Validator;
use Auth;
use JWTAuth;

class PlanController extends Controller
{
    public function index()
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        $plans = Plan::where('user_id', $user->id)->latest()->get();

        return response()->json(['data'=>$plans, 'status'=>'successfully retrieved plans'], 200);
    }
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
       ]);

       $plan->save();
       
       //create a schedule for the plan
       $plan->schedule()->create([
           'plan_id' => $plan->id,
           'start_date' => $request->date_of_collection
       ]);

       return response()->json(['data'=>$plan, 'status'=>'successfully created plan!'], 201);
    }
}
