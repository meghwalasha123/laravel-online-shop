<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Api extends Model
{
    use HasFactory;

    public static function updateOnlinePayment($attributes)
    {
        $result = [];
        $order_status = '';
        $gateway_payment_id = '';

        if (!empty($attributes['payment_id'])) {
            $gateway_payment_id = $attributes['payment_id'];
        }
        if ($attributes['payment_method'] == 'PhonePe') {
            if (!empty($attributes['success'])) {
                if ($attributes['success'] == 1 || $attributes['success'] == true) {
                    $order_status = 'Success';
                }
            }
        }

        $status = 0;
        if ($order_status == 'Success') {
            $status = 1;
        }

        $order_id = '';
        if (!empty($attributes['order_id'])) {
            $order_id = $attributes['order_id'];
        }

        $order_detail = Order::where('gateway_order_id', '=', $order_id)->first();
        if(!empty($order_detail)){
            Order::where('gateway_order_id', '=', $order_id)->update(['payment_method' => 'phonepe', 'payment_status' => 'paid', 'status' => 'shipped']);
        }

        $result  = array(
            'msg'      =>   "Your Payment Successful and You have successfully placed order",
            'status'   =>   1,
        );

        return  $result;
    }

}
