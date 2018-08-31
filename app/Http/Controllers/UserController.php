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

      return response()->json($result, 201);
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
              return response()->json(['message'=>'Email or Password is incorrect!'], 401);
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

}
