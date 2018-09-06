<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Card;
use JWTAuth;
use App\Http\Controllers\PaymentController as Payment;

class CardController extends Controller
{
    public function addCard($TnxRef)
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        // dd($user->hasCard());

        $payment = new Payment();

        $results = $payment->verifyTransaction($TnxRef); //charge user's card

        $card_details= [
            'user_id'=> $user->id,
            'auth_code'=> $results['data']['authorization']['authorization_code'],
            'bin'=> $results['data']['authorization']['bin'],
            'last_four'=> $results['data']['authorization']['last4'],
            'card_type'=>$results['data']['authorization']['card_type'] 
        ];

        $card = $user->cards()->create($card_details); //store user's card to the DB
    
        $result = [
            'status'=>true,
            'message'=>'Card has been added successfully',
            'data'=>$card
        ];
       
        return response()->json($result, 201);

    }

    public function allCards()
    {
        $user = JWTAuth::parseToken()->toUser(); //fetch the associated user

        $user_cards = $user->cards()->get(['id', 'last_four', 'card_type']); //fetch only specific columns from the card table
        
        $result = [
            'status'=>true,
            'message'=>'Retrieved all cards',
            'data'=>$user_cards 
        ];

        return response()->json($result, 200);

    }

    public function deleteCard($id)
    {
        $card = Card::findorFail($id);

        $this->authorize('touch', $card);

        $card->delete();

        $result = [
            'status'=>true,
            'message'=>'Card has been deleted',
        ];

        return response()->json($result, 200);
    }
}
