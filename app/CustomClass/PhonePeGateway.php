<?php
namespace App\CustomClass;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Ixudra\Curl\Facades\Curl;

class PhonePeGateway
{
    protected $merchantId = '';
    protected $saltKey = '';
    protected $saltIndex = '';
    // protected $hostURL = '';

    protected $testMode = true;
    // protected $liveHostURL = 'https://api.phonepe.com/apis/hermes';
    protected $liveHostURL = 'https://api.phonepe.com/apis/hermes/pg/v1';
    protected $testHostURL = 'https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1';
    
    function __construct()
    {
        if(config('phonepe_live_mode') == 1){
            $this->testMode = false;
        }
        $this->merchantId = "PGTESTPAYUAT";
        $this->saltKey = "099eb0cd-02cf-4e2a-8aca-3e6c6aff0399";
        $this->saltIndex = 1;
        // $this->hostURL = env('HOST_URL', '');
    }

    public function getHostURL()
    {
        return $this->testMode?$this->testHostURL:$this->liveHostURL;
    }

    public function request($parameters)
    {
        $result = [];
        $parameters = (object)$parameters;
        // dd($parameters);
        if(!empty($parameters->merchantTransactionId) && !empty($parameters->redirectUrl) && !empty($parameters->callbackUrl) && !empty($parameters->amount) && $parameters->amount > 0){

            $mobileNumber = '';
            if(!empty($parameters->mobileNumber)){
                $mobileNumber = $parameters->mobileNumber;
            }

            $amount = $parameters->amount;
            $amount = round(($amount*100),2);
            $data = array (
                'merchantId' =>  $this->merchantId,
                'merchantTransactionId' => $parameters->merchantTransactionId,
                'merchantUserId' => $parameters->merchantUserId,
                'amount' => $amount,
                'redirectUrl' => $parameters->redirectUrl,
                'redirectMode' => 'POST',
                'callbackUrl' => $parameters->callbackUrl,
                'mobileNumber' => $mobileNumber,
                'paymentInstrument' => 
                array (
                    'type' => 'PAY_PAGE',
                ),
            );            
            // dd($data);

            $encode = base64_encode(json_encode($data));  
            $string = $encode.'/pg/v1/pay'.$this->saltKey;
            $sha256 = hash('sha256',$string);
            $finalXHeader = $sha256.'###'.$this->saltIndex;

            // $curl = curl_init();
            // curl_setopt_array($curl, [
            //     CURLOPT_URL => $this->getHostURL()."/pay",
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 30,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "POST",
            //     CURLOPT_HTTPHEADER => [
            //         "Content-Type: application/json",
            //         "X-VERIFY: ".$finalXHeader,
            //         "accept: application/json"
            //     ],
            //     CURLOPT_POSTFIELDS => json_encode(['request' => $encode]),
            // ]);
            // $response = curl_exec($curl);
            // // dd($parameters, $response, $encode, $finalXHeader, $curl);
            // $err = curl_error($curl);
            // curl_close($curl);
            
            $url = $this->getHostURL()."/pay";
            $response = Curl::to($url)
                ->withHeader('Content-Type:application/json')
                ->withHeader('X-VERIFY:'.$finalXHeader)
                ->withHeader('accept:application/json')
                ->withData(json_encode(['request' => $encode]))
                ->post();    
            
            // if ($err) {
            //     $result = [
            //         'status' => 0,
            //         'msg' => 'Oops! Something went wrong please try again.',
            //     ];
            // } else {
                $response = json_decode($response);
                // dd($response);
                // return redirect()->to($response->data->instrumentResponse->redirectInfo->url);
                if(!empty($response->success) && $response->success == 1){
                    if(!empty($response->data->instrumentResponse->redirectInfo->url)){
                        $result = [
                            'status' => 1,
                            'response' => $response,
                            'url' => $response->data->instrumentResponse->redirectInfo->url,
                            'msg' => 'Success',
                        ];
                    }else{
                        $result = [
                            'status' => 0,
                            'msg' => 'Oops! Something went wrong please try again.',
                        ];
                    }
                }else{
                    $result = [
                        'status' => 0,
                        'msg' => 'Oops! Something went wrong please try again.',
                    ];
                }
            // }
        }else{
            $result = [
                'status' => 0,
                'msg' => 'Oops! Something went wrong please try again.',
            ];
        }
        // dd($result);
        return $result;
    }

