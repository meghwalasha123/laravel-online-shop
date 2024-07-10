<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    
    public function success(Request $request){
        // dd($request->all());

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);
        // dd($response);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            $order_id = '';
            if (!empty($response['id'])) {
                $order_id = $response['id'];
            }

            $order_detail = Order::where('gateway_order_id', '=', $order_id)->first();
            if(!empty($order_detail)){
                Order::where('gateway_order_id', '=', $order_id)->update(['payment_method' => 'paypal', 'payment_status' => 'paid', 'status' => 'shipped']);
            }

            return redirect()->route('front.paymentStatus',$order_detail)->with('success', 'Your Payment Successful and You have successfully placed order');
        } else {
            // return redirect()->route('front.paymentStatus',$order_detail)->with('error', $response['message'] ?? 'Something went wrong');
            return redirect()->route('payment.cancel');
        }

    }

    public function cancel(Request $request){
    //    dd($request->all());
        if($request->token){

            $order_detail = Order::where('gateway_order_id', '=', $request->token)->first();
            if(!empty($order_detail)){
                Order::where('gateway_order_id', '=', $request->token)->update(['payment_method' => 'paypal', 'payment_status' => 'not paid', 'status' => 'pending']);
            }
            return redirect()->route('front.paymentStatus',$order_detail)->with('error', 'Your payment has been cancelled !!!');
        }  
    }
}
