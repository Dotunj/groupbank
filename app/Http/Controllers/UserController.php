<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;


class UserController extends Controller
{
   public function register(Request $request)
   {
       $rules = [
           'name'=>'required',
           'email'=>'required|email|unique:users',
           'password'=>'required'
       ];

       $validator = Validator::make($request->all(), $rules);
         if($validator->fails()){
             return response()->json($validator->messages());
         }

       $user = new User([
           'name'=>$request->name,
           'email'=>$request->email,
           'password'=>bcrypt($request->password)      
        ]);
      $user->save();

      $result = [
          'status'=>true,
          'message'=>'successfully created user!',
          'data'=>$user,
      ];
       
      return $this->login($request);
   }
   
   public function login(Request $request)
   {
    $rules = [
        'email'=>'required|email',
        'password'=>'required'
    ];

    $validator = Validator::make($request->all(), $rules);
      if($validator->fails()){
          return response()->json($validator->messages());
      }
       $credentials = $request->only('email', 'password');
       try{
          if(!$token = JWTAuth::attempt($credentials)){
              return response()->json(['message'=>'Invalid Credentials!'], 401);
          }
       }catch (JWTException $e){
           return response()->json(['error'=>'Could not create token!'], 500);
       }

       $result = [
        'status'=>true,
        'message'=>'successfully logged in user!',
        'token'=>$token,
       ];
       
       return response()->json($result, 200);
   
    }

    public function fetchUser()
    {
        $user = JWTAuth::parseToken()->toUser(); 

        $user_has_card = $user->hasCard(); //check if the user has a card or not

        $result = [
            'status'=>true,
            'message'=>'successfull',
            'data' => $user,
            'user_has_card'=>$user_has_card 
        ];

        return response()->json($result, 200);

    }

    public function logout(Request $request)
    {
         $this->validate($request, ['token'=> 'required']);

         try{
             JWTAuth::invalidate($request->input('token'));
             return response()->json(['success'=>true, 'message'=>"You have successfully logged out"]);
         } catch (JWTException $e) {
             //something went wrong while attempting to encode the token
             return response()->json(['success'=>false, 'message'=>'Failed to logout, please try again'], 500);
         }
    }

}
