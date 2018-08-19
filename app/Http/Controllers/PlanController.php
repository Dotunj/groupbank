<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;
use App\User;
use Validator;
use Auth;
use JWTAuth;
use Mail;
use App\Mail\PlanMember;
use App\Mail\UserPlanMember;

class PlanController extends Controller
{
    public function index()
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        $plans = Plan::where('user_id', $user->id)->latest()->get();

        $result = [
            'status'=>true,
            'message'=>'successfully retrieved plans',
            'data'=>$plans, 
        ];

        return response()->json($result, 200);
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

       $result = [
           'status'=>true,
           'message'=>'successfully created plan',
           'data'=>$plan,
       ];

       return response()->json($result, 201);
    }

    public function sendUserEmail(Request $request, Plan $plan)
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        $email = $request->email;

        $rules = [
            'name'=> 'required',
            'email'=> 'required|email'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['error'=>$validator->messages()]);
        }

        if($this->userEmailExists($email)){
            Mail::to($email)->send(new PlanMember($user, $plan));
        } else {
            Mail::to($email)->send(new UserPlanMember($user, $plan));
        }

        $result = [
            'status'=>true,
            'message'=>'successfully sent mail',
        ];

        return response()->json($result, 200);

    }

    public function addMembersToPlanForm(Plan $plan)
    {

        return view('auth.register', compact('plan'));
    }

    public function subscribeNewUserToPlan(Request $request, Plan $plan)
    {
       //register new user and subscribe user to plan
       $rules = [
        'name'=>'required',
        'email'=>'required|email|unique:users',
        'password'=>'required'
      ];

     $validator = Validator::make($request->all(), $rules);
      if($validator->fails()){
          return response()->json(['error'=>$validator->messages()]);
        }

        $user = new User([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)      
         ]);
       $user->save();

       $user->plans()->attach([$plan->id]);

       $result = [
           'status'=>true,
           'message'=>'successfully created user and subscribed to plan!',
           'data'=>$user,
       ];

       return response()->json($result, 201);
    }

    public function subscribeRegisteredUserToPlan(Plan $plan)
    {
        
    }

    private function userEmailExists($email)  //function to check if user email exists
    {
        if(User::where('email', $email)->exists()){
            return true;
        }
    }
}