    public function requestApp($parameters)
    {
        $result = [];
        $parameters = (object)$parameters;
        if(!empty($parameters->merchantTransactionId) && !empty($parameters->redirectUrl) && !empty($parameters->callbackUrl) && !empty($parameters->amount) && $parameters->amount > 0){

            $mobileNumber = '';
            if(!empty($parameters->mobileNumber)){
                $mobileNumber = $parameters->mobileNumber;
            }

            $amount = $parameters->amount;
            $amount = round(($amount*100),2);
            $data = array (
                'merchantId' =>  $this->merchantId,
                'merchantTransactionId' => $parameters->merchantTransactionId,
                'merchantUserId' => $parameters->merchantUserId,
                'amount' => $amount,
                'redirectUrl' => $parameters->redirectUrl,
                'redirectMode' => 'POST',
                'callbackUrl' => $parameters->callbackUrl,
                'mobileNumber' => $mobileNumber,
                'paymentInstrument' => 
                array (
                    'type' => 'PAY_PAGE',
                ),
            );
            
            $encode = base64_encode(json_encode($data));  
            $string = $encode.'/pg/v1/pay'.$this->saltKey;
            $sha256 = hash('sha256',$string);
            $finalXHeader = $sha256.'###'.$this->saltIndex;

            $result = [
                'status' => 1,
                'data' => $data,
                'encode' => $encode,
                'string' => $string,
                'sha256' => $sha256,
                'finalXHeader' => $finalXHeader,
                'saltKey' => $this->saltKey,
                'saltIndex' => $this->saltIndex,
                'msg' => 'Success',
            ];
            
        }else{
            $result = [
                'status' => 0,
                'msg' => 'Oops! Something went wrong please try again.',
            ];
        }
        return $result;
    }

    public function response($request)
    {
        $result = [];
        if(!empty($request)){
            if(!empty($request['merchantId']) && !empty($request['transactionId']) && !empty($this->saltKey) && !empty($this->saltIndex)){
                

                $finalXHeader = hash('sha256','/pg/v1/status/'.$request['merchantId'].'/'.$request['transactionId'].$this->saltKey).'###'.$this->saltIndex;

                // $curl = curl_init();
                // curl_setopt_array($curl, [
                //     CURLOPT_URL => $this->getHostURL().'/status/'.$request['merchantId'].'/'.$request['transactionId'],
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => "",
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 30,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => "GET",
                //     CURLOPT_HTTPHEADER => [
                //         "accept: application/json",
                //         "Content-Type: application/json",
                //         "X-VERIFY: ".$finalXHeader,
                //         "X-MERCHANT-ID: ".$request['merchantId']
                //     ],
                // ]);

                // $response = curl_exec($curl);
                // $err = curl_error($curl);
                // curl_close($curl);

                $response = Curl::to('https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/status/'.$request['merchantId'].'/'.$request['transactionId'])
                ->withHeader('Content-Type:application/json')
                ->withHeader('accept:application/json')
                ->withHeader('X-VERIFY:'.$finalXHeader)
                ->withHeader('X-MERCHANT-ID:'.$request['transactionId'])
                ->get();

                // if ($err) {
                //     $result = [
                //         'status' => 0,
                //         'msg' => 'Oops! Something went wrong please try again.',
                //     ];
                // } else {
                    $response = json_decode($response);
                    if($response->success == 0 || $response->success == 1){

                        $order_id = '';
                        $payment_id = '';
                        $msg = '';
                        if (!empty($response->data->merchantTransactionId)) {
                            $order_id = $response->data->merchantTransactionId;                    
                        }
                        if (!empty($response->data->transactionId)) {
                            $payment_id = $response->data->transactionId;                    
                        }

                        if (!empty($response->message)) {
                            $msg = $response->message;                    
                        }


                        $result = [
                                'status' => 1,
                                'success' => $response->success,
                                'response' => $response,
                                'order_id' => $order_id,
                                'payment_id' => $payment_id,
                                'msg' => $msg,
                            ];
                            
                    }else{
                        $result = [
                            'status' => 0,
                            'msg' => 'Oops! Something went wrong please try again.',
                        ];
                    }
                // }
            
            }else{
                $result = [
                    'status' => 0,
                    'msg' => 'Oops! Something went wrong please try again.',
                ];
            }
        }else{
            $result = [
                'status' => 0,
                'msg' => 'Oops! Something went wrong please try again.',
            ];
        }
        return $result;
    }

}