<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Models\Agreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BkashController extends Controller
{
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;

    public function __construct()
    {
        // Default values which will pass with each request:
        // Wallet : 01770618575
        // OTP : 123456
        // PIN : 12121

        $bkash_app_key = '7epj60ddf7id0chhcm3vkejtab'; // bKash Merchant API APP KEY
        $bkash_app_secret = '18mvi27h9l38dtdv110rq5g603blk0fhh5hg46gfb27cp2rbs66f'; // bKash Merchant API APP SECRET
        $bkash_username = 'sandboxTokenizedUser01'; // bKash Merchant API USERNAME
        $bkash_password = 'sandboxTokenizedUser12345'; // bKash Merchant API PASSWORD
        $bkash_base_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/'; // For Live Production URL: https://checkout.pay.bka.sh/v1.2.0-beta
        // $bkash_base_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout'; // For Live Production URL: https://checkout.pay.bka.sh/v1.2.0-beta


        // https://checkout.sandbox.bka.sh/v1.2.0-beta/

        // $bkash_base_url = 'https://checkout.sandbox.bka.sh/v1.2.0-beta/';
        // $bkash_app_key = '5nej5keguopj928ekcj3dne8p';
        // $bkash_app_secret = '1honf6u1c56mqcivtc9ffl960slp4v2756jle5925nbooa46ch62';
        // $bkash_username = 'testdemo';
        // $bkash_password = 'test%#de23@msdao';
        // $amount = '2';
        // $merchantInvoiceNumber = time() . uniqid();


        $this->app_key = $bkash_app_key;
        $this->app_secret = $bkash_app_secret;
        $this->username = $bkash_username;
        $this->password = $bkash_password;
        $this->base_url = $bkash_base_url;
    }

    public function index()
    {
        # code...
        $agreements = Agreement::active()->get();
        return view('bkash-payment')->with('agreements', $agreements);
    }

    public function getToken()
    {
        session()->forget('bkash_token');

        $post_token = array(
            'app_key' => $this->app_key,
            'app_secret' => $this->app_secret
        );

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


    public function createPaymentChechout(Request $request)
    {
        $token = session()->get('bkash_token');
        // dd($token);
        $mode = '0011';
        $payerReference = '01770618575';
        // $callbackURL = 'http://127.0.0.1:8000/checkout-callback';
        // $callbackURL = 'http://192.168.1.154:8080/bkash-tokenized-payment/public/checkout-callback';
        $callbackURL = 'http://192.168.1.154:8080/pgw-revamp/Payment.php';
        $createagreementbody = array(
            'payerReference' => $payerReference,
            'callbackURL' => $callbackURL,
            'mode' => $mode,
            'amount' => '10',
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => 'commonPayment001',
        );
        $url = curl_init("$this->base_url/tokenized/checkout/create");
        $createagreementbodyx = json_encode($createagreementbody);
        $header = array(
            'Content-Type:application/json',
            "authorization: $token",
            "x-app-key: $this->app_key"
        );

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



    public function createAgrement(Request $request)
    {
        $token = session()->get('bkash_token');
        $mode = '0000';
        $payerReference = '01770618575';
        $callbackURL = 'http://127.0.0.1:8000/agreement-callback';

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

    public function executeAgreement($paymentID)
    {
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

                Agreement::create([
                    'user_id' => Auth::id(),
                    'agreement_id' => $resultdata['agreementID']
                ]);
            }
        } else {
            dd($resultdata);
        }
        return redirect()->route('bkash');
    }

    public function agreementCallback(Request $request)
    {
        return $this->executeAgreement($request->paymentID);
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
        $url = curl_init("$this->base_url/tokenized/checkout/execute");
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
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    public function checkoutExecutePayment($paymentID)
    {
        $token = session()->get('bkash_token');
        // $token = 'eyJraWQiOiJvTVJzNU9ZY0wrUnRXQ2o3ZEJtdlc5VDBEcytrckw5M1NzY0VqUzlERXVzPSIsImFsZyI6IlJTMjU2In0.eyJzdWIiOiIwZTVjOGU4Ni0xMTQzLTQyNjEtOTJkYy02MTQwMTNhMmNkYTAiLCJhdWQiOiI2cDdhcWVzZmljZTAxazltNWdxZTJhMGlhaCIsImV2ZW50X2lkIjoiMDdmMTQ4NzgtMzAyMy00MDllLTk5MGItZWYzMGZmN2M1MjllIiwidG9rZW5fdXNlIjoiaWQiLCJhdXRoX3RpbWUiOjE2NzY4MzQxNTIsImlzcyI6Imh0dHBzOlwvXC9jb2duaXRvLWlkcC5hcC1zb3V0aGVhc3QtMS5hbWF6b25hd3MuY29tXC9hcC1zb3V0aGVhc3QtMV9yYTNuUFkzSlMiLCJjb2duaXRvOnVzZXJuYW1lIjoic2FuZGJveFRva2VuaXplZFVzZXIwMSIsImV4cCI6MTY3NjgzNzc1MiwiaWF0IjoxNjc2ODM0MTUyfQ.g01AVT36GsWxmXSGCwCiALh2kBVYMEdJ-xvJKfWpX2PXgEK4-NSLsfXY9cxJc6L0zsS6E6ENrj5TSDERha6TLapsDUYgRtz3KkJ0XvwxnvL1gvcLZ1GcCdOkswcuI_a_EKHiaLDFqwI493AmEtupZVp_AMuB2Sh13W36NdCxgsHcnDiAfUe3Kdq7pG_hlv60HPB2QvpYWNpoT7h-mzALV6YpzjEk-lMJvllaQ5UTgameX8ZjhcRCjbObLpuOy7unixwiFB9szcNqML81OEH72af9FaKmyc9FsLTZUM6th_nJIJY4OH7nTvhoARCAxGC-0YeKc3eZITvCMxdNjaS8iA';
        // $paymentID = $request->paymentID;
        $requestbody = array(
            'paymentID' => $paymentID
        );
        $url = curl_init("$this->base_url/tokenized/checkout/execute");
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
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }


    public function paymentCallback(Request $request)
    {
        return $this->executePayment($request->paymentID);
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

    public function removeAgreent($id)
    {
        $agreement = Agreement::find($id);
        if ($agreement) {
            $agreement = $agreement->delete();
        }
        return redirect()->route('bkash');
    }

    // http://127.0.0.1:8000/checkout-callback?paymentID=TR0011B01676831671225&status=success&apiVersion=1.2.0-beta
    public function checkoutCallback(Request $request)
    {
        $errData = [
            'code' => 200,
            'message' => 'transaction error.'
        ];

        if ($request->has('paymentID') && $request->has('status')) {
            if ($request->paymentID && $request->status == 'success') {
                // dd($request->all());

                return $this->checkoutExecutePayment($request->paymentID);
            } else if ($request->paymentID && $request->status == 'failure') {
                return response()->json($errData);
            } else {
                return response()->json($errData);
            }
        }
    }
}
