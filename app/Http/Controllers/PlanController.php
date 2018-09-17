<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Plan;
use App\Card;
use App\Subscription;
use Validator;
use Auth;
use JWTAuth;
use Mail;
use App\Mail\PlanMember;
use App\Mail\UserPlanMember;
use App\Http\Controllers\PaymentController as Payment;

class PlanController extends Controller
{

    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        $plans = Plan::where('user_id', $user->id)->latest()->get();

        $result = [
            'status'=>true,
            'message'=>'successfully retrieved plans',
            'data'=>$plans, 
        ];        

        dd($plans->subscriptions);

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

       if(!$user->hasCard()){
           return response()->json(['message'=>'You need to add a card first before you can create a plan']);
       }else{
       //creates a new plan for a user
       $plan = new Plan([
           'name'=>$request->name,
           'amount'=>$request->amount,
           'user_id'=>$user->id,
       ]);
       $plan->save();
       }

       //create a schedule for the plan
       $plan->schedule()->create([
           'plan_id' => $plan->id,
           'start_date' => $request->start_date
       ]);

       //fetch user card 
       $card_id = Card::where('user_id', $user->id)->first()->get(['id']);

       $plan->subscription()->create([
          'user_id'=>$user->id,
          'plan_id'=>$plan->id,
          'card_id'=>$card_id,       
       ]);

       $result = [
           'status'=>true,
           'message'=>'Plan created Successfully',
           'data'=>$plan,
       ];

       return response()->json($result, 201);
    }

    public function sendUserEmail(Request $request, Plan $plan)
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        $email = $request->email;

        $rules = [
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
            'message'=>'Mail sent successfully',
        ];

        return response()->json($result, 200);

    }

    public function addMembersToPlanForm(Plan $plan)
    {

        return view('auth.register', compact('plan'));
    }

    public function registerNewUser(Request $request, Plan $plan)
    {
       //register new user 
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
           'message'=>'successfully created user!',
           'data'=>$user,
       ];

       return response()->json($result, 201);
    }

    public function subscribeNewUserToPlan(Plan $plan, $TnxRef)
    {
        //dd($plan);
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        $payment = new Payment();

        $result = $payment->verifyTransaction($TnxRef); //charge user's card

       // dd($result);

        $card_details= [
            'user_id'=> $user->id,
            'auth_code'=> $result['data']['authorization']['authorization_code'],
            'bin'=> $result['data']['authorization']['bin'],
            'last_four'=> $result['data']['authorization']['last4'],
            'card_type'=>$result['data']['authorization']['card_type'] 
        ];

        $card = $user->cards()->create($card_details); //store user's card to the DB


        //subscribe the user to the plan 
        $user->subscriptions()->create([
             'user_id'=> $user->id,
             'plan_id'=>$plan->id,
             'card_id'=>$card->id
        ]);

        $result = [
            'status'=>true,
            'message'=>'Youve been subscribed to the plan successfully',
            'data'=>$user
        ];

        
        return response()->json($result, 200);
    }

    public function fetchAllSubscribedUsersToAPlan(Plan $plan) //fetch all users subscribed to a plan
    {
        $this->authorize('touch', $plan);

        $subscribed_users = Subscription::where('plan_id', $plan->id)->get();

        foreach($subscribed_users as $subscription){
            echo $subscription->user->email;
        }
    }

    public function fetchSubscribedPlans() //fetch all plans a user is subscribed to
    {
        $user = JWTAuth::parseToken()->toUser(); 

        $subscribed_plans = Subscription::where('user_id', $user->id)->get();

        foreach($subscribed_plans as $subscribed_plan){
            echo $subscribed_plan->plan->name;
        }

    }

    private function userEmailExists($email)  //function to check if user email exists
    {
        if(User::where('email', $email)->exists()){
            return true;
        }
    }

}


