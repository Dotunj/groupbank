<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Config;
use App\Exceptions\PaymentVerificationFailedException;

class PaymentController extends Controller
{
    protected $base_url;

    protected $client;

    protected $secret_key;

    public function __construct()
    {
        $this->base_url = getenv('PAYSTACK_PAYMENT_URL');

        $this->secret_key = getenv('PAYSTACK_SECRET_KEY');

        $this->setClient();
    }

    public function setClient()
    {
        $authBearer = 'Bearer '. $this->secret_key;
        $this->client = new Client([
            'base_uri'=> $this->base_url,
            'headers'=> [
                'Authorization'=>$authBearer,
                'Content-Type'=>'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }


    public function verifyTransaction($TnxRef)
    {
        $result = array();

        $response = $this->client->request('GET', '/transaction/verify/'. $TnxRef);

        $body = $response->getBody();

        if($body){
            $result = json_decode($body, true);
            if($result){
                if($result['data']['status'] == 'success'){
                   return $result;
                }else{
                    throw New PaymentVerificationFailedException; 
                }
            }
        }

    }

    public function chargeAuthorization()
    {
        $result = array();
        
        $response = $this->client->request('POST', 'transaction/charge_authorization', [
            'json'=> [
                'authorization_code'=>'AUTH_6kebmcdtew',
                 'email'=>'customer@email.com',
                 'amount'=> 100
            ]
        ]);

        $body = $response->getBody();

        $result =json_decode($body,true);

        dd($result);
    }

    public function fetchAllBanks()
    {
        $result = array();

        $response = $this->client->request('GET', '/bank');

        $body = $response->getBody();

        $result =json_decode($body,true);

        //dd($this->addAccountNumber(3113379206, "011"));

        return $result;
    }

    public function addAccountNumber($acct_no, $bank_code)
    {
        $result = array();

        $response = $this->client->request('GET', '/bank/resolve?account_number='. $acct_no .'&bank_code='.$bank_code);

        $body = $response->getBody();

        $result =json_decode($body,true);

        return $result; 
    }

    public function fetchBankName($bank_code)
    {
        $results = array();

        $response = $this->client->request('GET', '/bank');

        $body = $response->getBody();

        $results =json_decode($body,true);

        foreach($results['data'] as $result){
            if($result['code'] === $bank_code){
                return $result['name'];
            }
        }
    }
}
