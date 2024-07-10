<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Product;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingCharge;
use App\Models\DiscountCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PhonePeController;
use App\CustomClass\PhonePeGateway;
use App\CustomClass\StripePayment;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
// use Stripe;
// use Illuminate\View\View;
// use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function addToCart(Request $request){

        $product = Product::with('product_images')->find($request->id);

        if($product == null){
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        if(Cart::count() > 0){
            // echo "Product already in cart";
            // Product found in cart
            // Check if this product already in the card
            // return as message that product already added in your cart
            // if product not found in the cart, than add product in cart

            $cartContent = Cart::content();
            $productAlreadyExist = false;
            foreach ($cartContent as $item) {
                if($item->id == $product->id){
                    $productAlreadyExist = true;
                }
            }

            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status = true;
                $message = '<strong>'.$product->title.'</strong> added in your cart successfully.';
                session()->flash('success', $message);
            } else {
                $status = false;
                $message = $product->title.' already added in cart';
            }

        }else{
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);

            $status = true ;
            $message = '<strong>'.$product->title.'</strong> added in your cart successfully.';
            session()->flash('success', $message);
        }


        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
    public function cart(){
        $cartContent = Cart::content();
        // dd($cartContent);
        $data['cartContent'] = $cartContent;
        return view('front.cart', $data);
    }

    public function updateCart(Request $request){
        $rowId = $request->rowId; 
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);

        $product = Product::find($itemInfo->id);

         // check qty available in stock

        if($product->track_qty == 'Yes'){
            if($qty <= $product->qty){
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
                session()->flash('success',$message);
            }else{
                $message = 'Requested Qty('.$qty.') not available in stock';
                $status = false;
                session()->flash('error',$message);
            }
        }else{
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully';
            $status = true;
            session()->flash('success',$message);
        } 
        
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteItem(Request $request){

        $itemInfo = Cart::get($request->rowId);

        if($itemInfo == null){
            $errorMessage = 'Item not found in cart';
            session()->flash('error', $errorMessage);
            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ]);
        }

        Cart::remove($request->rowId);
        $message = 'Item remove from cart successfully';
        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function checkout(){

        $discount = 0;

        // -- if cart is empty redirect to cart page
        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        // -- if user is not logged in then redirect to login page
        if(Auth::check() == false){

            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }

            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddress::where('user_id',Auth::user()->id)->first();

        session()->forget('url.intended');

        $countries = Country::orderBy('name', 'ASC')->get();

        $subTotal = Cart::subtotal(2,'.','');

        // Apply Discount here
        if (session()->has('code')) {  
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount/100)*$subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        // Calculate shipping here
        if ($customerAddress != '') {
            $userCountry = $customerAddress->country_id; 
            $shippingInfo = ShippingCharge::where('country_id',$userCountry)->first();

            $totalQty = 0;
            $totalShippingCharge = 0;
            $grandTotal = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }
    
            $totalShippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal = ($subTotal-$discount)+$totalShippingCharge;
        } else {
            $grandTotal = ($subTotal-$discount);
            $totalShippingCharge = 0;
        }

        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'discount' => $discount,
            'grandTotal' => $grandTotal
        ]);
    }

    public function processCheckout(Request $request){
      
        // Step - 1 Apply validation
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:4',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'please fix the errors',
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // Step - 2 save user address
        $user = Auth::user();
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]
        );

        $discountCodeId = 0;
        $promoCode = '';
        $shipping = 0;
        $discount = 0;
        $subTotal = Cart::subtotal(2,'.','');
    
        // Apply Discount here
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount/100)*$subTotal;
            } else {
                $discount = $code->discount_amount;
            }

            $discountCodeId = $code->id;
            $promoCode = $code->code;
        }
        // Calculate Shipping
        $shippingInfo = ShippingCharge::where('country_id', $request->country)->first();

        $totalQty = 0;
        foreach (Cart::content() as $item) {
            $totalQty += $item->qty;
        }

        if ($shippingInfo != null) {
            $shipping = $totalQty*$shippingInfo->amount;
            $grandTotal = ($subTotal-$discount)+$shipping;
        } else {
            $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
            $shipping = $totalQty*$shippingInfo->amount;
            $grandTotal = ($subTotal-$discount)+$shipping;
        }   

        // $order = new Order;
        // $order->subtotal = $subTotal;
        // $order->shipping = $shipping;
        // $order->grand_total = $grandTotal;
        // $order->discount = $discount;
        // $order->cuopon_code_id = $discountCodeId;
        // $order->cuopon_code = $promoCode;
        // $order->payment_status = 'not paid';
        // $order->status = 'pending';
        // $order->user_id = $user->id;
        // $order->first_name = $request->first_name;
        // $order->last_name = $request->last_name;
        // $order->email = $request->email;
        // $order->mobile = $request->mobile;
        // $order->country_id = $request->country;
        // $order->address = $request->address;
        // $order->apartment = $request->apartment;
        // $order->city = $request->city;
        // $order->state = $request->state;
        // $order->zip = $request->zip;
        // $order->notes = $request->order_notes;
        // $order->save();

        // // Step - 4 store order items in order items table
        //  foreach (Cart::content() as $item) {

        //     $orderItem = new OrderItem;
        //     $orderItem->product_id = $item->id;
        //     $orderItem->order_id = $order->id;
        //     $orderItem->name = $item->name;
        //     $orderItem->qty = $item->qty;
        //     $orderItem->price = $item->price;
        //     $orderItem->total = $item->price*$item->qty;
        //     $orderItem->save();

        //     // Update product stock
        //     $productData = Product::find($item->id);

        //     if ($productData->track_qty == 'Yes') {
        //         $currentQty = $productData->qty;
        //         $updatedQty = $currentQty-$item->qty;
        //         $productData->qty = $updatedQty;
        //         $productData->save();
        //     }
            
        // }

        // // Send Order Email
        // // orderEmail($order->id, 'customer');

        // session()->flash('success', 'You have successfully placed order.');

        // Cart::destroy();

        // session()->forget('code');
       
        // Step - 3 store data in orders table
        if ($request->payment_method == 'cod') {     

            return response()->json([
                'message' => 'Order save successfully.',
                'orderId' => $order->id,
                'status' => true
            ]);

        } else if ($request->payment_method == 'phonepe') {

            // $gateway_order_id = Str::random(20);
            $gateway_order_id = "ORD" . generateNDigitRandomNumber(12);  
            // $order->payment_method = 'phonepe';         
            $order->gateway_order_id = $gateway_order_id;          
            $order->save();
               
            // $result = [];
            $parameters = [
                'merchantTransactionId' => $gateway_order_id,
                'merchantUserId' => $user->id,
                'amount' => $grandTotal,
                'redirectUrl' => route("paymentresponsephonepe"),
                'callbackUrl' => route("paymentresponsephonepe"),
                'mobileNumber' => $request->mobile,
            ];
            // dd($parameters);

            $PhonePeGateway = new PhonePeGateway();
            $phonepe_data = $PhonePeGateway->request($parameters);                
    
            if ($phonepe_data['status'] == 1) {
                return response()->json([
                    'message' => 'Order save successfully.',
                    'redirectUrl' => $phonepe_data['url'],
                    'status' => true
                ]);
                
            } else {
                return response()->json([
                    'message' => 'Samething went wrong.',
                    'status' => false
                ]);                
            }               
                       
        } else if ($request->payment_method == 'paypal') {
           
            // $order->payment_method = 'paypal';
            // $order->save();
            
            $provider = new PayPalClient;
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);
            // dd($provider);
         
            $paypal_data = $provider->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $grandTotal
                        ]
                    ]
                ],
                "application_context" => [
                    "return_url" => route('payment.success'),
                    "cancel_url" => route('payment.cancel')
                ]
            ]);
            // dd($paypal_data);

            if ($paypal_data['status'] == 'CREATED'){
                $order->gateway_order_id = $paypal_data['id'];
                $order->save();
                return response()->json([
                    'message' => 'Order save successfully.',
                    'redirectUrl' => $paypal_data['links'][1]['href'],
                    'status' => true
                ]); 
            } 
            session()->flash('error', 'Samething went wrong, Please try again');
            
                       
        } else if ($request->payment_method == 'paytm') {
           
            $order->payment_method = 'paytm';
            $order->save();


        } else if ($request->payment_method == 'stripe') {

            $order = new Order;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->cuopon_code_id = $discountCodeId;
            $order->cuopon_code = $promoCode;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->country_id = $request->country;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->notes = $request->order_notes;
            $order->save();
    
            // Step - 4 store order items in order items table
             foreach (Cart::content() as $item) {
    
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price*$item->qty;
                $orderItem->save();
    
                // Update product stock
                $productData = Product::find($item->id);
    
                if ($productData->track_qty == 'Yes') {
                    $currentQty = $productData->qty;
                    $updatedQty = $currentQty-$item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();
                }
                
            }
    
            // Send Order Email
            // orderEmail($order->id, 'customer');
    
            session()->flash('success', 'You have successfully placed order.');
    
            Cart::destroy();
    
            session()->forget('code');

            $parameters = [
                'number' => $request->card_number,
                'exp_month' => $request->expiry_month,
                'exp_year' => $request->expiry_year,
                'cvc' => $request->cvv_code,
                'amount' => $grandTotal,
            ];
            // dd($parameters);

            $StripePayment = new StripePayment();
            $stripe_data = $StripePayment->request($parameters);                
    
            if ($stripe_data['status'] == 1) {
                return response()->json([
                    'message' => 'Order save successfully.',
                    'redirectUrl' => $stripe_data['url'],
                    'status' => true
                ]);
                
            } else {
                return response()->json([
                    'message' => 'Samething went wrong.',
                    'status' => false
                ]);                
            }               
                       
        }

    }

    // public function success(Request $request){
    //     $provider = new PayPalClient;
    //     $provider->setApiCredentials(config('paypal'));
    //     $paypalToken = $provider->getAccessToken();

    //     $response = $provider->capturePaymentOrder($request->token);
    //     // dd($response);

    //     if (isset($response['status']) && $response['status'] == 'COMPLETED') {
    //         return "Payment is successful!";
    //     } else {
    //         return redirect()->route('paypal.cancel');
    //     }

    // }
    // public function cancel(){
    //     return "Payment is canceled!";
    // }

    public function paymentResponsePhonePe(Request $request)
    {
        $result = [];
        $attributes = $request->all();
        // dd($attributes);
        $PhonePeGateway = new PhonePeGateway();
        $phonepe_response = $PhonePeGateway->response($attributes);
        // dd($phonepe_response);
        if (!empty($phonepe_response['payment_id'])) {
            $phonepe_response['payment_id'] = $phonepe_response['payment_id'];
            $phonepe_response['payment_method'] = 'PhonePe';
        } else {
            $phonepe_response['payment_id'] = 'failed';
            $phonepe_response['payment_method'] = 'PhonePe';
        }
        $result =   Api::updateOnlinePayment($phonepe_response);
        // dd($result);

        if (!empty($result['msg'])) {
            $msg = $result['msg'];
        }

        $order_id = '';
        if (!empty($phonepe_response['order_id'])) {
            $order_id = $phonepe_response['order_id'];
        }

        $orderId = Order::where('gateway_order_id', '=', $order_id)->first();
        
        if ($result['status'] == 1) {
            return redirect()->route('front.paymentStatus',$orderId)->with('success', $msg);
        } else {
            return redirect()->route('front.paymentStatus',$orderId)->with('error', $msg);
        }
    }

    public function paymentStatus($id){
        return view('front.payment-status', [
            'id' => $id
        ]);
    }

    public function thankyou($id){
        return view('front.thanks', [
            'id' => $id
        ]);
    }

    public function getOrderSummery(Request $request){

        $subTotal = Cart::subtotal(2,'.','');
        $discount = 0;
        $discountString =0;

        // Apply Discount here
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount/100)*$subTotal;
            } else {
                $discount = $code->discount_amount;
            }

            $discountString = '<div class="mt-4" id="discount-response">
                <strong>'.session()->get('code')->code.'</strong>
                <button class="btn btn-sm btn-danger" type="button" id="remove-discount"><i class="fa fa-times"></i></button>
            </div>';
        }      
        
        if ($request->country_id > 0) {            

            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();

            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if ($shippingInfo != null) {

                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);
            } else {
                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();

                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);
            }

        } else {
            return response()->json([
                'status' => true,
                'grandTotal' => number_format(($subTotal-$discount),2),
                'discount' => number_format($discount,2),
                'discountString' => $discountString,
                'shippingCharge' => number_format(0,2)
            ]);
        }

    }

    public function applyDiscount(Request $request){
        $code = DiscountCoupon::where('code', $request->code)->first();
        if ($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon',
            ]);
        }

        // Check if coupon start date is valid or not
        $now = Carbon::now();
        if ($code->starts_at != "") {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->starts_at); 

            if ($now->lt($startDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon',
                ]);
            }
        }
        if ($code->expires_at != "") {
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->expires_at); 

            if ($now->gt($endDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon'
                ]);
            }
        }

        // Max Uses ckeck
        if ($code->max_uses > 0) {
            $couponUsed = Order::where('cuopon_code_id', $code->id)->count();
            if ($couponUsed >= $code->max_uses) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon'
                ]);
            }
        }        

        // Max Uses User ckeck
        if ($code->max_uses_user > 0) {
            $couponUsedByUser = Order::where(['cuopon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
            if ($couponUsedByUser >= $code->max_uses_user) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already used this coupon.'
                ]);
            }
        }       

        // Min Amount condition check
        if ($code->min_amount > 0) {
            $subTotal = Cart::subtotal(2,'.','');
            if ($subTotal < $code->min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your min Amount must be $'.$code->min_amount.'.'
                ]);
            }
        }

        session()->put('code',$code);
        return $this->getOrderSummery($request);
    }

    public function removeCoupon(Request $request){
        session()->forget('code');
        return $this->getOrderSummery($request);
    }
 
}
