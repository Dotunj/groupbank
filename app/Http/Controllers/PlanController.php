<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;
use Validator;
use Auth;
use JWTAuth;
use Mail;
use App\Mail\PlanMember;

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
            'start_date'=>'required' 
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
           'start_date' => $request->start_date
       ]);

       return response()->json(['data'=>$plan, 'status'=>'successfully created plan!'], 201);
    }

    public function addMemberToPlan(Request $request, Plan $plan)
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        //$creator_name = $user->name; //fetch the name of the user creating the plan

        $email = $request->email;

       // dd($email);

        $rules = [
            'name'=> 'required',
            'email'=> 'required|email'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['error'=>$validator->messages()]);
        }

        Mail::to($email)->send(new PlanMember($user, $plan));

        return response()->json(['status'=>'successfully sent mail'], 200);

    }

    public function addMembersToPlanForm(Plan $plan)
    {
        return view('auth.register', compact('plan'));
    }
}
