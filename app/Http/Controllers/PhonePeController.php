<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Hash;

class PhonePeController extends Controller
{
    public function phonePe(){
        // dd($attributes);die;
        $data = [
            // "merchantTransactionId" => $attributes->transactionId,
            // "merchantUserId" => $attributes->user_id,
            // "amount" => round(('$attributes->grand_total'*100),2),
            "merchantId" => "PGTESTPAYUAT",
            "merchantTransactionId" => "MT7850590068188104",
            "merchantUserId" => "MUID123",
            "amount" => round((10*100),2),
            "redirectUrl" => "route('response')",
            "redirectMode" => "REDIRECT",
            "callbackUrl" => "route('response')",
            "mobileNumber" => "4884285565",
            "paymentInstrument" => [
            "type" => "PAY_PAGE"
            ]
        ];

        $encode = base64_encode(json_encode($data));

        $saltKey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
        $saltIndex = 1;

        $string = $encode.'/pg/v1/pay'.$saltKey;
        $sha256 = hash('sha256',$string);

        $finalXHeader = $sha256.'###'.$saltIndex;

        $response = Curl::to('https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay')
            ->withHeader('Content-Type:application/json')
            ->withHeader('X-VERIFY:'.$finalXHeader)
            ->withData(json_encode(['request' => $encode]))
            ->post();

        $rData = json_decode($response);

        return redirect()->to($rData->data->instrumentResponse->redirectInfo->url);

    }

    public function response(Request $request){
        $input = $request->all();
        dd($input);
        $saltKey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
        $saltIndex = 1;

        // $string = $encode.'/pg/v1/status/'.$input['merchantId'].'/'.$input['transactionId'].$saltKey;
        // $sha256 = hash('sha256',$string);
        // $finalXHeader = $sha256.'###'.$saltIndex;

        $finalXHeader =  hash('sha256','/pg/v1/status/'.$input['merchantId'].'/'.$input['transactionId'].$saltKey).'###'.$saltIndex;

        $response = Curl::to('https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/status/'.$input['merchantId'].'/'.$input['transactionId'])
            ->withHeader('Content-Type:application/json')
            ->withHeader('accept:application/json')
            ->withHeader('X-VERIFY:'.$finalXHeader)
            ->withHeader('X-MERCHANT-ID:'.$input['transactionId'])
            ->get();

        dd(json_decode($input));
    }
}
