<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BkashController extends Controller
{
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;

    public function __construct()
    {

        // $config = [
        // 	"base_url" => "https://tokenized.sandbox.bka.sh/v1.2.0-beta/",
        // 	"app_key" => "7epj60ddf7id0chhcm3vkejtab",
        // 	"app_secret" => "18mvi27h9l38dtdv110rq5g603blk0fhh5hg46gfb27cp2rbs66f",
        //     "username" => "sandboxTokenizedUser01",
        // 	"password" => "sandboxTokenizedUser12345",
        // 	"amount"   => "2",
        // 	"merchantInvoiceNumber" => rand(0000,5000)
        // ];


        // bKash Merchant API Information

        // You can import it from your Database
        // $bkash_app_key = '5tunt4masn6pv2hnvte1sb5n3j'; // bKash Merchant API APP KEY
        // $bkash_app_secret = '1vggbqd4hqk9g96o9rrrp2jftvek578v7d2bnerim12a87dbrrka'; // bKash Merchant API APP SECRET
        // $bkash_username = 'sandboxTestUser'; // bKash Merchant API USERNAME
        // $bkash_password = 'hWD@8vtzw0'; // bKash Merchant API PASSWORD
        // $bkash_base_url = 'https://checkout.sandbox.bka.sh/v1.2.0-beta'; // For Live Production URL: https://checkout.pay.bka.sh/v1.2.0-beta



        $bkash_app_key = '7epj60ddf7id0chhcm3vkejtab'; // bKash Merchant API APP KEY
        $bkash_app_secret = '18mvi27h9l38dtdv110rq5g603blk0fhh5hg46gfb27cp2rbs66f'; // bKash Merchant API APP SECRET
        $bkash_username = 'sandboxTokenizedUser01'; // bKash Merchant API USERNAME
        $bkash_password = 'sandboxTokenizedUser12345'; // bKash Merchant API PASSWORD
        $bkash_base_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/'; // For Live Production URL: https://checkout.pay.bka.sh/v1.2.0-beta
        // $bkash_base_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout'; // For Live Production URL: https://checkout.pay.bka.sh/v1.2.0-beta

        $this->app_key = $bkash_app_key;
        $this->app_secret = $bkash_app_secret;
        $this->username = $bkash_username;
        $this->password = $bkash_password;
        $this->base_url = $bkash_base_url;
    }

    public function index()
    {
        # code...
        // $getToken = $this->getToken();
        // dd(session()->get('bkash_token'));

        return view('bkash-payment');
    }

    public function getToken()
    {
        session()->forget('bkash_token');

        $post_token = array(
            'app_key' => $this->app_key,
            'app_secret' => $this->app_secret
        );

        // $url = curl_init("$this->base_url/checkout/token/grant");
        $url = curl_init("$this->base_url/tokenized/checkout/token/grant");
        $post_token = json_encode($post_token);
        $header = array(
            'Content-Type:application/json',
            'Accept:application/json',
            "password:$this->password",
            "username:$this->username"
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $post_token);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);

        $response = json_decode($resultdata, true);
        // dd($response);

        if (array_key_exists('msg', $response)) {
            return $response;
        }

        session()->put('bkash_token', $response['id_token']);

        return response()->json(['success', true]);
    }

    public function createAgrement(Request $request)
    {
        $token = session()->get('bkash_token');

        // dd($token);
        $mode = '0000';
        $payerReference = '01932245768';
        // $callbackURL = 'https://merchantdemo.sandbox.bka.sh/';
        $callbackURL = 'http://127.0.0.1:8000/callback';

        $createagreementbody = array(
            'payerReference' => $payerReference,
            'callbackURL' => $callbackURL,
            'mode' => $mode,
        );



        $url = curl_init("$this->base_url/tokenized/checkout/create");
        $createagreementbodyx = json_encode($createagreementbody);
        $header = array(
            'Content-Type:application/json',
            "authorization: $token",
            "x-app-key: $this->app_key"
        );

        // dd($header);


        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $createagreementbodyx);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata, true);
    }

    public function createPayment(Request $request)
    {
        $agreementID =  session()->get('agreementID');
        $token = session()->get('bkash_token');

        $callbackURL = 'http://127.0.0.1:8000/payment-callback';

        $requestbody = array(
            'agreementID' => $agreementID,
            'mode' => '0001',
            'amount' => '10',
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => rand(),
            'callbackURL' => $callbackURL
        );


        $url = curl_init("$this->base_url/tokenized/checkout/create");
        $request_data_json = json_encode($requestbody);

        // dd($request_data_json);
        $header = array(
            'Content-Type:application/json',
            "authorization: $token",
            "x-app-key: $this->app_key"
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    public function executePayment($paymentID)
    {
        $token = session()->get('bkash_token');

        // $paymentID = $request->paymentID;


        $requestbody = array(
            'paymentID' => $paymentID
        );


        $url = curl_init("$this->base_url/checkout/payment/execute/" . $paymentID);
        $requestbodyJson = json_encode($requestbody);
        $header = array(
            'Content-Type:application/json',
            "authorization:$token",
            "x-app-key:$this->app_key"
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    public function queryPayment(Request $request)
    {
        $token = session()->get('bkash_token');
        $paymentID = $request->payment_info['payment_id'];

        $url = curl_init("$this->base_url/checkout/payment/query/" . $paymentID);
        $header = array(
            'Content-Type:application/json',
            "authorization:$token",
            "x-app-key:$this->app_key"
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    public function bkashSuccess(Request $request)
    {
        // IF PAYMENT SUCCESS THEN YOU CAN APPLY YOUR CONDITION HERE
        if ('Noman' == 'success') {

            // THEN YOU CAN REDIRECT TO YOUR ROUTE

            Session::flash('successMsg', 'Payment has been Completed Successfully');

            return response()->json(['status' => true]);
        }

        Session::flash('error', 'Noman Error Message');

        return response()->json(['status' => false]);
    }

    public function callback(Request $request)
    {
        $paymentID = $request->paymentID;
        $token = session()->get('bkash_token');

        $requestbody = array(
            'paymentID' => $paymentID
        );

        $url = curl_init("$this->base_url/tokenized/checkout/execute");
        $requestbodyJson = json_encode($requestbody);
        $header = array(
            'Content-Type:application/json',
            "authorization: $token",
            "x-app-key: $this->app_key"
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);
        $resultdata = json_decode($resultdata, true);

        if (isset($resultdata['statusCode'])) {
            if ($resultdata['statusCode'] == '0000' && $resultdata['agreementStatus'] == 'Completed') {
                session()->put('agreementID', $resultdata['agreementID']);
            }
        } else {
            dd($resultdata);
        }



        return redirect()->route('bkash');
    }

    public function paymentCallback(Request $request)
    {
        # code...
        // dd($request->all());
        return $this->executePayment($request->paymentID);
    }
}
