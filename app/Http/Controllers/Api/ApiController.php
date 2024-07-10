<?php

namespace App\Http\Controllers\Api;

use URL;
use Carbon\Carbon;
use App\Models\Api;
use App\Models\Bank;
use App\Models\Blog;
use App\Models\City;
use App\Models\Gift;
use App\Models\User;
use App\Models\Order;
use App\Models\Pages;
use App\Models\Skill;
use App\Models\State;
use App\Models\ApiLog;
use App\Models\Banner;
use App\Models\Notice;
use App\Models\Report;
use App\Models\Review;
use App\Models\Wallet;
use App\Models\ApiKeys;
use App\Models\Country;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\UserOtp;
use App\Models\BlogLike;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Follower;
use App\Models\Language;
use App\Models\Astrologer;
use App\Models\Prediction;
use App\Models\Suggestion;
use App\Models\CallHistory;
use App\Models\ChatChannel;
use App\Models\Testimonial;
use App\Models\UserAddress;
use App\Models\UserKundali;
use Illuminate\Support\Str;
use App\Models\LiveSchedule;
use App\Models\Notification;
use App\Models\OrderProduct;
use App\Models\ServiceOrder;
use App\Models\UserActivity;
use App\Models\VideoSection;
use Illuminate\Http\Request;
use App\Models\ConnectedUser;
use App\Models\PaytmChecksum;
use App\Models\ServiceAssign;
use App\Models\AstrologerGift;
use App\Models\BannerCategory;
use App\Models\CustomerRefund;
use App\Models\EmailTemplates;
use App\Models\KundaliPayment;
use App\CustomClass\VedicAstro;
use App\Models\AstrologerPrice;
use App\Models\AstrologerSkill;
use App\Models\ProductCategory;
use App\Models\RechargeVoucher;
use App\Models\ServiceCategory;
use App\Models\UserOderAddress;
use Illuminate\Validation\Rule;
use App\CustomClass\RazorpayApi;
use App\Models\UnregisteredUser;
use App\Models\UserNotification;
use App\Models\AstrologerGallery;
use App\Models\WithdrawalRequest;


use App\Models\AstrologerCategory;

use App\Models\AstrologerDocument;
use App\Models\AstrologerLanguage;
use App\Models\ChatChannelHistory;
use App\Models\RefundRequest;

use Illuminate\Support\Facades\DB;
use App\Console\Commands\MyCommand;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Backend\RazorpayPaymentController;
use App\CustomClass\CCAvenueGateway;
use App\CustomClass\PhonePeGateway;

use App\Models\Temp;

class ApiController extends Controller
{
    // public function __construct()
    // {
    // }


    public function welcome()
    {
        $master_data = [];
        $setting = getSettings();

        foreach ($setting as $v) {
            $master_data[$v['setting_name']] = $v['setting_value'];
        }
        $data = array(
            'company_name'                      => $master_data['company_name'],
            'email'                             => $master_data['email'],
            'postal_code'                       => $master_data['postal_code'],
            'city'                              => $master_data['city'],
            'state'                             => $master_data['state'],
            'country'                           => $master_data['country'],
            'address'                           => $master_data['address'],
            'mobile_no'                         => $master_data['mobile_no'],
            'firbase_api_token'                 => $master_data['firbase_api_token'],
            'google_map_api_key'                => $master_data['google_map_api_key'],
            'zego_api_id'                       => $master_data['zego_api_id'],
            'zego_secret_key'                   => $master_data['zego_secret_key'],
            'razorpay_id'                       => $master_data['razorpay_id'],
            'razorpay_key'                      => $master_data['razorpay_key'],
            'agora_api_id'                      => $master_data['agora_api_id'],
            'agora_api_certificate'             => $master_data['agora_api_certificate'],
            'send_bird_application_id'          => $master_data['send_bird_application_id'],
            'send_bird_api_token'               => $master_data['send_bird_api_token'],
            'client_id_prokerala'               => $master_data['client_id_prokerala'],
            'client_secret_prokerala'           => $master_data['client_secret_prokerala'],
            'vedic_astro_api_key'               => $master_data['vedic_astro_api_key'],
            'website_favicon'                   => asset(config('constants.setting_image_path') . $master_data['logo']),
            'back_website_logo'                 => asset(config('constants.setting_image_path') . $master_data['logo']),
            'logo'                              => asset(config('constants.setting_image_path') . $master_data['logo']),
            'youtube_link'                      => !empty($master_data['youtube_link']) ? $master_data['youtube_link'] : '',
            'linkedin_link'                     => !empty($master_data['linkedin_link']) ? $master_data['linkedin_link'] : '',
            'instagram_link'                    => !empty($master_data['instagram_link']) ? $master_data['instagram_link'] : '',
            'google_link'                       => !empty($master_data['google_link']) ? $master_data['google_link'] : '',
            'twitter_link'                      => !empty($master_data['twitter_link']) ? $master_data['twitter_link'] : '',
            'facebook_link'                     => !empty($master_data['facebook_link']) ? $master_data['facebook_link'] : '',
            'whatsapp_number'                   => !empty($master_data['whatsapp_number']) ? $master_data['whatsapp_number'] : '',
            'telephone'                         => !empty($master_data['telephone']) ? $master_data['telephone'] : '',
            'service_refund_duration'           => !empty($master_data['service_refund_duration']) ? $master_data['service_refund_duration'] : '',
            'share_chat_link'                   => !empty($master_data['share_chat_link']) ? $master_data['share_chat_link'] : '',
        );

        $result = array(
            'status' => 1,
            'data' => $data,
        );
        return response()->json($result);
    }

    public function customerDashbord(Request $request)
    {
        $api = saveapiLogs($request->all());

        $attributes = $request->all();
        $attributes['user_uni_id'] = !empty($attributes['user_uni_id'])? $attributes['user_uni_id'] : '';
        $attributes['offset'] = 0;
        $attributes['status'] = 1;
        $attributes['limit']     = config('constants.api_page_limit');
        $banners            =   Banner::where([['status', 1], ['banner_category_id', 1]])->get();
        foreach ($banners as $key => $value) {
            $banners[$key]->url = !empty($value->url) ? $value->url : '';
            $banners[$key]->title = !empty($value->title) ? $value->title : '';
            $imgPath = public_path(config('constants.banner_image_path'));
            if (!empty($value->banner_image) && file_exists($imgPath . $value->banner_image)) {
                $value->banner_image =    url(config('constants.banner_image_path') . $value->banner_image);
            } else {
                $value->banner_image =    asset(config('constants.default_banner_image_path'));
            }
        }



        $ServiceCategory    = Api::ServiceCategory($request);
        $ProductCategory    = Api::ProductCategory($request);
        $VideoSections      = Api::videoSections($request);
        //shi ye getAllBlog
        $getAllBlog         = Api::getAllBlog($attributes);
        $testimonials       = Testimonial::where('status', 1)->get();
        $notice             = Notice::where('status', 1)->where('type', 'app')->first();

        $attributes['limit']     = config('constants.api_page_limit');
        $attributes['type']      = 'live';

        //shi ye getAstroData
        $live_astrologers   = Api::getAstroDataForCustomer($attributes);
        // dd($live_astrologers);x
        $currentDate = Carbon::now()->addMinutes(config('constants.service_available_time'))->format('Y-m-d H:i:s');


        $avalable_service = (object) [];
        if (!empty($attributes['user_uni_id'])) {
            $serviceOrder = ServiceOrder::where([['customer_uni_id', $attributes['user_uni_id']], ['status', 'approved']]);
        $serviceOrder->where(DB::raw("CONCAT(`date`, ' ', `time`)"), '<=', $currentDate);
        $avalable_service = $serviceOrder->first();

            $last_call = CallHistory::with('astrologer')->where('customer_uni_id', $attributes['user_uni_id'])
                ->where('call_type', 'call')
                ->where('status', 'completed')
                ->where('is_review', '0')
                ->first();
        }

        $deshbord =  array(
            'banners' => $banners,
            'testimonials' => $testimonials,
            'live_astrologers' => $live_astrologers,
            'services' => $ServiceCategory,
            'blogs' => $getAllBlog,
            'product_categories' => $ProductCategory,
            'notice' => $notice,
            'videosection' => $VideoSections,
            // 'avalable_service' => !empty($avalable_service) ? $avalable_service :(object) [],
            'is_review' => !empty($last_call['astrologer_uni_id']) ? 1 : 0,
            'id_for_review' => !empty($last_call['astrologer_uni_id']) ? $last_call['astrologer_uni_id'] : '',
            'display_name_for_review' => !empty($last_call['astrologer']['display_name']) ? $last_call['astrologer']['display_name'] : '',
        );

if(!empty($avalable_service)) {
    $deshbord['avalable_service'] = $avalable_service ;
}

        if (!empty($last_call['astrologer_uni_id'])) {
            $last_call->update(['is_review' => 1]);
        }

        if ($deshbord) {
            $result = array(
                'status' => 1,
                'data' => $deshbord,
                'msg'     => 'Dashboard data',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Something went wrong',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public  function test(Request $request)
    {

        $attributes = $request->all();

        $dob        =  !empty($attributes['dob']) ? $attributes['dob'] : '';
        $tob        =  !empty($attributes['tob']) ? $attributes['tob'] : '';
        $lat        =  !empty($attributes['lat']) ? $attributes['lat'] : '';
        $lon        =  !empty($attributes['lon']) ? $attributes['lon'] : '';
        $tz        =  !empty($attributes['tz']) ? $attributes['tz'] : '';
        $div        =  !empty($attributes['div']) ? $attributes['div'] : '';
        $color        =  !empty($attributes['color']) ? $attributes['color'] : '';
        $style        =  !empty($attributes['style']) ? $attributes['style'] : '';
        $font_size        =  !empty($attributes['font_size']) ? $attributes['font_size'] : '';
        $font_style        =  !empty($attributes['font_style']) ? $attributes['font_style'] : '';
        $colorful_planets        =  !empty($attributes['colorful_planets']) ? $attributes['colorful_planets'] : '';
        $size        =  !empty($attributes['size']) ? $attributes['size'] : '';
        $stroke        =  !empty($attributes['stroke']) ? $attributes['stroke'] : '';
        $lang        =  !empty($attributes['lang']) ? $attributes['lang'] : '';

        $vedicAstro =  new vedicAstro();
        $chartImage = $vedicAstro->chartImage($dob, $tob, $lat, $lon, $tz, $div, $color, $style, $font_size, $font_style, $colorful_planets, $size, $stroke, $lang);
        $result = array(
            'status' => 1,
            'data' => $chartImage,
            'msg'     => 'Dashboard data',
        );
        return response()->json($result);
        // echo date('Y-m-d H:i:s');
        // die;
        // echo 'dddddd';die;
        // $data   =    Api::getCustomerDetailByMobile('9352998149');
        // $array =  array('status'        // echo date('Y-m-d H:i:s');
        // die;
        // echo 'dddddd';die;
        // $data = Api::exotelIncomingCallRequestCurl('7690999966');
        // $array =  array('status' => '0', 'msg' => 'Please Update your application on playstore.');
        // pr($arry);die;
        // echo json_encode($array);die;
    }

    function otpSend(Request $request)
    {
        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'phone'     => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes = $request->all();
        $user_phone = $attributes['phone'];


        if (!empty(Config::get('sms_live_mode'))) {
            $rand_number = rand(100000, 999999);
            $msg =  $rand_number . ' is OTP for ' . Config::get('company_name') . ' login and valid for the next 30 minutes';
            MyCommand::send_sms($user_phone, $msg);
        } else {
            $rand_number = config('constants.default_otp_code');
        }

        $expires_at = date('Y-m-d H:i:s', strtotime('30 minutes'));


        $user_otp_array = array('phone' => $user_phone,  'otp' => $rand_number, 'expires_at' => $expires_at);
        $data = UserOtp::where('phone', "=", $user_phone);

        if ($data->count() > 0) {
            $res = UserOtp::where('phone', $user_phone)->update($user_otp_array);
        } else {
            $res = UserOtp::create($user_otp_array);
        }


        if ($res) {
            $result = array(
                'status' => 1,
                'data' => $user_otp_array,
                'msg'     => 'Your OTP is Send Successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Something went wrong',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    // this is a customer login
    public function customerLogin(Request $request)
    {

        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'phone'     => ['required'],
            'otp'     => ['required'],
            'user_ios_token'     => ['nullable'],
            'user_fcm_token'     => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes = $request->all();

        $user_otp = $attributes['otp'];
        $user_phone = $attributes['phone'];

        $res = UserOtp::where('phone', "=", $user_phone)->first();

        $current_time = date('Y-m-d H:i:s');
        if (!empty($res)) {
            if (($res->otp == $user_otp &&  $res->expires_at > $current_time) || $user_otp == config('constants.default_otp_code')) {

                $saveData = [];
                $user = User::where('phone', "=", $user_phone)->where('role_id', "=", config('constants.customer_role_id'))->where('trash', "=", '0')->first();
                if (empty($user)) {
                    $customer['customer_uni_id']  = new_sequence_code('CUS');
                    $array_astro = [
                        "customer_uni_id"  => $customer['customer_uni_id'],
                    ];

                    $customer =  Customer::create($array_astro);

                    $role_id['role_id']  = config('constants.customer_role_id');
                    $user['user_uni_id']   =  $customer['customer_uni_id'];
                    $array_admin = array(
                        'user_uni_id'           => $user['user_uni_id'],
                        'role_id'               => $role_id['role_id'],
                        'phone'                 => $user_phone,
                        'user_ios_token'        => !empty($attributes['user_ios_token']) ? $attributes['user_ios_token'] : "",
                        'user_fcm_token'        => !empty($attributes['user_fcm_token']) ? $attributes['user_fcm_token'] : "",
                        'status'                => '1',
                        'trash'                 => '0'
                    );


                    $user =  User::create($array_admin);
                    if (!empty($user->user_uni_id)) {
                        customerWelcomeBonus($user->user_uni_id);
                    }
                }

                if (!empty($user['status']) && intval($user['status']) == 1) {

                    $data   =    Api::getUserData($request, true);
                    //   dd($data);
                    if (!empty($attributes['user_fcm_token'])) {
                        $saveData['user_fcm_token']   =  $attributes['user_fcm_token'];
                    }
                    if (!empty($attributes['user_ios_token'])) {
                        $saveData['user_ios_token']   =  $attributes['user_ios_token'];
                    }
                    if (!empty($saveData)) {
                        $user->update($saveData);
                    }

                    $data['user_api_key'] = generateUserApiKey($data['customer_uni_id']);
                    $result = array(
                        'status' => 1,
                        'data' => $data,
                        'msg'     => 'You are Logged in Successfully',
                    );
                } else {
                    $result = array(
                        'status' => 0,
                        'msg'     => 'Your account is inactive. Please contact to admin.',
                    );
                }
            } else {
                $result = array(
                    'status' => 0,
                    'msg'     => 'Incorrect Otp Entered.',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Please generate OTP first.',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    // this is a astrologer login
    public function astrologerLogin(Request $request)
    {

        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'phone'     => ['required'],
            'otp'     => ['required'],
            'user_ios_token'     => ['nullable'],
            'user_fcm_token'     => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes = $request->all();
        $user_phone = $attributes['phone'];
        $user_otp = $attributes['otp'];

        $res = UserOtp::where('phone', "=", $user_phone)->first();
        $current_time = date('Y-m-d h:i:s');
        if (!empty($res)) {
            if (($res->otp == $user_otp &&  $res->expires_at > $current_time) || $user_otp == config('constants.default_otp_code')) {

                $saveData = [];
                $user = User::where('phone', "=", $user_phone)->where('role_id', "=", config('constants.astrologer_role_id'))->where('trash', "=", '0')->first();
                if (empty($user)) {
                    $astrologer['astrologer_uni_id']  = new_sequence_code('ASTRO');
                    $array_astro = [
                        "astrologer_uni_id"  => $astrologer['astrologer_uni_id'],
                    ];

                    $astrologer =  Astrologer::create($array_astro);

                    $role_id['role_id']  = config('constants.astrologer_role_id');
                    $user_astrologer   =  $astrologer['astrologer_uni_id'];

                    $array_admin = array(
                        'user_uni_id'           => $astrologer['astrologer_uni_id'],
                        'role_id'               => $role_id['role_id'],
                        'phone'                 => $user_phone,
                        'status'                => '0',
                        'user_ios_token'        => !empty($attributes['user_ios_token']) ? $attributes['user_ios_token'] : "",
                        'user_fcm_token'        => !empty($attributes['user_fcm_token']) ? $attributes['user_fcm_token'] : "",
                        'trash'                 => '0'
                    );
                    $user = User::create($array_admin);
                }

                $astrologer = Astrologer::where('astrologer_uni_id', $user['user_uni_id'])->first();

                if (empty($astrologer['process_status']) || (intval($astrologer['process_status']) < 4) || (!empty($user['status']) && intval($user['status']) == 1 && intval($astrologer['process_status']) == 4)) {
                    $filter = [];
                    $filter['astrologer_uni_id'] = $user['user_uni_id'];
                    $data = Api::getAstroData($filter, 1);
                    if (!empty($attributes['user_fcm_token'])) {
                        $saveData['user_fcm_token']   =  $attributes['user_fcm_token'];
                    }
                    if (!empty($attributes['user_ios_token'])) {
                        $saveData['user_ios_token']   =  $attributes['user_ios_token'];
                    }

                    if (!empty($saveData)) {
                        $user->update($saveData);
                    }

                    $data['user_api_key'] = generateUserApiKey($user['user_uni_id']);
                    $test =  Api::userActivity('login', $user['user_uni_id']);


                    $result = array(
                        'status' => 1,
                        'data' => $data,
                        'msg'     => 'You are Logged in Successfully',
                    );
                } else {

                    $result = array(
                        'status' => 0,
                        // 'data' => $data,
                        'msg'     => 'Your account is inactive. Please contact to admin.',
                    );
                }
            } else {
                $result = array(
                    'status' => 0,
                    'msg'     => 'Incorrect Otp Entered.',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Please generate OTP first.',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function getAllAstrologer(Request $request)
    {
        // dd($request);
        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'user_uni_id'   => ['nullable'],
            'search'        => ['nullable'],
            'gender'        => ['nullable'],
            'language'      => ['nullable'],
            'category'      => ['nullable'],
            'skill'         => ['nullable'],
            'type'          => ['nullable'],
            'user_ios_token' => ['nullable'],
            'user_fcm_token' => ['nullable'],
            'sortby'        => ['nullable'],
            'offset'        => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes = $request->all();

        $limit = config('constants.api_page_limit');
        $attributes['limit'] = $limit;
        $attributes['offset'] = !empty($attributes['offset']) ? $attributes['offset'] : '0';
        $attributes['status'] = 1;
        $astrologers =    Api::getAstroDataForCustomer($attributes);
        $astro_count     =  $astrologers->count();
        if ($astro_count > 0) {
            $you_are_in_queue = [];
            if (!empty($user_uni_id)) {
                $you_are_in_queue = Api::getCustomerQueueList($user_uni_id);
            }
            $result = array(
                'status' => 1,
                'msg' => 'success',
                'count' => $astro_count,
                'offset'    => $attributes['offset'] + $limit,
                'you_are_in_queue' => $you_are_in_queue,
                'data' => $astrologers,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg' => 'No Records Found',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function getFollowing(Request $request)
    {

        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'api_key'       => ['nullable'],
            'user_uni_id'   => ['nullable'],
            'offset'        => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }


        $attributes = $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        
        $attributes['following_id'] = $attributes['user_uni_id'];
        unset($attributes['user_uni_id']);

        $limit = config('constants.api_page_limit');
        $attributes['limit'] = $limit;
        $attributes['status'] = 1;
        $attributes['offset'] = !empty($attributes['offset'])? $attributes['offset'] : 0;

        $astrologers =    Api::getAstroDataForCustomer($attributes);
        $astro_count     =  $astrologers->count();
        if ($astro_count > 0) {
            $result = array(
                'status' => 1,
                'count' => $astro_count,
                'offset'    => $attributes['offset'] + $limit,
                'data' => $astrologers,
                'msg' => 'success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg' => 'No Records Found',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    // this is a getChatAstrologer
    public function getChatAstrologer(Request $request)
    {
        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'gender'             => ['nullable'],
            'skill'              => ['nullable'],
            'language'           => ['nullable'],
            'categoy'            => ['nullable'],
            'skill'              => ['nullable'],
            'user_ios_token'     => ['nullable'],
            'user_fcm_token'     => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes = $request->all();


        $gender =   '';

        $chat_astrologer = User::join('astrologers', 'users.user_uni_id', '=', 'astrologers.astrologer_uni_id')->join('astrologer_skills', 'astrologers.id', '=', 'astrologer_skills.astrologer_id')->join('astrologer_languages', 'astrologers.id', '=', 'astrologer_languages.astrologer_id');
        // dd(getQueryWithBindings($chat_astrologer));

        if (!empty($request->gender)) {
            $gender = $request->gender;
            $chat_astrologer->where(function ($query) use ($gender) {
                $query->Where('astrologers.gender', 'LIKE', '%' . $gender . '%')->orWhere('astrologers.phone', 'LIKE', '%' . $gender . '%');
            });
        }
        if (!empty($request->skill)) {
            $skill = $request->skill;
            $chat_astrologer->where('astrologer_skills.skill_id', $skill);
        }

        if (!empty($request->language)) {
            $language = $request->language;
            $chat_astrologer->where('astrologer_languages.language_id', $language);
        }
        //dd(getQueryWithBindings($chat_astrologer));
        $chat_count     =  $chat_astrologer->count();
        $astrolozers = $chat_astrologer->get();
        if ($chat_count > 0) {
            $result = array(
                'stuts' => 1,
                'count' => $chat_count,
                'data' =>  $astrolozers,
                'msg' => 'success'
            );
        } else {
            $result = array(
                'stuts' => 0,
                'msg' => 'empty'
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function getLiveAstrologer(Request $request)
    {
        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'id'                 => ['nullable'],
            'user_api_key'       => ['nullable'],
            'gender'     => ['nullable'],
            'skill'     => ['nullable'],
            'language'     => ['nullable'],
            'categoy'     => ['nullable'],
            'skill'     => ['nullable'],
            'user_ios_token'     => ['nullable'],
            'user_fcm_token'     => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes = $request->all();

        $skill =   '';

        $live_astrologer = User::join('astrologers', 'users.user_uni_id', '=', 'astrologers.astrologer_uni_id')->join('astrologer_skills', 'astrologers.id', '=', 'astrologer_skills.astrologer_id')->join('astrologer_languages', 'astrologers.id', '=', 'astrologer_languages.astrologer_id');

        if (!empty($request->skill)) {
            $skill = $request->skill;
            $live_astrologer->where(function ($query) use ($skill) {
                $query->Where('astrologer_skills.skill_id', 'LIKE', '%' . $skill . '%');
            });
        }

        if (!empty($request->language)) {
            $language = $request->language;
            $live_astrologer->where('astrologer_languages.language_id', $language);
        }
        $live_count     =  $live_astrologer->count();
        $astrolozers = $live_astrologer->get();
        if ($live_count > 0) {
            $result = array(
                'stuts' => 1,
                'count' => $live_count,
                'data' =>  $astrolozers,
                'msg' => 'success'
            );
        } else {
            $result = array(
                'stuts' => 0,
                'msg' => 'empty'
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    // this is a astrologerProfileDetail
    public function getAstrologerDetail(Request $request)
    {
        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'astrologer_uni_id' => ['nullable'],
            'user_id'            => ['nullable'],
            'user_ios_token'     => ['nullable'],
            'user_fcm_token'     => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes = $request->all();

        $limit = config('constants.api_page_limit');
        $attributes['limit'] = $limit;
        $attributes['offset'] = 0;
        $data =    Api::getAstroDataForCustomer($attributes, 1);
        if ($data) {
            $result = array(
                'status' => 1,
                'data' =>  $data,
                'msg' => 'Astrologer updated successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is a loginAstroDetails
    public function getAstroDashbord(Request $request)
    {

        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'api_key'             => ['nullable'],
            'astrologer_uni_id'   => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes = $request->all();

        $api_key = $attributes['api_key'];
        $user_uni_id = $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $currentDate = Carbon::now()->addMinutes(config('constants.service_available_time'))->format('Y-m-d H:i:s');
        $serviceOrder = ServiceOrder::where([['astrologer_uni_id', $attributes['astrologer_uni_id']], ['status', 'approved']]);
        $serviceOrder->where(DB::raw("CONCAT(`date`, ' ', `time`)"), '<=', $currentDate);
        $avalable_service = $serviceOrder->first();
        // dd(getQueryWithBindings($serviceOr));

        $data =    Api::getAstroData($attributes, 1);

        $ysday = date('Y-m-d', strtotime('-1 day'));
        $currentdate = date('Y-m-d');
        $amount_balance = Api::astroIncome($user_uni_id, $ysday, $ysday);
        $yesterday_earning = $amount_balance;


        $amount_balance = Api::astroIncome($user_uni_id, $currentdate, $currentdate);
        $today_earning = $amount_balance;

        $amount_balance = Api::astroIncome($user_uni_id);
        $total_earning = $amount_balance;
        $total_balance =  Api::getTotalBalanceById($user_uni_id);


        if (!empty($data)) {
            $data['follows'] = 0;
            $data['today_earning'] = !empty($today_earning) ? round($today_earning, 2) : 0;
            $data['yesterday_earning'] = !empty($yesterday_earning) ? round($yesterday_earning, 2) : 0;
            $data['total_earning'] = !empty($total_earning) ? round($total_earning, 2) : 0;
            $data['total_balance'] = !empty($total_balance) ? round($total_balance, 2) : 0;
            $data['avalable_service'] = !empty($avalable_service) ? $avalable_service : '';
            $data['notice'] = Notice::where('status', 1)->first();
            $result = array(
                'status' => 1,
                'data' =>  $data,
                'msg' => 'You are Logged in Successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "No Record Found",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is getNotification
    public function getNotification(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        if (empty($request->page)) {
            $request->page = 1;
        }
        $thismodel = UserNotification::where('user_uni_id', $user_uni_id);
        $page_limit = config('constants.api_page_limit');
        $offset = ($request->page - 1) * $page_limit;

        $thismodel->offset($offset)->limit($page_limit);
        $thismodel->orderBy('id', 'DESC');
        $notifications = $thismodel->get();
        // dd($notifications);
        if (!empty($notifications)) {
            $result = array(
                'status'     => 1,
                'data'         => $notifications,
                'msg'         => "View Notification!",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "No Record found",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is a saveAsrologer other data
    public function saveAstrologerStep3(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'pan_no' => ['nullable'],
            // 'pan_no' => ['nullable', 'min:' . config('constants.pan_number_lenght'), 'max:' . config('constants.pan_number_lenght')],
            'bank_name' => ['required'],
            'account_no' => ['required'],
            'account_type' => ['required'],
            'ifsc_code' => ['required'],
            'account_name' => ['required'],
            'account_type' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];

        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $user = User::where('user_uni_id', $astrologer_uni_id)->first();
        $astro = Astrologer::where('astrologer_uni_id', $astrologer_uni_id)->first();
        $bankAlready = Bank::where('astrologer_id', $astrologer_uni_id)->first();

        $saveData = array(
            'astrologer_id'          =>  $attributes['astrologer_uni_id'],
            'bank_name'              => $attributes['bank_name'],
            'account_no'             => $attributes['account_no'],
            'account_type'           => $attributes['account_type'],
            'ifsc_code'              =>  $attributes["ifsc_code"],
            'account_name'           =>  $attributes["account_name"],
            'account_type'           =>  $attributes["account_type"],
        );
        if (!empty($bankAlready)) {
            Bank::where('astrologer_id', $astrologer_uni_id)->update($saveData);
        } else {
            $bank =  Bank::create($saveData);
        }

        // $astro = Astrologer::where('astrologer_uni_id', $astrologer_uni_id)->first();
        if (!empty($astro) && $astro->process_status < 3) {
            $process_update['process_status'] = 3;
            $astro->update($process_update);
        }

        $saveUserData['pan_no'] = $attributes['pan_no'];
        $user->update($saveUserData);

        $filter = [];
        $filter['astrologer_uni_id'] = $astrologer_uni_id;
        $res = Api::getAstroData($filter, 1);
        $result = array(
            'status' => 1,
            'data' => $res,
            'msg' => 'bank are createsd Success',
        );

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is astrologer file upload
    public function saveAstrologerStep4(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'astro_img' => ['required'],
            'documents.*.document_type' => ['required'],
            'documents.*.front' => ['required', 'mimes:jpg,jpeg,png,bmp,webp,gif,tiff', 'max:4096'],
            'documents.*.back' => ['nullable', 'mimes:jpg,jpeg,png,bmp,webp,gif,tiff', 'max:4096'],
            'galleries.*' => ['nullable', 'mimes:jpg,jpeg,png,bmp,webp,gif,tiff', 'max:4096'],
        ], [
            'documents.*.document_type.required' => 'The document type field is required for all items.',
            'documents.*.front.required' => 'The front image field is required for all items.',
            'documents.*.front.mimes' => 'The front image file must be one of the following types: :values for all items.',
            'documents.*.front.max' => 'The front image file must not be greater than :values for all items.',
            'documents.*.back.mimes' => 'The back image file must be one of the following types: :values for all items.',
            'documents.*.back.max' => 'The back image file must not be greater than :values for all items.',
            'galleries.*.mimes' => 'The gallery image file must be one of the following types: :values for all items.',
            'galleries.*.max' => 'The gallery image file must not be greater than :values for all items.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();
        $imageCount = !empty($attributes['galleries']) ? count($attributes['galleries']) : 0;
        $api_key       =   $attributes['api_key'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];

        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }


        $astro = Astrologer::where('astrologer_uni_id', $astrologer_uni_id)->first();
        $alreadyImageCount = AstrologerGallery::where('astrologer_uni_id', $request->astrologer_uni_id)->count();
        $totalImageCount = $alreadyImageCount + $imageCount;
        $canUpload = env('GELLERY_COUNT') - $alreadyImageCount;

        if (empty(env('GELLERY_COUNT')) || (!empty(env('GELLERY_COUNT')) && $totalImageCount <= env('GELLERY_COUNT'))) {
            $saveDate = [];

            if (!empty($attributes['documents'])) {

                foreach ($attributes['documents'] as $key => $doc) {
                    $front = $back = '';
                    if (!empty($doc['front'])) {
                        $imgKey     = 'documents.' . $key . '.front';
                        $imgPath    =   public_path(config('constants.astrologer_doc_image_path'));
                        $filename   = UploadImage($request, $imgPath, $imgKey);
                        if (!empty($filename)) {
                            $front = $filename;
                        }
                    }
                    if (!empty($doc['back'])) {
                        $imgKey     = 'documents.' . $key . '.back';;
                        $imgPath    =   public_path(config('constants.astrologer_doc_image_path'));
                        $filename   = UploadImage($request, $imgPath, $imgKey);
                        if (!empty($filename)) {
                            $back = $filename;
                        }
                    }
                    $saveData = [];
                    $saveData['document_type'] = $doc['document_type'];
                    $saveData['user_uni_id'] = $astrologer_uni_id;
                    $saveData['front'] = !empty($front) && is_string($front) ? $front : '';
                    $saveData['back'] = !empty($back) && is_string($back) ? $back : '';
                    AstrologerDocument::create($saveData);
                }
            }
            if (!empty($attributes['astro_img'])) {
                $img        =   'astro_img';
                $imgPath    =   public_path(config('constants.astrologer_image_path'));
                $filename   =   UploadImage($request, $imgPath, $img);
                $saveDate['astro_img'] = $filename;
            }

            if (!empty($attributes['galleries'])) {
                foreach ($attributes['galleries'] as $key => $doc) {
                    if (!empty($doc)) {
                        $imgKey     = 'galleries.' . $key;
                        $imgPath    =   public_path(config('constants.astrologer_gellery'));
                        $filename   = UploadImage($request, $imgPath, $imgKey);
                    }
                    $saveData = [];
                    $saveData['astrologer_uni_id'] = $astrologer_uni_id;
                    $saveData['image'] = !empty($filename) && is_string($filename) ? $filename : '';
                    $saveData['status'] = 1;
                    AstrologerGallery::create($saveData);
                }
            }

            $process_status = $astro->process_status;
            if (!empty($astro) && $astro->process_status < 4) {
                $saveDate['process_status'] = 4;
            }

            if (!empty($saveDate)) {
                $astro->update($saveDate);
            }

            $filter = [];
            $filter['astrologer_uni_id'] = $astrologer_uni_id;
            $res = Api::getAstroData($filter, 1);
            if ($res) {
                $user       =   Astrologer::where('astrologer_uni_id', $astrologer_uni_id)->leftJoin('users', function ($join) {
                    $join->on('users.user_uni_id', '=', 'astrologers.astrologer_uni_id');
                })->first();
                if ($user->welcome_mail != 1 && !empty($user->email) && $process_status < 4) {
                    $mail   =   MyCommand::SendNotification($user->user_uni_id, 'welcome-template-for-astrologer', 'welcome-template-for-astrologer');
                    if ($mail) {
                        $attributes['welcome_mail'] =   1;
                        $mail =   array('welcome_mail' => $attributes['welcome_mail']);
                        User::where('user_uni_id', '=', $astrologer_uni_id)->update($mail);
                    }
                }

                $result = array(
                    'status'     => 1,
                    'data'          => $res,
                    'msg'         => 'Document Uploaded Successfully',

                );
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => 'Something went wrong',
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => 'You can upload a maximum of ' . env('GELLERY_COUNT') . ' images. ' . $alreadyImageCount . ' images have already been uploaded. You can now upload ' . $canUpload . ' more images.',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is a astrologer Followere
    public function astrologerFollow(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $astrologer_uni_id  =   $attributes['astrologer_uni_id'];
        $status  =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        } else {
            $msg = 'Unfollow';
            if ($status == 1) {
                $msg = 'Follow';
            }

            $selectQuesries = Follower::where('astrologer_uni_id', '=', $astrologer_uni_id)->where('user_uni_id', '=', $user_uni_id)->where('status', '=', $status)->first();
            if (empty($selectQuesries)) {
                $result = array(
                    'astrologer_uni_id'   => $attributes['astrologer_uni_id'],
                    'user_uni_id'         => $attributes['user_uni_id'],
                    'status'              => $status
                );

                $follower = Follower::where('astrologer_uni_id', '=', $astrologer_uni_id)->where('user_uni_id', '=', $user_uni_id)->first();
                if (empty($follower)) {
                    Follower::create($result);
                } else {
                    $follower->update($result);
                }

                // $user = Customer::where('user_uni_id', '=', $user_uni_id)->first();
                // if (empty($user)) {
                //     dd('hhh');
                //     $database = array(

                //         "astrologer_uni_id"         => $user_uni_id,
                //         "customer_uni_id"             => $customer_uni_id,
                //         "status"                     => 1,
                //     );
                //     $likesdata =  UserActivity::create($database);
                // }

                $result = array(
                    'status'     => 1,
                    'msg'         => 'Successfully ' . $msg,
                );
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => 'Already ' . $msg,
                );
            }
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is a astrologer unfollow
    public function astrologerUnfollow(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];
        $user_uni_id    =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        } else {


            $selectQuesries = Follower::where('astrologer_uni_id', '=', $astrologer_uni_id)->where('user_uni_id', '=', $user_uni_id)->where('status', '=', 0)->count();
            if ($selectQuesries <= 0) {
                $result = array(
                    'astrologer_uni_id'   => $attributes['astrologer_uni_id'],
                    'user_uni_id'         => $attributes['user_uni_id'],
                    'status'              => 0
                );

                $follower = Follower::where('astrologer_uni_id', '=', $astrologer_uni_id)->where('user_uni_id', '=', $user_uni_id)->first();
                if (empty($follower)) {
                    Follower::create($result);
                } else {
                    $follower->update($result);
                }

                // $users = Customer::join('users', 'customers.customer_uni_id	', '=', 'users.user_uni_id')
                // ->get();

                // $user = Customer::where('user_uni_id', '=', $user_uni_id)->first();
                // // dd(getQueryWithBindings($user));
                // if (empty($user)) {
                //     $database = array(
                //         "astrologer_uni_id"         => $user_uni_id,
                //         "user_uni_id"             => $user_uni_id,
                //         "msg"                         => $user['name'] . ' Unfollow you',
                //         "status"                     => 1,
                //     );
                //     $likesdata =  UserActivity::create($database);
                // }

                $result = array(
                    'status'     => 1,
                    'msg'         => 'Successfully Unfollow',
                );
            } else {

                $result = array(
                    'status'     => 0,
                    'msg'         => 'Already Unfollow',

                );
            }
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function rechargeVoucher(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result          =  array();
        $dolor_price = 1;
        $amount_balance     = Api::getTotalBalanceById($user_uni_id);

        $user_wallet_amt     = $amount_balance;
        $isrecharge = Api::isRecharged($user_uni_id);

        $RechargeVoucher = RechargeVoucher::where('status', 1);

        if ($isrecharge == true) {
            $RechargeVoucher->where('tag', '!=', 'new');
        }

        $RechargeVoucher->orderBy('wallet_amount', 'asc');
        $recharge = $RechargeVoucher->get();

        $gstprecent = config('gst');
        foreach ($recharge as $key => $row) {
            $currency       =   '₹';
            $main_amount       =    $row['wallet_amount'] + $row['gift_amount'];
            $gstamount       =    !empty($gstprecent) ? round($row['wallet_amount'] * $gstprecent / 100, 2) : 0;
            $totalamount       =    $row['wallet_amount'] + $gstamount;

            $recharge[$key]['wallet_cms_id'] = $row['id'];
            $recharge[$key]['gstprecent'] = $gstprecent;
            $recharge[$key]['gstamount'] = $gstamount;
            $recharge[$key]['totalamount'] = $totalamount;
            $recharge[$key]['currency'] = $currency;
            $recharge[$key]['main_amount'] = !empty($main_amount) ? $main_amount : "0.00";
        }

        if (!empty($recharge)) {
            $result = array(
                'status'     => 1,
                'wallet'     => $user_wallet_amt,
                'data'         => $recharge,
                'msg'         => 'Result Found',
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => 'Data Not Found !!',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is addCustomerWalletRecharge
    public function proceedPaymentRequest(Request $request)
    {
        $result = [];
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'payment_method' => ['required'],
            'amount' => ['nullable'],
            'wallet_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $payment_method     =   $attributes['payment_method'];
        $wallet_id          =   $attributes['wallet_id'];
        $gst                =   Config::get('gst');
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $customerData = Customer::join('users', 'customers.customer_uni_id', '=', 'users.user_uni_id')->where('customer_uni_id', '=', $user_uni_id)->first();

        // dd();
        // $data =  Wallet::where('id', '=', $wallet_id)->get();
        $data =  RechargeVoucher::where('id', '=', $wallet_id)->first();
        if (!empty($data)) {
            $isrecharge = Api::isRecharged($user_uni_id);
            if (($isrecharge == false && $data['tag'] == 'new') || $data['tag'] != 'new') {
                $transaction_amount = $data['wallet_amount'];
                $gstcalc  =   $data['wallet_amount'] * $gst / 100;
                $totalamount = $gstcalc + $data['wallet_amount'];
                if (!empty(Config::get('offer_ammount_status'))) {
                    $wallet_amount = $data['wallet_amount'];
                    $gift_amount = $data['gift_amount'];
                } else {
                    $wallet_amount = $data['wallet_amount'] + $data['gift_amount'];
                }

                if (!empty($totalamount)) {
                    $currecy            =  'INR';
                    if (!empty($wallet_amount)) {
                        $walletAmountData = array(
                            'user_uni_id'                => $user_uni_id,
                            'reference_id'                => $wallet_id,
                            'transaction_code'           => 'add_wallet',
                            'wallet_history_description' => 'Wallet Add Amount by Customer Recharge # RS. ' . $wallet_amount,
                            'transaction_amount'         => $totalamount,
                            'amount'                     => $wallet_amount,
                            'main_type'                  => 'cr',
                            'status'                     => 0,
                            'offer_status'                => 0,
                            'currency'                   => $currecy,
                            'gst_amount'                 => $gstcalc,
                            'coupan_amount'              => !empty($coupan_amount) ? $coupan_amount : '0',
                            'payment_method'             => $payment_method,
                            'where_from'                 => 'app',
                        );
                        $orderDetail_wallet = Wallet::create($walletAmountData);
                    }

                    if (!empty($gift_amount)) {
                        $giftAmountData = array(
                            'user_uni_id'                => $user_uni_id,
                            'reference_id'               => $wallet_id,
                            'transaction_code'           => 'add_wallet_voucher_gift',
                            'wallet_history_description' => 'Wallet Add Amount by Customer Gift # RS. ' . $gift_amount,
                            'transaction_amount'         => 0,
                            'amount'                     => $gift_amount,
                            'main_type'                  => 'cr',
                            'status'                     => 0,
                            'offer_status'               => 1,
                            'currency'                   => $currecy,
                            'coupan_amount'              => !empty($coupan_amount) ? $coupan_amount : '0',
                            'payment_method'             => $payment_method,
                            'where_from'                 => 'app',
                        );
                        $orderDetail_gift = Wallet::create($giftAmountData);
                    }

                    $arrry = array('amount' => $totalamount, 'currency' => $currecy);
                    // dd($order_id);
                    if (!empty($orderDetail_wallet)) {
                        if ($payment_method == 'PayTm') {
                            $datas = $this->paytmpost($user_uni_id, $orderDetail_wallet, $transaction_amount);
                        } else if ($payment_method == 'razorpay') {
                            $RazorpayApi = new RazorpayApi();
                            $response = $RazorpayApi->createOrderId($arrry);
                            if (!empty($response['orderId'])) {
                                // $walletDetails = Wallet::where('id', '=', $orderDetail->id)->first();
                                // dd($orderDetail->id);
                                $orderDetail_wallet->update(['gateway_order_id' => $response['orderId']]);
                                if (!empty($orderDetail_gift)) {
                                    $orderDetail_gift->update(['gateway_order_id' => $response['orderId']]);
                                }
                                $data['order_id'] = $response['orderId'] . '';
                                $data['amount'] = $totalamount . '';
                                $data['customerData'] = $customerData;
                                $result = array(
                                    'status'     => 1,
                                    'msg'        => "Recharge Request successfully.",
                                    'data'       => $data,
                                );
                            } else {
                                $result = array(
                                    'status'     => 0,
                                    'msg'         => $response['msg'],
                                );
                            }
                        } else if ($payment_method == 'CCAvenue') {

                            // $gateway_order_id = 'order_'.uniqid();
                            $gateway_order_id = generateNDigitRandomNumber(15);

                            $parameters = [
                                'merchant_id' => config('ccavenue_merchant_id'),
                                'currency' => config('ccavenue_currency'),
                                'redirect_url' => route("paymentresponseccavenueapp"),
                                'cancel_url' => route("paymentresponseccavenueapp"),
                                'language' => config('ccavenue_language'),
                                'order_id' => $gateway_order_id,
                                'amount' => $totalamount,
                                'billing_name' => !empty($customerData->name) ? $customerData->name : '',
                                'billing_tel' => '',
                                'billing_email' => '',
                                'merchant_param1' => $user_uni_id,
                            ];

                            $CCAvenueGateway = new CCAvenueGateway();
                            $ccavenue_request = $CCAvenueGateway->request($parameters);
                            $enc_val = '';

                            if (!empty($ccavenue_request->encRequest)) {
                                $enc_val = $ccavenue_request->encRequest;
                            }

                            if (!empty($enc_val)) {
                                $ccavenue_data = array(
                                    'order_id'      => $gateway_order_id,
                                    'access_code'   => config('ccavenue_access_code'),
                                    'redirect_url'  => route("paymentresponseccavenueapp"),
                                    'cancel_url'    => route("paymentresponseccavenueapp"),
                                    'enc_val'       => $enc_val,
                                    'merchant_id'   => config('ccavenue_merchant_id'),
                                    'working_key'   => config('ccavenue_working_key'),
                                    'currency'      => config('ccavenue_currency'),
                                    'language'      => config('ccavenue_language'),
                                );

                                $orderDetail_wallet->update(['gateway_order_id' => $gateway_order_id]);
                                if (!empty($orderDetail_gift)) {
                                    $orderDetail_gift->update(['gateway_order_id' => $gateway_order_id]);
                                }

                                $data['order_id'] = $gateway_order_id . '';
                                $data['amount'] = $totalamount . '';
                                $data['customerData'] = $customerData;
                                $result = array(
                                    'status'        => 1,
                                    'msg'           => "Recharge Request successfully.",
                                    'ccavenue_data' => $ccavenue_data,
                                    'data'          => $data,
                                );
                            } else {
                                $result = array(
                                    'status'     => 0,
                                    'msg'         => 'Something went Wrong. Please Try Again',
                                );
                            }
                        } else if ($payment_method == 'PhonePe') {

                            $gateway_order_id = "ORD".generateNDigitRandomNumber(13);
                            $parameters = [
                                'merchantTransactionId' => $gateway_order_id,
                                'merchantUserId' => $user_uni_id,
                                'amount' => $totalamount,
                                'redirectUrl' => route("paymentresponsephonepeapp"),
                                'callbackUrl' => route("paymentresponsephonepeapp"),
                                'mobileNumber' => '',
                            ];

                            $PhonePeGateway = new PhonePeGateway();
                            $phonepe_data = $PhonePeGateway->requestApp($parameters);

                            if($phonepe_data['status'] == 1){
                                $phonepe_data['order_id'] = $gateway_order_id;
                                $orderDetail_wallet->update(['gateway_order_id' => $gateway_order_id]);
                                if(!empty($orderDetail_gift)){
                                    $orderDetail_gift->update(['gateway_order_id' => $gateway_order_id]);
                                }
                                $data['order_id'] = $gateway_order_id . '';
                                $data['amount'] = $totalamount . '';
                                $data['customerData'] = $customerData;
                                $result = array(
                                    'status'        => 1,
                                    'msg'           => "Recharge Request successfully.",
                                    'phonepe_data'  => $phonepe_data,
                                    'data'          => $data,
                                );
                            } else {
                                $result = array(
                                    'status'     => 0,
                                    'msg'         => 'Something went Wrong. Please Try Again',
                                );
                            }
                        }
                    } else {
                        $result = array(
                            'status'     => 0,
                            'msg'         => 'Something went Wrong. Please Try Again',
                        );
                    }
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => 'Recharge amount is required',
                    );
                }
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => 'You have already recharged this offer',
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => 'Wallet CMS Id',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is updateOnlinePayment
    public function updateOnlinePayment(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'payment_method' => ['required'],
            'payment_id' => ['required'],
            'order_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $payment_method     =   $attributes['payment_method'];
        $payment_id         =   $attributes['payment_id'];
        $order_id           =   $attributes['order_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result             = array();
        if ($payment_method == 'PayTm') {
        } else if ($payment_method == 'razorpay') {
            $result =   Api::updateOnlinePayment($attributes);
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function paymentResponseCCAvenueApp(Request $request)
    {
        $result = [];
        $CCAvenueGateway = new CCAvenueGateway();
        $ccavenue_response = $CCAvenueGateway->response($request);
        if (!empty($ccavenue_response['tracking_id'])) {
            $ccavenue_response['payment_id'] = $ccavenue_response['tracking_id'];
            $ccavenue_response['payment_method'] = 'CCAvenue';
        }else{
            $ccavenue_response['payment_id'] = 'failed';
            $ccavenue_response['payment_method'] = 'CCAvenue';
        }
        $result =   Api::updateOnlinePayment($ccavenue_response);

        if (!empty($result['msg'])) {
            $err_msg = $result['msg'];
        }
        $status = "Failure";
        if ($result['status'] == 1) {
            $status = "Success";
        }

        return redirect()->route('customerwalletapp', $status)->with('success', $err_msg);
    }



    public function paymentResponseCCAvenueWebhook(Request $request)
    {

        // $attributes = $request->all();
        $result = [];
        $CCAvenueGateway = new CCAvenueGateway();
        $ccavenue_response = $CCAvenueGateway->response($request);
        if (!empty($ccavenue_response['tracking_id'])) {
            $ccavenue_response['payment_id'] = $ccavenue_response['tracking_id'];
            $ccavenue_response['payment_method'] = 'CCAvenue';
        }else{
            $ccavenue_response['payment_id'] = 'failed';
            $ccavenue_response['payment_method'] = 'CCAvenue';
        }
        $result =   Api::updateOnlinePayment($ccavenue_response);

        if (!empty($result['msg'])) {
            $err_msg = $result['msg'];
        }
        $status = "Failure";
        if ($result['status'] == 1) {
            $status = "Success";
        }

        return redirect()->route('customerwalletapp', $status)->with('success', $err_msg);
    }

    public function paymentResponsePhonePeApp(Request $request)
    {
        $result = [];
        $attributes = $request->all();
        $PhonePeGateway = new PhonePeGateway();
        $phonepe_response = $PhonePeGateway->response($attributes);
        if (!empty($phonepe_response['payment_id'])) {
            $phonepe_response['payment_id'] = $phonepe_response['payment_id'];
            $phonepe_response['payment_method'] = 'PhonePe';
        }else{
            $phonepe_response['payment_id'] = '';
            $phonepe_response['payment_method'] = 'PhonePe';
        }
        $result =   Api::updateOnlinePayment($phonepe_response);
        if (!empty($result['msg'])) {
            $err_msg = $result['msg'];
        }
        $status = "Failure";
        if ($result['status'] == 1) {
            $status = "Success";
        }

        return redirect()->route('customerwalletapp', $status)->with('success', $err_msg);
    }

    //this is startcall
    public function checkCallDetail(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
            'call_type' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $astrologer_uni_id  =   $attributes['astrologer_uni_id'];
        $call_type          =   $attributes['call_type'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $res =  Api::checkCallDetail($astrologer_uni_id, $call_type, $user_uni_id);
        if (!empty($res)) {
            $result = $res;
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }


        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function getCustomerQueueList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $res =  Api::getCustomerQueueList($user_uni_id);
        if (!empty($res)) {
            $result = array(
                'status'     => 1,
                'data'     => $res,
                'msg'         => "List",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }


        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function getAstrologerQueueList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $res =  Api::getAstrologerQueueList($user_uni_id);
        if (!empty($res)) {
            $result = array(
                'status'     => 1,
                'data'     => $res,
                'msg'         => "List",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }


        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function astrologerQueueRefresh(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $waitingCustomer = Api::waitingCustomer($astrologer_uni_id);
        if (!empty($waitingCustomer)) {
            Api::startCall($waitingCustomer, $waitingCustomer->call_type);
            $result = array(
                'status'     => 1,
                'msg'         => "Wait For a While...",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "No Record Found...",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function startVoiceCallExotel(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes     = $request->all();
        $api_key        = $attributes['api_key'];
        $user_uni_id    = $attributes['user_uni_id'];

        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $request->astrologer_uni_id = $request->astrologer_id;
        $senddata = Api::startCall($request, 'call');

        if (!empty($senddata)) {
            $result = $senddata;
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Astrologer not available",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //service to call
    public function DirectCall(Request $request)
    {
        $attributes = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'caller_id' => ['required'],
            'receiver_id' => ['required'],
            'second' => ['required'],
        ]);

        if ($attributes->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $attributes->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $attributes->messages()->all()),
            ]);
        }


        $attributes = $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        // dd($attributes);
        // $items   = json_decode();
        $caller = User::where('user_uni_id', $attributes['caller_id'])->first();
        $receiver = User::where('user_uni_id', $attributes['receiver_id'])->first();
        if (!empty($receiver)) {
            $receiverNumber = $receiver->phone;
            if (!empty($caller)) {
                $callerNumber = $caller->phone;
                $second =  $attributes['second'];
                $custoWhite = API::exotelCustomerWhitelistCurl($callerNumber);
                $astroWhite = API::exotelCustomerWhitelistCurl($receiverNumber);
                $CallReq = API::exotelCallRequestCurl($receiverNumber, $callerNumber, $second);
                // dd($CallReq);
                if (!empty($CallReq['Call']['Sid'])) {
                    $result = array(
                        'status'     => 1,
                        'msg'         => 'Your request is send Successfully.System will call you soon',
                    );
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => 'Oops! Cannot generate token. Please try again',
                    );
                }
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Invalid Credentials",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Receiver not available",
            );
        }

        return response()->json($result);
    }
    public function ServiceCall(Request $request)
    {
        $attributes = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'order_id' => ['required'],

        ]);

        if ($attributes->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $attributes->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $attributes->messages()->all()),
            ]);
        }


        $attributes = $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $minute = $secound = $available = 0;
        $current_time = Carbon::now();
        $serviceorder = ServiceOrder::where('order_id', $attributes['order_id'])->first();
        if (!empty($serviceorder)) {
            $duration = $serviceorder['available_duration'];
            if (empty($serviceorder['start_time'])) {
                $serviceorder->update(['start_time' => $current_time]);
                $minute = $duration;
                $secound = $duration * 60;
                $available = 1;
            } else {
                $time = time();
                $servicetime = strtotime($serviceorder['start_time']);
                $time_difference = $time - $servicetime;
                $durationtime = $duration * 60;
                $secoundtime = $durationtime - $time_difference;
                if ($secoundtime > 0) {
                    $minute = round($secoundtime / 60);
                    $secound = $secoundtime;
                    $available = 1;
                } 
                # Format datetime object as string
                // dd($time_difference);
            }
            
            if (!empty($secound) && $secound > 0) {
                $data['minute'] = $minute;
                $data['secound'] = $secound;
                $data['available'] = $available;
                $result = array(
                    'status' => 1,
                    'data'   => $data,
                    'msg'     => 'Your service is send successfully',
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'     => 'Your service time is over ',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Invalid service',
            );
        }

        return response()->json($result);
    }

    public function statusCallback(Request $request)
    {
        $result = [];
        $api            = saveapiLogs($request->all());
        $token          = $request->CallSid;
        $status         = $request->Status;
        $startTime      = $request->StartTime;
        $endTime        = $request->EndTime;
        $duration       = $request->ConversationDuration;
        $RecordingUrl   = !empty($request->RecordingUrl) ? $request->RecordingUrl : '';

        $calls = Api::getByToken($token);
        $user_uni_id = $calls->customer_uni_id;
        $astrologer_uni_id = $calls->astrologer_uni_id;
        $uniqeid = $calls->uniqeid;



        if ($status == 'completed') {
            if (!empty($calls)) {
                $sendData = [];
                $sendData['uniqeid'] = $calls->uniqeid;
                $sendData['startTime'] = $startTime;
                $sendData['endTime'] = $endTime;
                $sendData['duration'] = $duration;
                $sendData['RecordingUrl'] = $RecordingUrl;
                $sendData['call_type'] = 'call';

                $result = Api::callTransations($sendData);
            }
        } else {

            // if($status == 'busy'){
            //     $status == 'Declined(Astrologer)';
            // }

            $callHistory = CallHistory::where('uniqeid', '=', $uniqeid)->first();
            $callHistory->update(['status' => $status]);
            Api::removeBusyStatus($astrologer_uni_id);

            if (!empty($calls->astrologer_uni_id)) {
                $waitingCustomer = Api::waitingCustomer($calls->astrologer_uni_id);
                if (!empty($waitingCustomer)) {
                    $senddata = Api::startCall($waitingCustomer, $waitingCustomer->call_type);
                }
            }
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function endCallSendBird(Request $request)
    {
        $astrologer_uni_id  = '';
        $api = saveapiLogs($request->all());
        $reqData = json_decode($request->getContent());

        if (!empty($reqData->application_id) && !empty($reqData->direct_call->call_id) && $reqData->result == 'completed') {
            if ($reqData->category == 'direct_call:end') {
                $res = Api::requestSendBird($reqData->application_id, $reqData->direct_call->call_id);
                // pr($res);
                if ($reqData->direct_call->call_id == $res->call_id) {

                    $call_id              = $reqData->direct_call->call_id;
                    $user_uni_id          = $reqData->direct_call->caller_id;
                    $astrologer_uni_id  = $reqData->direct_call->callee_id;
                    $isServiceCall  = !empty($reqData->direct_call->custom_items->isServiceCall) ? $reqData->direct_call->custom_items->isServiceCall : 'false';
                    $call_type = '';
                    if ($isServiceCall == 'false') {
                        $already = CallHistory::where('token', $call_id)->where('status', 'completed')->first();

                        if (empty($already)) {
                            if (!empty($res->is_video_call)) {
                                $call_type      = 'video';
                                $astroPrices    = Api::getAstroPriceDataType($astrologer_uni_id, $call_type);
                                $uniqeid     = new_sequence_code('VIDEO');
                            } else {
                                $call_type      = 'internal_call';
                                $astroPrices = Api::getAstroPriceDataType($astrologer_uni_id, $call_type);
                                $uniqeid     = new_sequence_code('CALL');
                            }

                            if (!empty($astroPrices->price)) {
                                $astro_price = $astroPrices->price;
                                $occurred_at = date('Y-m-d H:i:s', ceil($reqData->occurred_at / 1000));
                                $order_date = date('Y-m-d', ceil($res->started_at / 1000));
                                $started_at = date('Y-m-d H:i:s', ceil($res->started_at / 1000));
                                $ended_at = date('Y-m-d H:i:s', ceil($res->ended_at / 1000));
                                $duration = ceil($res->duration / 1000);

                                $callHistoryData = array(
                                    'uniqeid'               => $uniqeid,
                                    'customer_uni_id'       => $user_uni_id,
                                    'astrologer_uni_id'     => $astrologer_uni_id,
                                    'call_type'             => $call_type,
                                    'charge'                => $astro_price,
                                    'token'                 => $call_id,
                                    'call_start'            => $started_at,
                                    'call_end'              => $ended_at,
                                    'duration'              => $duration,
                                    'order_date'            => $order_date,
                                    'status'                => 'in-progress',
                                    'created_at'            => $occurred_at,
                                    'updated_at'            => $occurred_at,
                                );

                                $res = CallHistory::create($callHistoryData);
                                if (!empty($res)) {
                                    $sendData = [];
                                    $sendData['uniqeid'] = $uniqeid;
                                    $sendData['startTime'] = $started_at;
                                    $sendData['endTime'] = $ended_at;
                                    $sendData['duration'] = $duration;
                                    $sendData['call_type'] = $call_type;
                                }

                                $result = Api::callTransations($sendData);
                            } else {
                                $result = array(
                                    'status'     => 0,
                                    'msg'         => "Something Went wrong. Error Code 001",
                                );
                            }
                        } else {
                            $result = array(
                                'status'     => 0,
                                'msg'         => "Already Saved",
                            );
                        }
                    }
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => "Invalid Call ID. Try Again",
                    );
                }
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Call not ended till.",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Invalid App Details. Try Again",
            );
        }

        if (!empty($strologer_uni_id)) {
            $waitingCustomer = Api::waitingCustomer($astrologer_uni_id);
            if (!empty($waitingCustomer)) {
                $senddata = Api::startCall($waitingCustomer, $waitingCustomer->call_type);
            }
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    //this is startVideoCall
    public function startVideoCall(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $senddata = Api::startCall($request, 'video');
        // dd($senddata);
        if (!empty($senddata)) {
            $result = $senddata;
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Astrologer not available",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }



    // this is reciveVideoCall
    public function reciveVideoCall(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'uniqeid' => ['required'],
            'astrologer_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $uniqeid       =   $attributes['uniqeid'];
        $astrologer_id        =   $attributes['astrologer_id'];
        if (!checkUserApiKey($api_key, $astrologer_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $save_data = array(
            'call_start'            => date('Y-m-d H:i:s')
        );
        $res = CallHistory::where('uniqeid', '=', $uniqeid)->update($save_data);
        if (!empty($res)) {
            $result = array(
                'status'     => 1,
                'msg'         => " Successfully Update",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is endVideoCall
    public function endVideoCall(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'uniqeid' => ['required'],
            'duration' => ['required'],
            'status' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $duration        =   $attributes['duration'];
        $status        =   $attributes['status'];
        $uniqeid        =   $attributes['uniqeid'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $save_data = array(
            'call_end'            => date('Y-m-d H:i:s'),
            'duration'            =>  $duration,
            //'recording'           => $this->input->post('recording'),
            'status'         => $status,
        );
        $res = CallHistory::where('uniqeid', '=', $uniqeid)->update($save_data);
        if (!empty($res) && $status == 'completed') {

            $callsql = CallHistory::where('uniqeid', '=', $uniqeid)->first();

            $astroamount  = $callsql['charge'];
            $astrologer_uni_id = $callsql['astrologer_uni_id'];
            $customer_uni_id = $callsql['customer_uni_id'];
            $updatestatus = array('busy_status' => 0, 'chat_status' => 0);
            $statusUpdate = Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)
                ->update($updatestatus);

            $currency       =   '₹';
            $location       =  'India';
            $astroprices       = Api::getAstroPriceDataType($astrologer_uni_id, 'video');
            $amount_balance = Api::getTotalBalanceById($customer_uni_id);

            $wallet_balance = $amount_balance;
            $price             = $astroprices->price;
            $useAmount         = round($price * ($duration / 60), 2);

            $wallet_history_description  = "Calling Charge For Astrologer " . floor($duration / 60) . ':' . ($duration % 60) . " Min.";
            $walletdetect   = $wallet_balance - $useAmount;


            $wallet_Data    =   array(
                'user_uni_id'                       =>   $customer_uni_id,
                'transaction_code'              =>  'remove_wallet_by_calling',
                'wallet_history_description'    =>  $wallet_history_description,
                'transaction_amount'            =>  $useAmount,
                'amount'                        =>  $useAmount,
                // 'credit_amt'                    =>  0,
                // 'debit_amt'                     =>  $useAmount,
                'main_type'                         => 'dr',
                'uniqeid'                    =>  $uniqeid,
                // 'created_at'                    =>  $this->currentdate,
                'created_by'                    => $customer_uni_id
            );


            $res         =  Wallet::create($wallet_Data);
            $astroData          = Api::getAstrologerById($astrologer_uni_id);
            $amount_Astbalance = Api::getTotalBalanceById($astrologer_uni_id);
            // dd($amount_Astbalance);
            $wallet_history_descriptionAdd  = "Calling Amount For User " . floor($duration / 60) . ':' . ($duration % 60) . " Min.";
            $admin_percentage = Config::get('admin_percentage');
            if (!empty($astroData->admin_percentage)) {
                $admin_percentage = $astroData->admin_percentage;
            }

            //Accounting

            $gst                  =  Config::get('GST') * 0;

            $gstamount            = round(($gst / 100) * $useAmount, 2);
            $finalamt              = round($useAmount - $gstamount, 2);

            $percentToGet          = $admin_percentage;
            $admin_amount       = ($percentToGet / 100) * $finalamt;
            $halfAmount         = ($finalamt - $admin_amount);

            $tds               =  Config::get('TDS');
            $finaltds          =  round(($tds / 100) * $halfAmount, 2);
            $remain            =  ($halfAmount - $finaltds);


            $gateway_charge    =  Config::get('gateway_charge');
            $finalgateway      =  round(($gateway_charge / 100) * $remain, 2);

            $astroAmount       =  round($remain - $finalgateway, 2);
            // dd($amount_Astbalance['user_wallet_amt']);
            $walletadd           = $amount_Astbalance['user_wallet_amt'] + $useAmount;

            $wallet_AsData    =   array(
                'user_id'                       =>  $astrologer_uni_id,
                'transaction_code'              =>  'add_wallet_by_calling',
                'wallet_history_description'    =>  $wallet_history_descriptionAdd,
                'transaction_amount'            =>  $astroAmount,

                'main_type'                     =>  'cr',
                'amount'                        =>  $walletadd,
                'gst_amount'                    =>  $gstamount,
                'admin_percentage'                =>  $admin_percentage,
                'admin_amount'                    =>  $admin_amount,
                'tds_amount'                    =>  $finaltds,
                'gateway_charge'                =>  $finalgateway,

                'uniqeid'                    =>  $uniqeid,
                'created_by'                    =>  $astrologer_uni_id,
            );

            $res = Wallet::create($wallet_AsData);

            $result = array(
                'status'     => 1,
                'msg'         => " Successfully Update",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is callWithLiveTest
    public function callWithLiveTest__old(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required'],
        ]);
        $attributes = $request->all();
        $status            =   $attributes['status'];
        $result = array(
            'status'     => $status,
            'msg'         => "Success",
        );
        return response()->json($result);
    }

    //this is startVoiceCallWithLive
    public function startVoiceCallWithLive__old(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $customer_uni_id        =   $attributes['user_uni_id'];
        $astrologer_uni_id      =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $customer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $astroData          = Api::getAstrologerById($astrologer_uni_id);
        $astroprices        = Api::getAstroPriceDataType($astrologer_uni_id, 'callwithlive');
        $customerData       = Api::getCustomerById($customer_uni_id);
        $amount_balance     = Api::getTotalBalanceById($customer_uni_id);
        $user_wallet_amt     = $amount_balance;


        $time_in_minutes = 1;
        if (!empty($astroprices->time_in_minutes) && $astroprices->time_in_minutes > 0) {
            $time_in_minutes = $astroprices->time_in_minutes;
        }
        if (!empty($astroprices->price) && $astroprices->price > 0) {
            $usersql = CallHistory::where('customer_uni_id', '=', $customer_uni_id)->where('status', 1)->get();

            if (!empty($usersql)) {
                $astroPrice     = $astroprices->price;
            } else {
                $price =   Config::get('new_user_price');
                $astroPrice  = !empty($price) ? $price : '0';
            }

            $second         = $time_in_minutes * 60;
            $minutes        = $time_in_minutes;


            $call_expire = date('Y-m-d H:i:s', strtotime('+' . ($second + 60) . ' seconds'));
            if ($user_wallet_amt > 0 && $user_wallet_amt >= $astroPrice) {
                $uniqeid = new_sequence_code('CALL');
                $senddata = [];
                $senddata['uniqeid'] = $uniqeid;
                $status_online_data = array(
                    'uniqeid'               => $uniqeid,
                    'customer_uni_id'           => $customer_uni_id,
                    'astrologer_uni_id'    => $astrologer_uni_id,
                    'call_type'               => 'callwithlive',
                    'charge'                => $astroPrice,
                    'duration'              => $second,
                    'status'                => 1,
                    'call_start'            => date('Y-m-d H:i:s'),

                );
                $saveCallHistory = array(
                    'busy_status' => 1,
                    'chat_status' => 1,

                );
                $CallHistory = array(
                    'call_end' => $call_expire

                );
                $result = CallHistory::create($status_online_data);

                // dd($status_online_data);
                if (!empty($result)) {

                    $statusUpdate = Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->update($saveCallHistory);
                    $result = CallHistory::where('astrologer_uni_id', '=', $astrologer_uni_id)->update($CallHistory);
                    $useAmount         = $astroPrice;
                    if ($useAmount > $user_wallet_amt) {
                        $useAmount = $user_wallet_amt;
                    }
                    $wallet_history_description  = "Calling Charge For Astrologer " . floor($second / 60) . ':' . ($second % 60) . " Min.";
                    $walletdetect   = $user_wallet_amt - $useAmount;

                    $wallet_Data    =   array(
                        'user_uni_id'               =>  $customer_uni_id,
                        'transaction_code'              =>  'remove_wallet',
                        'wallet_history_description'    =>  $wallet_history_description,
                        'transaction_amount'            =>  $useAmount,
                        'main_type'                     =>  'dr',
                        'amount'                        =>  $walletdetect,
                        'created_by'                    =>  $customer_uni_id
                    );

                    $res = Wallet::create($wallet_Data);
                    $amount_Astbalance = Api::getTotalBalanceById($astrologer_uni_id);
                    $wallet_history_descriptionAdd  = "Calling Amount For User " . floor($second / 60) . ':' . ($second % 60) . " Min.";


                    $admin_percentage = Config::get('admin_percentage');
                    if (!empty($astroData->admin_percentage)) {
                        $admin_percentage = $astroData->admin_percentage;
                    }
                    //Accounting
                    $gst                  = Config::get('GST') * 0;
                    $gstamount            = round(($gst / 100) * $useAmount, 2);
                    $finalamt              = round($useAmount - $gstamount, 2);

                    $percentToGet          = $admin_percentage;
                    $admin_amount       = ($percentToGet / 100) * $finalamt;
                    $halfAmount         = ($finalamt - $admin_amount);

                    $tds                   =  Config::get('TDS');
                    $finaltds              =  round(($tds / 100) * $halfAmount, 2);
                    $remain                =  ($halfAmount - $finaltds);


                    $gateway_charge        =  Config::get('gateway_charge');
                    $finalgateway          =  round(($gateway_charge / 100) * $remain, 2);

                    $astroAmount           =  round($remain - $finalgateway, 2);

                    $walletadd           = $amount_Astbalance['user_wallet_amt'] + $astroAmount;
                    $wallet_AsData    =   array(
                        'user_uni_id'                       =>  $astrologer_uni_id,
                        'transaction_code'              =>  'add_wallet',
                        'wallet_history_description'    =>  $wallet_history_descriptionAdd,
                        'transaction_amount'            =>  $astroAmount,
                        //'credit_amt'                    =>  $astroAmount,
                        'main_type'                     =>  'cr',
                        'amount'                        =>  $walletadd,
                        'gst_amount'                    =>  $gstamount,
                        'admin_percentage'                =>  $admin_percentage,
                        'admin_amount'                    =>  $admin_amount,
                        'tds_amount'                    =>  $finaltds,
                        'gateway_charge'                =>  $finalgateway,
                        'created_by'                    =>  $customer_uni_id
                    );

                    $wallet_history = Wallet::create($wallet_AsData);

                    $senddata['second'] = $second;



                    if (!empty($astroData->name)) {

                        //$astro_fcm_tokan    = $astroData->user_fcm_token;
                        $astro_name    = $astroData->name;
                        $token              = array();
                        $token              = array($astro_name);
                        $title              = $customerData->customer_name; //"Customer";

                        $type               = 'android';
                        $message            = 'Incoming Video call ...';
                        $start_time            = date('H:i:s');
                        $duration            = 60;
                        // sendNotification($token, $message, $type, $title, 'video', $senddata['token'], $senddata['channelName'], $customer_uni_id, $start_time, $duration);
                        // dd($notiresult);
                    }

                    if (!empty($customerData->name)) {
                        $customer_name = $customerData->name;
                        $token2                = array();
                        $token2             = array($customer_name);
                        $title2             = $astroData->name; // "Astrologer";
                        $type2                 = 'android';
                        $message2             = 'Video Calling';
                        // sendNotification($token2, $message2, $type2, $title2, 'video', $senddata['token'], $senddata['channelName']);
                        // // dd($notiresult);
                    }

                    $result = array(
                        'status'     => 1,
                        'data'         => $senddata,
                        'msg'         => " Your call request inserted successfully. Please wait.",
                    );
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => "Something Went wrong. Try Again",
                    );
                }
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "You Have Not Sufficent Balance Kindly Recharge",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Astrologer does not avalable on Live call",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function joinLiveCall(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
            'call_type' => ['required'],
            'channel_name' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $astrologer_uni_id  =   $attributes['astrologer_uni_id'];
        $call_type          =   $attributes['call_type'];
        $channel_name       =   $attributes['channel_name'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $astroDetail        = Api::getAstrologerById($astrologer_uni_id);
        $customerData       = Api::getCustomerById($user_uni_id);
        $currency           = getCurrency($customerData->phone);

        $amount_balance     = Api::getTotalBalanceById($user_uni_id);
        $astroprices        = Api::getAstroPriceDataType($astrologer_uni_id, $call_type, $currency);


        if (!empty($astroprices->price) && $astroprices->price > 0) {
            $astroPrice         = $astroprices->price;
            if ($amount_balance > 0 && $amount_balance >= $astroPrice) {

                $astrodata['second']            = $astroprices->time_in_minutes * 60;
                $astrodata['minutes']           = $astroprices->time_in_minutes;
                $result = array(
                    'status' => 1,
                    'data' => $astrodata,
                    'msg'    => "Successfully",
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'    => "You Have Not Sufficent Balance Kindly Recharge",
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'    => "Astrologer not available for this service",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }
    public function acceptLiveCall(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
            'call_type' => ['required'],
            'channel_name' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         = $request->all();
        $api_key            = $attributes['api_key'];
        $user_uni_id        = $attributes['user_uni_id'];
        $astrologer_uni_id  = $attributes['astrologer_uni_id'];
        $call_type          = $attributes['call_type'];
        $channel_name       = $attributes['channel_name'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $astroDetail        = Api::getAstrologerById($astrologer_uni_id);
        $customerData       = Api::getCustomerById($user_uni_id);
        $currency           = getCurrency($customerData->phone);

        $amount_balance     = Api::getTotalBalanceById($user_uni_id);
        $astroprices        = Api::getAstroPriceDataType($astrologer_uni_id, $call_type, $currency);


        if (!empty($astroprices->price) && $astroprices->price > 0) {
            $useAmount   = $astroprices->price;
            $duration    = 1;
            if (!empty($astroprices->time_in_minutes)) {
                $duration    = $astroprices->time_in_minutes;
            }

            $duration = $duration * 60;

            if ($amount_balance > 0 && $amount_balance >= $useAmount) {
                if ($call_type === 'callwithlive') {
                    $uniqeid        = new_sequence_code('CALL');
                } else if ($call_type === 'videocallwithlive') {
                    $uniqeid        = new_sequence_code('VIDEO');
                }
                $callHistoryData = [
                    'uniqeid'               => $uniqeid,
                    'customer_uni_id'       => $user_uni_id,
                    'astrologer_uni_id'     => $astrologer_uni_id,
                    'duration'              => $duration,
                    'charge'                => $useAmount,
                    'call_type'             => $call_type,
                    'channel_name'          => $channel_name,
                    'order_date'            => config('current_date'),
                    'call_start'            => config('current_datetime'),
                    'status'                => 'completed',
                ];

                CallHistory::create($callHistoryData);

                if (!empty($astroDetail->admin_percentage) && intval($astroDetail->admin_percentage) > 0) {
                    $admin_percentage = $astroDetail->admin_percentage;
                } else {
                    $admin_percentage = Config::get('admin_percentage');
                }

                $postAstroWalletHistoryData = [
                    'astrologer_uni_id'     => $astrologer_uni_id,
                    'admin_percentage'      => $admin_percentage,
                    'user_uni_id'           => $user_uni_id,
                    'call_type'             => $call_type,
                    'useAmount'             => $useAmount,
                    'duration'              => $duration,
                    'uniqeid'               => $uniqeid,
                ];

                Api::walletHistoryCreate($postAstroWalletHistoryData);

                $result = array(
                    'status'     => 1,
                    'msg'         => "Successfully",
                );
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "You Have Not Sufficent Balance Kindly Recharge",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Astrologer not available for this service",
            );
        }


        return response()->json($result);
    }

    //this is getChatChannels


    public function getChatChannels(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'page' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $thismodel = ChatChannel::select([
            'chat_channels.*', 'customers.customer_img', 'astrologers.astro_img', 'astrologers.display_name', 'customer.name as user_name',
        ])
            ->leftJoin('users as astrologer', 'astrologer.user_uni_id', '=', DB::raw("SUBSTRING_INDEX(chat_channels.channel_name, '-', '-1')"))
            ->leftJoin('users as customer', 'customer.user_uni_id', '=', DB::raw("SUBSTRING_INDEX(chat_channels.channel_name, '-', '1')"))
            ->leftJoin('astrologers', 'astrologers.astrologer_uni_id', '=', DB::raw("SUBSTRING_INDEX(chat_channels.channel_name, '-', '-1')"))
            ->leftJoin('customers', 'customers.customer_uni_id', '=', DB::raw("SUBSTRING_INDEX(chat_channels.channel_name, '-', '1')"));


        if (!empty($first_msg_id)) {
            $thismodel->where('chat_channels.id', '<=', $first_msg_id);
        }

        if (!empty($user_uni_id)) {
            $thismodel->where('chat_channels.channel_name', 'like', "%$user_uni_id%");
        }


        if (empty($request->page)) {
            $request->page = 1;
        }

        $page_limit = config('constants.api_page_limit');
        $offset = ($request->page - 1) * $page_limit;

        $thismodel->orderBy('chat_channels.updated_at', 'DESC');
        $thismodel->offset($offset)->limit($page_limit);
        $chat_channels = $thismodel->get();

        foreach ($chat_channels as $key => $value) {
            $chat_channels[$key]['user_name'] = !empty($value['user_name']) ? $value['user_name'] : '';

            $imgPath = public_path(config('constants.customer_image_path'));
            if (!empty($value['customer_img']) && file_exists($imgPath . $value['customer_img'])) {
                $chat_channels[$key]['customer_img'] = url(config('constants.customer_image_path') . $value['customer_img']);
            } else {
                $chat_channels[$key]['customer_img'] = asset(config('constants.default_customer_image_path'));
            }

            $imgPath = public_path(config('constants.astrologer_image_path'));
            if (!empty($value['astro_img']) && file_exists($imgPath . $value['astro_img'])) {
                $chat_channels[$key]['astro_img'] = url(config('constants.astrologer_image_path') . $value['astro_img']);
            } else {
                $chat_channels[$key]['astro_img'] = asset(config('constants.default_astrologer_image_path'));
            }
        }


        if (!empty($chat_channels) && $chat_channels->count() > 0) {
            $result = array(
                'status'     => 1,
                'data'         => $chat_channels,
                'msg'         => "Saved Successfully.",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "No Record Found",
            );
        }
        // dd($chat_channels->toArray());
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is getChatChannelHistory
    public function getChatChannelHistory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'channel_name' => ['required'],
            'first_msg_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $channel_name        =   $attributes['channel_name'];
        $first_msg_id        =   $attributes['first_msg_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $chat_channel_history = Api::getChatChannelHistory($attributes);

        // dd($chat_channel_history);
        if (!empty($chat_channel_history)) {
            $result = array(
                'status'     => 1,
                'data'         => $chat_channel_history,
                'page'         => $request->page + 1,
                'msg'         => "Saved Successfully.",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "No Record Found",
            );
        }
        // dd($result);
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is getUserCallings
    public function getUserCallings(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'call_type' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];
        $call_type        =   $attributes['call_type'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $cmsArr = Callhistory::where('astrologer_uni_id', '=', $astrologer_uni_id)->where('call_type', '=', $call_type)->get();
        $result             =   array();
        $records            =   array();
        foreach ($cmsArr as $row) {
            $records[] = array(
                'id'            => $row['id'],
                'user_uni_id'   => $row['user_uni_id'],
                'call_type'       => $row['call_type'],
                'user_name'     => Api::getCustomerNameById($row['user_uni_id']),
                'astro_name'    => Api::getAstrologerNameById($row['astrologer_uni_id']),
                // 'uniqeid'       => !empty($row['uniqeid']) ? $row['uniqeid'] : '',
                // 'token'         => !empty($row['token']) ? $row['token'] : '',
            );
        }
        if (!empty($records)) {
            $result = array(
                'status'     => 1,
                'msg'         => "Successfully...",
                'data'         => $records,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Data Not Found...",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is re
    // this is reciveVideoCall
    public function reciveVoiceCallWithLive(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'uniqeid' => ['required'],
            'astrologer_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $uniqeid       =   $attributes['uniqeid'];
        $astrologer_id        =   $attributes['astrologer_id'];
        if (!checkUserApiKey($api_key, $astrologer_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $save_data = array(
            'call_start'            => date('Y-m-d H:i:s')
        );
        $res = CallHistory::where('uniqeid', '=', $uniqeid)->update($save_data);
        if (!empty($res)) {
            $result = array(
                'status'     => 1,
                'msg'         => " Successfully Update",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    //this is endVoiceCallWithLive
    public function endVoiceCallWithLive(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'uniqeid' => ['required'],
            'duration' => ['required'],
            'status' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $duration        =   $attributes['duration'];
        $status        =   $attributes['status'];
        $uniqeid        =   $attributes['uniqeid'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $callsql = CallHistory::where('uniqeid', '=', $uniqeid)->first();
        $astrologer_uni_id =   $callsql['astrologer_uni_id'];

        if (!empty($callsql)) {
            $customer_uni_id    = $callsql['customer_uni_id'];
            $usersql  = CallHistory::where('customer_uni_id', '=', $customer_uni_id)->where('status', 1)->first();
            $save_data = array(
                'call_end'            => date('Y-m-d H:i:s'),
                'duration'            => $duration,
                'status'         => $status,
            );
            $res = CallHistory::where('uniqeid', '=', $uniqeid)->update($save_data);
            if (!empty($res) && $status == 1) {

                $astroamount    = $callsql['charge'];

                $update_astrologer = array(
                    'busy_status' => 0, 'chat_status' => 0
                );
                $statusUpdate = Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->update($update_astrologer);


                $result = array(
                    'status'     => 1,
                    'msg'         => " Successfully Update",
                );
            } else if (!empty($res) && ($status == 10 || $status == 11)) {
                $result = array(
                    'status'     => 1,
                    'msg'         => " Successfully Update",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is startVoiceCall
    public function startVoiceCall(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'customer_uni_id' => ['required'],
            'astrologer_id' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['customer_uni_id'];
        $astrologer_uni_id        =   $attributes['astrologer_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $astroData          = Api::getAstrologerById($astrologer_uni_id);
        $astroprices        = Api::getAstroPriceDataType($astrologer_uni_id, 'video');

        $customerData       = Api::getCustomerById($user_uni_id);
        $amount_balance     = Api::getTotalBalanceById($user_uni_id);
        $user_wallet_amt     = $amount_balance;

        if (!empty($astroprices->price) && $astroprices->price > 0) {
            $usersql = CallHistory::where('customer_uni_id', $user_uni_id)->where('status', 1)->get();

            $astroPrice     = $astroprices->price;
            $second = $minutes = 0;

            if ($astroPrice > 0) {
                $second         = (floor($user_wallet_amt / $astroPrice) * 60);
                $minutes        = floor($user_wallet_amt / $astroPrice);
            }
            if (($user_wallet_amt > 0  && $user_wallet_amt >= $astroPrice)) {
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "You Have Not Sufficent Balance Kindly Recharge",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Astrologer does not avalable on video call",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is getUserProfileData
    public function getUserProfileData(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $data  = User::where('user_uni_id', '=', $user_uni_id)->first();
        $prodile_detail = Api::getUserData($request, true);
        if (!empty($data)) {
            if (!empty($prodile_detail)) {
                $res[] = $data;
                $result = array(
                    'status'     => 1,
                    'data'         => $res,
                    'msg'         => 'View User Details',
                );
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Mobile Number Not Valid",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Mobile Number Not Valid",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is startLiveStream
    public function startLiveStream__old(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_id'      => ['required'],
            'status'      => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes               =   $request->all();
        $api_key       =   $attributes['api_key'];
        $astrologer_id            =   $attributes['astrologer_id'];
        $status                   =   $attributes['status'];
        if (!checkUserApiKey($api_key, $astrologer_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        if ($status == 0) {
            $astrodatetime = array(
                'live_status'     =>   '0',
            );
            $atrologerupdate = Astrologer::where('astrologer_uni_id', '=', $astrologer_id)->update($astrodatetime);
            $result = array(
                'status'     => 1,
                'msg'         => 'You are Offline',
            );
        } else {
            $data         = [];
            $astrodata = Astrologer::where('astrologer_uni_id', '=', $astrologer_id)->first();
            // $astrodatass 	= Api::getAllAstrologers($astrodata, '', 0, 1, 1);
            // dd($astrodatass);
            if (!empty($astrodata)) {
                $live_expire = date('Y-m-d H:i:s', strtotime('+12 hours'));
                $uniqeid  =   new_sequence_code('LIVEVIDEO');
                $arry   =   array('uniqeid' => $uniqeid, 'user_uni_id' => $astrologer_id);
                // $senddata = Api::agoraRtmToken($uniqeid, $astrologer_id);
                $senddata = Api::generateZegoToken($arry);

                if (!empty($senddata->token)) {
                    $astrodatetime = array(
                        'live_status'               =>   '1',
                        'online_status'             =>   '1',
                        'video_status'              =>   '0',
                        'call_status'               =>   '0',
                        'chat_status'               =>   '0',
                        'livetoken'                 =>   $senddata->token,
                        'livechannel'               =>   $uniqeid,
                        //  'live_expire' 				=>   $live_expire,
                    );
                    // dd($astrodatetime);
                    $astrodatetimeupdate = Astrologer::where('astrologer_uni_id', '=', $astrologer_id)->update($astrodatetime);

                    $senddata->uniqeid = $uniqeid;




                    $result = array(
                        'status'     => 1,
                        'data'         => $senddata,
                        'msg'         => 'You are Online',
                    );
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => "Cannot generate token. Try Again",
                    );
                }
            } else {
                $result = array(
                    'status' => 1,
                    'msg'     => 'You are Offline',
                );
            }
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is startLiveStream
    public function startLiveStream(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'astrologer_uni_id'  => ['required'],
            'api_key'   => ['required'],
            'status'    => ['required'],
            'topic'     => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes               =   $request->all();
        $api_key       =   $attributes['api_key'];
        $astrologer_id            =   $attributes['astrologer_uni_id'];
        $status                   =   $attributes['status'];
        if (!checkUserApiKey($api_key, $astrologer_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        if ($status == 0) {
            $astrodatetime = array(
                'live_status'     =>   '0',
            );
            $atrologerupdate = Astrologer::where('astrologer_uni_id', '=', $astrologer_id)->first();

            $atrologerupdate->update($astrodatetime);
            $result = array(
                'status'     => 1,
                'msg'         => 'You are Offline',
            );
        } else {
            $data         = [];
            $astrodata = Astrologer::where('astrologer_uni_id', '=', $astrologer_id)->first();
            $live_expire_hours = config('constants.live_expire_hours');
            if (!empty($astrodata)) {
                $live_expire = date('Y-m-d H:i:s', strtotime('+' . $live_expire_hours . ' hours'));
                $uniqeid  =   new_sequence_code('LIVEVIDEO');
                $userdata = User::where('user_uni_id', '=', $astrologer_id)->first();

                $arry   =   array('uniqeid' => $uniqeid, 'user_uni_id' => $astrologer_id, 'user_id' => $userdata->id);
                $senddata = Api::agoraRtmToken($uniqeid, $astrologer_id);
                $senddata = Api::generateAgoraRtcToken($arry);
                // $senddata = (object)[];
                // $senddata->token = '';

                if (isset($senddata->token)) {
                    $astrodatetime = array(
                        'live_status'               =>   '1',
                        'online_status'             =>   '1',
                        'video_status'              =>   '0',
                        'call_status'               =>   '0',
                        'chat_status'               =>   '0',
                        'livetoken'                 =>   $senddata->token,
                        'livechannel'               =>   $uniqeid,
                        'live_expire'               =>   $live_expire,
                        'live_topic'                =>   !empty($attributes['topic']) ? $attributes['topic'] : '',
                    );

                    // dd($astrodatetime);
                    $astrodata->update($astrodatetime);
                    $senddata->uniqeid = $uniqeid;

                    $attributes['type'] = 'live';
                    Api::getNotificationTofollowers($attributes);


                    $result = array(
                        'status'     => 1,
                        'data'         => $senddata,
                        'msg'         => 'You are Online',
                    );
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => "Cannot generate token. Try Again",
                    );
                }
            } else {
                $result = array(
                    'status' => 0,
                    'msg'     => 'Astrologer does not exist.',
                );
            }
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is joinLiveStream
    public function joinLiveStream(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'user_uni_id'  => ['required'],
            'api_key'   => ['required'],
            'uniqeid'   => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes               =   $request->all();
        $api_key                    =   $attributes['api_key'];
        $user_uni_id            =   $attributes['user_uni_id'];
        $uniqeid                   =   $attributes['uniqeid'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $userdata = User::where('user_uni_id', '=', $user_uni_id)->first();
        if (!empty($userdata)) {
            $arry   =   array('uniqeid' => $uniqeid, 'user_uni_id' => $user_uni_id, 'user_id' => $userdata->id, 'role' => 'audience');
            $senddata = Api::generateAgoraRtcToken($arry);

            if (!empty($senddata->token)) {
                $result = array(
                    'status'     => 1,
                    'data'         => $senddata,
                    'msg'         => 'Success',
                );
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Cannot generate token. Try Again",
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'User does not exist.',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is joinLiveStreamWeb
    public function joinLiveStreamWeb(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'uid'  => ['required'],
            'uniqeid'   => ['required'],
            'role_type'   => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes     =   $request->all();
        $uid            =   $attributes['uid'];
        $uniqeid        =   $attributes['uniqeid'];
        $role_type      =   $attributes['role_type'];

        // 'audience'
        $arry   =   array('uniqeid' => $uniqeid, 'user_id' => $uid, 'role' => $role_type);
        $senddata = Api::generateAgoraRtcToken($arry);
        if (!empty($senddata->token)) {
            $result = array(
                'status'     => 1,
                'data'       => $senddata->token,
                'msg'        => 'Success',
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'        => "Cannot generate token. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }




    //this is connectLiveStream
    public function connectLiveStream__old(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_id'      => ['required'],
            'astrologer_id'      => ['required'],
            //'chat_url'      => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes               =   $request->all();
        $api_key       =   $attributes['api_key'];
        $astrologer_id            =   $attributes['astrologer_id'];
        $user_id                   =   $attributes['user_id'];
        if (!checkUserApiKey($api_key, $astrologer_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $data         = [];
        $astrodata = Astrologer::where('astrologer_uni_id', '=', $astrologer_id)->first();
        if (!empty($astrodata['astrologer_uni_id'])) {
            $channelName = $astrodata['livechannel'];
            $senddata = Api::agoraRtmTokenUser($channelName, $astrologer_id);
            if (!empty($senddata['token'])) {
                $connected_user = ConnectedUser::where('astrologer_uni_id', '=', $astrologer_id)->where('user_id', '=', $user_id)->first();


                if (!empty($connected_user)) {

                    $connected_id = $connected_user->id;
                    $savedata = array('status' =>  '1');
                    $res = ConnectedUser::where('id', '=', $connected_id)->update($savedata);
                } else {

                    $savedata = array(
                        'user_id'             => $user_id,
                        'astrologer_uni_id' => $astrologer_id,
                        'status'             => '1',
                    );
                    $res = ConnectedUser::create($savedata);
                }
                if (!empty($res)) {
                    $result = array(
                        'status'     => 1,
                        'data'         => $senddata,
                        'msg'         => 'You are connected',
                    );
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => 'You are not connected',
                    );
                }
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Cannot generate token. Try Again",
                );
            }
            // dd($senddata);

        } else {
            $result = array(
                'status'     => 0,
                'msg'         => 'Invalid Astrologer',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is disconnectLiveStream
    public function disconnectLiveStream__old(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_id'      => ['required'],
            'astrologer_id'      => ['required'],
            'withfollow'      => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes               =   $request->all();
        $api_key       =   $attributes['api_key'];
        $astrologer_id            =   $attributes['astrologer_id'];
        $user_id                   =   $attributes['user_id'];
        $withfollow                 =   $attributes['withfollow'];
        if (!checkUserApiKey($api_key, $astrologer_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $data         = [];
        $astrodata = Astrologer::where('astrologer_uni_id', '=', $astrologer_id)->first();
        $connected_user = ConnectedUser::where('astrologer_uni_id', '=', $astrologer_id)->where('user_id', '=', $user_id)->first();
        $connected_id = $connected_user->id;

        if (!empty($connected_id)) {

            if (!empty($withfollow)) {

                $selectQuesries = Follower::where('astrologer_uni_id', '=', $astrologer_id)->where('customer_uni_id', '=', $user_id)->first();
                if (!empty($selectQuesries)) {

                    $likesdata      = array('astrologer_uni_id' => $astrologer_id, 'customer_uni_id' => $user_id);
                    $follower = Follower::create($likesdata);
                    // dd($follower);
                    // $user = Customer::join('users','customers.customer_uni_id','=','users.user_uni_id')->where('customer_uni_id','=',$user_id)->first();
                    // dd($user);
                    $user = Customer::where('customer_uni_id', '=', $user_id)->first();
                    $customer = User::where('user_uni_id', '=', $user_id)->first();



                    // if (!empty($user)) {
                    //     $datasave  = array(
                    //         "astrologer_uni_id"     => $astrologer_id,
                    //         "user_uni_id"             => $user_id,
                    //         "msg"                     => $customer['name'] . 'followed you',
                    //         "status"                 => 1,
                    //     );

                    //     $user_Activity = UserActivity::create($datasave);
                    // }
                    $result = array(
                        'status'     => 0,
                        'msg'         => 'Successfully Follow',
                    );
                }
            }


            $savedata = array('status' =>  '0');

            $res = ConnectedUser::where('id', '=', $connected_id)->update($savedata);
            if (!empty($res)) {
                $result = array(
                    'status'     => 1,
                    'msg'         => 'You are disconnected',
                );
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => 'You are not disconnected',
                );
            }
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is addReviews
    public function addReviews(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['nullable'],
            'review_rating' => ['nullable'],
            'review_comment' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $review = array(
            'review_by_id'      => $user_uni_id,
            'review_for_id'     => $attributes['astrologer_uni_id'],
            'review_rating'     => $attributes['review_rating'],
            'review_comment'    => $attributes['review_comment'],
            // 'status' => "1",
        );

        $reviews = Review::create($review);

        if (!empty($reviews)) {
            updateUserRating($attributes['astrologer_uni_id']);

            $result = array(
                'status'     => 1,
                'reviews'     => $reviews,
                'msg'         => 'Result Found',
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => 'Data Not Found !!',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is getReviews
    public function getReviews(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            // 'api_key' => ['required'],
            'astrologer_uni_id' => ['nullable'],
            'offset' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $limit = config('constants.api_page_limit');
        $attributes         =   $request->all();
        $offset = !empty($attributes['offset']) ? $attributes['offset'] : 0;

        $attributes['limit'] = $limit;
        $attributes['offset'] = $offset;
        $attributes['status'] = 1;
        $reviews = Review::getQuery($attributes);

        if (!empty($reviews) && $reviews->count() > 0) {
            $result = array(
                'status'     => 1,
                'msg'         => 'Result Found',
                'offset'     => $offset + $limit,
                'reviews'     => $reviews,
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => 'Data Not Found !!',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }


    //this is saveReport
    public function saveReport(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'customer_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
            'content' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $customer_uni_id        =   $attributes['customer_uni_id'];
        $astrologer_uni_id      =   $attributes['astrologer_uni_id'];
        $content                =   $attributes['content'];
        if (!checkUserApiKey($api_key, $customer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        } else {
            $likesdata      = array(
                'astrologer_uni_id' => $astrologer_uni_id,
                'customer_uni_id' => $customer_uni_id,
                'content' => $content,
                'status' => 1,
            );
            // dd($likesdata );
            $report = Report::create($likesdata);
            $result = array(
                'status'     => 1,
                'data'   => $report,
                'msg'         => 'Successfully Added',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is a page api
    public function page(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'page_slug' => ['required'],
        ]);

        $attributes         =   $request->all();
        $pages = getslugpage($request->page_slug);

        if (!empty($pages)) {
            $result = array(
                'status' => 1,
                'data' => $pages,
                'msg' => 'Successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Login First",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is a cityList
    public function cityList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        //  $validator  	    = asset('assets/img/logos/'.$attributes['customer_img']);
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result = [];
        $validator = Validator::make($request->all(), [
            'state_id'     => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes                 = $request->all();
        $state_id =  $attributes['state_id'];
        $where  = array('state_id' => $state_id);
        $records       =   City::where($where)->get();
        // echo $records;
        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg' => 'Success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    //this is startChat
    public function startChat(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key'           => ['required'],
            'user_uni_id'       => ['required'],
            'astrologer_uni_id' => ['required'],
            'uniqeid' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();
        // $request->start = 1;
        $senddata = Api::startCall($request, 'chat');
        // dd($senddata);
        if (!empty($senddata)) {
            $result = $senddata;
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Astrologer not available",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this a reciveChat
    public function reciveChat(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'uniqeid' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }


        $attributes             =   $request->all();
        $api_key           =   $attributes['api_key'];
        $astrologer_uni_id      =   $attributes['astrologer_uni_id'];
        $uniqeid                =   $attributes['uniqeid'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $save_data = array(
            'call_start'        => date('Y-m-d H:i:s'),
            'order_date'        => date('Y-m-d'),
            'status'            => 'in-progress',
        );
        $calls = CallHistory::where('uniqeid', '=', $uniqeid)->first();
        $calls_astrologer = $calls['customer_uni_id'];
        if (!empty($calls_astrologer)) {
            $res = CallHistory::where('uniqeid', '=', $uniqeid)->update($save_data);
            $customer = Customer::join('users', 'customers.customer_uni_id', '=', 'users.user_uni_id')->first();
            if (!empty($res)) {
                $customerData =  $customer['customer_uni_id'];
                $senddata = [];
                $senddata['name'] = $customer['name'];
                $result = array(
                    'status'     => 1,
                    'data'         => $senddata,
                    'msg'         => " Your call request inserted successfully. Please  wait.",
                );
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Something Went wrong.. Try Again",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Invalid uniqeid",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function endChat(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'uniqeid' => ['required'],
            'duration' => ['nullable'],
            'status' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes    =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id   =   $attributes['user_uni_id'];
        $duration      =   $attributes['duration'];
        $uniqeid       =   $attributes['uniqeid'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $call_end = date('Y-m-d H:i:s');
        $calls = CallHistory::where('uniqeid', '=', $uniqeid)->first();

        if (!empty($calls['call_start'])) {

            if (!empty($calls['status']) && $calls['status'] != 'completed') {
                $duration_server = strtotime($call_end) - strtotime($calls['call_start']);

                if (!empty($duration)) {
                    $call_end = date('Y-m-d H:i:s', strtotime('+' . $duration . ' seconds', strtotime($calls['call_start'])));
                } else if (!empty($duration_server)) {

                    $duration = $duration_server;
                }
                // dd($duration);

                if (!empty($duration) && $duration > 0) {

                    $sendData = [];
                    $sendData['uniqeid'] = $calls->uniqeid;
                    $sendData['startTime'] = $calls['call_start'];
                    $sendData['endTime'] = $call_end;
                    $sendData['duration'] = $duration;
                    $sendData['call_type'] = 'chat';

                    $result = Api::callTransations($sendData);
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => "Duration is required",
                    );
                }
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Already updated.",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Call Not Received",
            );
        }

        $callHistory = CallHistory::where('uniqeid', '=', $uniqeid)->first();
        if (!empty($callHistory->astrologer_uni_id)) {
            $waitingCustomer = Api::waitingCustomer($callHistory->astrologer_uni_id);
            if (!empty($waitingCustomer)) {
                $senddata = Api::startCall($waitingCustomer, $waitingCustomer->call_type);
            }
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is a saveChat
    public function saveChat(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['nullable'],
            'uniqeid' => ['nullable'],
            'channel_name' => ['nullable'],
            'message' => ['nullable'],
            'parent_id' => ['nullable'],
            'selected_text' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key           =   $attributes['api_key'];
        $user_uni_id      =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $result = Api::saveChat($attributes);



        updateapiLogs($api, $result);
        return response()->json($result);
    }



    //this is a statelist
    public function stateList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $result = [];
        $validator = Validator::make($request->all(), [
            'country_id'     => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes                 = $request->all();

        $country_id =  $attributes['country_id'];
        $where  = array('country_id' => $country_id);
        $records       =   State::where($where)->get();
        // echo $records;
        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg' => 'Success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function countryList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        //  $validator  	    = asset('assets/img/logos/'.$attributes['customer_img']);
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $records = [];
        $records       =   Country::all();
        // echo $records;
        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg' => 'Success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function giftItem(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        //  $validator  	    = asset('assets/img/logos/'.$attributes['customer_img']);
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $records = [];
        $records       =   Gift::where('status', 1)->get();

        //  echo $records; die();
        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg' => 'Success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function languageList(Request $request)
    {
        $api = saveapiLogs($request->all());
        // $validator = Validator::make($request->all(), [
        //     'api_key' => ['required'],
        //     'user_uni_id' => ['required'],
        // ]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         "status" => 0,
        //         "errors" => $validator->errors(),
        //         "message" => 'Something went wrong'
        //     ]);
        // }
        // $attributes         =   $request->all();

        //  $validator  	    = asset('assets/img/logos/'.$attributes['customer_img']);
        // $user_api_key       =   $attributes['api_key'];
        // $user_uni_id        =   $attributes['user_uni_id'];
        // $key_response       =   checkUserApiKey($user_api_key, $user_uni_id);
        // if (empty($key_response[0])) {
        //     $result = array(
        //         'status' => 0,
        //         'msg'     => $key_response[1],
        //     );
        //     return response()->json($result);
        // }
        $records = [];
        $records       =   Language::where('status', 1)->get();
        // echo $records;
        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg' => 'Success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }
    // this is blog
    public function addBlog(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'astrologer_uni_id' => ['required'],
            'blog_title' => ['required'],
            'blog_content' => ['required'],
            'blog_image' => ['nullable'],
            'api_key' => ['required'],
            'blog_category_id' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();

        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];

        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $result = array(
            'auth_id' => $attributes['astrologer_uni_id'],
            'title' => $attributes['blog_title'],
            'content' => $attributes['blog_content'],
            'status' =>  0,
            'blog_category_id' =>  !empty($attributes['blog_category_id']) ? $attributes['blog_category_id'] : '0',
        );

        if (!empty($attributes['blog_image'])) {
            $img        =   'blog_image';
            $imgPath    =   public_path(config('constants.blog_image_path'));
            $filename   =   UploadImage($request, $imgPath, $img);
            $result['blog_image'] = $filename;
        }

        $blog = Blog::create($result);

        if (!empty($blog)) {
            $result = array(
                'status' => 1,
                'msg' => 'blog are Success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }
    // this is skill list
    public function skillList(Request $request)
    {

        $api = saveapiLogs($request->all());
        $records = [];
        $records       =   Skill::where('status', 1)->get();
        // echo $records;
        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg' => 'Success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }



    //this is myBlog
    public function getMyBlog(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'offset' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        $api_key      =  $attributes['api_key'];
        $astrologer_uni_id  =  $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $offset = !empty($request->offset) ? $request->offset : '0';
        $limit = config('constants.api_page_limit');
        $attributes['limit'] =  $limit;
        $attributes['offset'] = $offset;
        $blogs = Api::getAllBlog($attributes);

        if (!empty($blogs)) {
            $result = array(
                'status'    => 1,
                'msg'       => 'Result Found',
                'offset'    => $offset + $limit,
                'data'      => $blogs,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "NO record",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is a Delete blog
    public function deleteBlog(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];
        $id                 =   $attributes['id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $blog = Blog::where('auth_id', '=', $user_uni_id)->where('id', '=', $id)->first();

        if (!empty($blog)) {
            blog::where('id', $id)->delete();
            $result = array(
                'status' => 1,
                'msg'     => "Blog deleted successfully ",
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "No Record Found",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    //this is blogLike
    public function blogLike(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'blog_id'         => ['required'],
            'status'         => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $blog_id        =   $attributes['blog_id'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $msg = '';
        if ($status == 1) {
            $msg = 'Liked';
        } else {
            $msg = 'Disliked';
        }

        $data_result   = '';
        $selectQuesries = BlogLike::where('blog_id', $blog_id)->where('user_uni_id', $user_uni_id)->where('status', $status);
        if ($selectQuesries->count() <= 0) {
            $likesdata      = array('blog_id' => $blog_id, 'user_uni_id' => $user_uni_id, 'status' => $status);
            $blog = BlogLike::where('blog_id', $blog_id)->where('user_uni_id', $user_uni_id)->first();
            if (empty($blog)) {
                BlogLike::create($likesdata);
            } else {
                $blog->update($likesdata);
            }

            // if (!empty($blog)) {
            //     $datasave  = array(
            //         "astrologers_uni_id"   => $blog['blog_by_id'],
            //         "user_uni_id"          => $user_uni_id,
            //         "status"               => $msg,
            //     );

            //     UserActivity::create($datasave);
            // }

            $result = array(
                'status'     => 1,
                'msg'         => 'Successfully ' . $msg,
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => 'Already ' . $msg,
            );
        }


        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is banner
    public function banner(Request $request)
    {
        $api = saveapiLogs($request->all());
        $banners       =   banner::where('status', 1)->get();
        foreach ($banners as $key => $value) {
            $imgPath = public_path(config('constants.banner_image_path'));
            if (!empty($value->banner_image) && file_exists($imgPath . $value->banner_image)) {
                $value->banner_image =    url(config('constants.banner_image_path') . $value->banner_image);
            } else {
                $value->banner_image =    asset(config('constants.default_banner_image_path'));
            }
        }
        if (!empty($banners)) {
            $result = array(
                'status' => 1,
                'data' => $banners,
                'msg' => 'Success',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }
    //this is filterList
    public function getWalletBalance(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $location = 'India';
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $amount_balance = Api::getTotalBalanceById($user_uni_id);
        // $amount_balance = Api::getTotalBalanceGiftById($user_uni_id);
        $result = array(
            'status' => 1,
            'msg'     => 'Wallet Balance',
            'data'     =>  $amount_balance,
        );

        updateapiLogs($api, $result);
        return response()->json($result);
    }
    //this is astrologerOnlineStatus
    // public function astrologerOnlineStatus(Request $request)
    // {
    //     $api = saveapiLogs($request->all());
    //     $validator = Validator::make($request->all(), [
    //         'api_key' => ['required'],
    //         'astrologer_uni_id' => ['required'],
    //         'status' => ['required'],

    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             "status" => 0,
    //             "errors" => $validator->errors(),
    //             "message" => 'Something went wrong',
    //             "msg" => implode('\n', $validator->messages()->all()),
    //         ]);
    //     }
    //     $attributes         =   $request->all();


    //     $api_key       =   $attributes['api_key'];
    //     $user_uni_id        =   $attributes['astrologer_uni_id'];
    //     $status        =   $attributes['status'];
    //     if (!checkUserApiKey($api_key, $user_uni_id)) {
    //         $result = array(
    //             'status' => 0,
    //             'error_code' => 101,
    //            'msg'     => 'Unauthorized User... Please login again',
    //         );
    //         return response()->json($result);
    //     }

    //     $astrodatetime = array(
    //         'online_status'   => $status,
    //         'live_status'     => $status,
    //         'video_status'    => $status,
    //         'call_status'     => $status,
    //         'chat_status'     => $status,
    //     );

    //     $res = Astrologer::where('astrologer_uni_id', '=', $user_uni_id)->update($astrodatetime);
    //     if ($res) {
    //         $result = array(
    //             'status'     => 1,
    //             'msg'         => "Successfully Updated",
    //         );
    //     } else {
    //         $result = array(
    //             'status'     => 0,
    //             'msg'         => "Something Went wrong.. Try Again",
    //         );
    //     }
    //     updateapiLogs($api, $result);
    //     return response()->json($result);
    // }

    //this is updateNextOnlineTime
    public function updateNextOnlineTime(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key'               => ['required'],
            'astrologer_uni_id'     => ['required'],
            'time'                  => ['required'],
            'date'                  => ['required'],
            'schedule_type'         => ['required'],
            'topic'                 => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes     = $request->all();
        $api_key        = $attributes['api_key'];
        $user_uni_id    = $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $attributes['status'] = 1;
        
        if (LiveSchedule::create($attributes)) {
            $result = array(
                'status' => 1,
                'msg'     => 'Updated successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Something went wrong',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is getWalletHistory
    public function getWalletHistory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'offset' => ['nullable'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();

        $api_key       =   $attributes['api_key'];
        $user_uni_id   =   $attributes['user_uni_id'];

        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $limit = config('constants.api_page_limit');
        $request->limit = $limit;
        $offset = !empty($attributes['offset']) ? $attributes['offset'] : '0';
        $amount_data = Api::getWalletHistory($request);
        // dd($amount_data);

        if (!empty($amount_data)) {
            $result = array(
                'status'    => 1,
                'msg'       => 'Success',
                'offset'    => $offset  + $limit,
                'data'      => $amount_data,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Amount History data Was Empty!',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is getRechargeHistory
    public function getRechargeHistory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_id' => ['required'],
            'type' => ['nullable'],
            'page' => ['nullable'],
            // 'location' => ['nullable'],
            // 'from' => ['nullable'],
            // 'to' => ['nullable'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_id        =   $attributes['user_id'];
        $type        =   $attributes['type'];
        // $location        =   $attributes['location'];
        // $from        =   $attributes['from'];
        $page        =   $attributes['page'];
        // $to        =   $attributes['to'];
        if (!checkUserApiKey($api_key, $user_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }



        $amount_data = Api::getRechargeHistory($request);

        if (!empty($amount_data)) {
            $result = array(
                'status'     => 1,
                'msg'         => 'Success',
                //'user_amt'  => $amount_data['user_amt'],
                'data'         => $amount_data,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Amount History data Was Empty!',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is getChatRequest
    public function getChatRequest(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();


        $api_key            =   $attributes['api_key'];
        $astrologer_uni_id  =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $amount_data = Api::getChatRequest($request);

        if (!empty($amount_data)) {
            $result = array(
                'status'     => 1,
                'msg'         => 'Success',
                //'user_amt'  => $amount_data['user_amt'],
                'data'         => $amount_data,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Amount History data Was Empty!',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is sendGiftAstro
    public function declineChatRequest(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'uniqeid' => ['required'],
            'status' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes      =   $request->all();
        $api_key         =   $attributes['api_key'];
        $user_uni_id     =   $attributes['user_uni_id'];
        $uniqeid         =   $attributes['uniqeid'];
        $status          =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $calls = CallHistory::where('uniqeid', $uniqeid)->where(function ($query) {
            $query->where('call_history.status', '=', 'queue');
            $query->orWhere('call_history.status', '=', 'queue_request');
            $query->orWhere('call_history.status', '=', 'request');
            // $query->orWhere('call_history.status', '=', 'in-progress');
        })->first();
        if (!empty($calls)) {
            CallHistory::where('uniqeid', $uniqeid)->update(['status' => $status]);
            Api::removeBusyStatus($calls->astrologer_uni_id);
            $result = array(
                'status'     => 1,
                'msg'         => "Success",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Already Ended.",
            );
        }
        //  dd($result);
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is sendGiftAstro
    public function sendGiftAstro(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['nullable'],
            'gift_id' => ['nullable'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];
        $gift_id        =   $attributes['gift_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $gift = Gift::where('id', '=', $gift_id)->where('status', 1)->first();
        if (!empty($gift)) {
            $useAmount             = $gift->gift_price;
            $currency              =   '₹';

            $amount_balance = Api::getTotalBalanceById($user_uni_id);
            if (!empty($amount_balance)) {
                if ($amount_balance > 0 && $useAmount > 0 && $amount_balance >= $useAmount) {
                    $des  = "$useAmount $currency Gift Charge For $gift->gift_name To Astrologer.";
                    $wallet_Data    =   array(
                        'user_uni_id'                       =>  $user_uni_id,
                        'transaction_code'              =>  'remove_wallet_by_gift',
                        'wallet_history_description'    =>  $des,
                        'transaction_amount'            =>  $useAmount,
                        'main_type'                     =>  'dr',
                        'amount'                        =>  $useAmount,
                        'reference_id'                  =>  !empty($gift->id) ? $gift->id : '',
                        'status'                        => 1,
                    );


                    $res  = Wallet::create($wallet_Data);

                    $astroDetail    = Api::getAstrologerById($astrologer_uni_id);
                    $admin_percentage = 0;
                    if(!empty(Config::get('admin_percentage_on_gift_send')) && Config::get('admin_percentage_on_gift_send') == 1){
                        if (!empty($astroDetail->admin_percentage) && intval($astroDetail->admin_percentage) > 0) {
                            $admin_percentage = $astroDetail->admin_percentage;
                        } else {
                            if(is_numeric(Config::get('admin_percentage'))){
                                $admin_percentage = Config::get('admin_percentage');
                            }
                        }
                    }
                    $tds = 0;
                    if(!empty(Config::get('tds_on_gift_send')) && Config::get('tds_on_gift_send') == 1){
                        if(is_numeric(Config::get('tds'))){
                            $tds = Config::get('tds');
                        }
                    }

                    $tds_amount = 0;
                    $admin_amount = 0;
                    
                    if (!empty($admin_percentage) && $admin_percentage > 0) {
                        $admin_amount = $useAmount * $admin_percentage / 100;
                    }
                    $useAmount = $useAmount - $admin_amount;

                    if (!empty($tds) && $tds > 0) {
                        $tds_amount = round((($tds * $useAmount) / 100), 2);
                    }
                    $useAmount = $useAmount - $tds_amount;

                    $amount_Astbalance = Api::getTotalBalanceById($astrologer_uni_id);
                    $des  = "$useAmount $currency Gift Amount For $gift->gift_name From User.";

                    $walletadd           = $amount_Astbalance + $useAmount;
                    $wallet_AsData    =   array(
                        'user_uni_id'                   =>  $astrologer_uni_id,
                        'transaction_code'              =>  'add_wallet_by_gift',
                        'wallet_history_description'    =>  $des,
                        'transaction_amount'            =>  $useAmount,
                        'amount'                        =>  $useAmount,
                        'main_type'                     =>  'cr',
                        // 'current_wallet_amt'         =>  $walletadd,
                        'reference_id'                  =>  $gift->id,
                        'tds_amount'                    => $tds_amount,
                        'admin_amount'                  => $admin_amount,
                        'status'                        => 1,
                    );
                    $wallet  = Wallet::create($wallet_AsData);

                    $wallet_gData    =   array(
                        'user_id'               =>  $user_uni_id,
                        'astrologer_uni_id'     =>  $astrologer_uni_id,
                        'amount'                =>  $useAmount,
                        'gift_id'                =>  $gift->id,
                    );

                    $astrologer_gifts = AstrologerGift::create($wallet_gData);


                    $result = array(
                        'status'     => 1,
                        'msg'         => "Send Gift Successfully",
                    );
                } else {
                    $result = array(
                        'status'     => 0,
                        'msg'         => "Customer Ammount is Low",
                    );
                }
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Insufficient balance",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Invalid gift Id",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    //astroGiftHistory
    public function astroGiftHistory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $astrologer_uni_id  =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $records = Api::astroGiftHistory($attributes);


        if (!empty($records)) {
            $result = array(
                'status'     => 1,
                'msg'         => 'Success',
                'data'         => $records,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Amount History data Was Empty!',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //customerGiftHistory
    public function userGiftHistory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }


        $records = Api::userGiftHistory($request);


        if (!empty($records)) {
            $result = array(
                'status'     => 1,
                'msg'         => 'Success',
                'data'         => $records,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Amount History data Was Empty!',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    //this is userAddressList
    public function userAddressList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result = [];
        $records = UserAddress::where('user_uni_id', '=', $user_uni_id)->get();


        if (!empty($records)) {

            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'Record Found',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'No Record Found',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function addAddress(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'name' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
            'house_no' => ['nullable'],
            'street_area' => ['nullable'],
            'landmark' => ['nullable'],
            'address' => ['nullable'],
            'city' => ['nullable'],
            'state' => ['nullable'],
            'country' => ['nullable'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
            'pincode' => ['nullable'],
            'status' => ['nullable'],

        ]);

        // dd($validator);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $user_address = array(
            'name'         =>   $request['name'],
            'email'        =>   $request['email'],
            'user_uni_id'        =>   $request['user_uni_id'],
            'phone'       =>   $request['phone'],
            'house_no'       =>   $request['house_no'],
            'street_area'        =>   $request['street_area'],
            'landmark'        =>   $request['landmark'],
            'address'        =>   $request['address'],
            'city'        =>   $request['city'],
            'state'        =>   $request['state'],
            'country'        =>   $request['country'],
            'latitude'        =>   $request['latitude'],
            'longitude'        =>   $request['longitude'],
            'pincode'        =>   $request['pincode'],
            'status'        =>   1,
        );
        // dd($request->phone);
        $address = UserAddress::create($user_address);
        // dd($address);
        if (!empty($address)) {
            $result = array(
                'status' => 1,
                'data' => $address,
                'msg'     => 'User Address create',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Something went wrong',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }



    //this is productCategory
    public function productCategory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'user_uni_id' => ['nullable'],
            'search' => ['nullable'],
            'offset' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();
        $user_uni_id        =   !empty($attributes['user_uni_id']) ? $attributes['user_uni_id'] : '';


        $result = [];
        $records = Api::ProductCategory($request);

        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'Address list',
            );
        } else {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'Address list',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is products
    public function products(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
            'search' => ['nullable'],
            'price' => ['nullable'],
            'category_id' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();

        $result = [];
        $records = Api::getProducts($request);

        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'Product List',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'No Record Found',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is productOrderList
    public function productOrderList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result = [];
        
        $res = Order::where('order.user_uni_id', '=', $request->user_uni_id)->with(['order_products', 'vendor', 'user', 'address'])->get();
        //    dd($res);

        // foreach ($res as $key => $value) {
        //     $address =  !empty($value['address']) ? $value['address'] : [];
        //     unset($res[$key]->address);
        //     $res[$key]->address =  $address;
        // }

        if (empty($res)) {
            $result = array(
                'status' => 0,
                'msg'     => 'No Record Found',
            );
        } else {
            $result = array(
                'status' => 1,
                'data' => $res,
                'msg'     => 'Order list',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    //this is orderStatusProcess
    public function orderStatusProcess(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_id' => ['required'],
            'status' => ['required'],
            'order_id' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_id'];
        $status        =   $attributes['status'];
        $order_id        =   $attributes['order_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $product_status = Order::where('order_id', '=', $order_id)->where('user_id', '=', $user_uni_id)->first();
        $date =  date('Y-m-d');
        if (!empty($product_status)) {
            $querystatus = array(
                'description'          => 'Product is dispatch',
                'status'              =>  $status,
                'updated_at'          =>  $date,
            );
            $res = order::where('order_id', '=', $order_id)->where('user_id', '=', $user_uni_id)->update($querystatus);

            if ($res) {
                $result = array(
                    'status' => 1,
                    'msg' => "Your product status changed successfully.",
                );
            } else {

                $result = array(
                    'status'     => 0,
                    'msg'         => "Something went wrong",
                );
            }
        } else {

            $result = array(
                'status'     => 0,
                'msg'         => "Sorry! Invalid Product",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is refundRequest
    public function refundRequest(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_id' => ['required'],
            'type' => ['required'],
            'order_id' => ['required'],
            'reason' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes     =   $request->all();
        $api_key        =   $attributes['api_key'];
        $user_uni_id    =   $attributes['user_id'];
        $unique_id      =   new_sequence_code('REFUND');
        $order_id       =   $attributes['order_id'];
        $type           =   $attributes['type'];
        $reason         =   $attributes['reason'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        if($type == 'call'){
            $call_history_check = CallHistory::where('uniqeid', $order_id)->first();
            if(!empty($call_history_check)){
                if(!($call_history_check->call_type == 'call' || $call_history_check->call_type == 'chat' || $call_history_check->call_type == 'video')){
                    $result = array(
                        'status' => 0,
                        'msg'     => 'Not allowed for this transaction.',
                    );
                    return response()->json($result);
                }
            }
        }

        $date =  date('Y-m-d');
        $exist_check = RefundRequest::where('order_id', $order_id)->where('type', $type)->first();
        if(empty($exist_check)){

            $data = array(
                'unique_id'                 => $unique_id,
                'user_id'                   => $user_uni_id,
                'order_id'                   => $order_id,
                'type'                      => $type,
                'reason'                   => $reason,
                'created_at'                => $date,
                'status'                    => 'Pending',
            );
            $result = RefundRequest::create($data);
            if (!empty($result)) {

                $template = '';
                if($type == 'service'){
                    ServiceOrder::where('order_id', $order_id)->update(['status' => 'refund_request']);

                    $template = (object) array(
                        'subject' => 'Refund Request for Service.',
                        'content' => 'User ID : ' . $user_uni_id . '<br>Order ID : ' . $order_id,
                        'template_code' => 'default',
                    );

                }elseif($type == 'product'){
                    Order::where('order_id', $order_id)->update(['status' => 'refund_request']);

                    $template = (object) array(
                        'subject' => 'Refund Request for Product.',
                        'content' => 'User ID : ' . $user_uni_id . '<br>Order ID : ' . $order_id,
                        'template_code' => 'default',
                    );

                }elseif($type == 'call'){
                    CallHistory::where('uniqeid', $order_id)->update(['status' => 'refund_request']);

                    $template = (object) array(
                        'subject' => 'Refund Request for Call.',
                        'content' => 'User ID : ' . $user_uni_id . '<br>Order ID : ' . $order_id,
                        'template_code' => 'default',
                    );

                }

                if(!empty($template)){
                    MyCommand::sendMailToAdmin($template);
                }



                $result = array(
                    'status' => 1,
                    'msg' => "Your Request Submitted Successfully.",
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'     => "Something Went wrong.. Try Again",

                );
            }

        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Your request is already exist.",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is orderReturnProcess
    public function orderReturnProcess(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_id' => ['required'],
            'status' => ['required'],
            'order_id' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_id'];
        $status        =   $attributes['status'];
        $order_id        =   $attributes['order_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $product_status = Order::where('order_id', '=', $order_id)->where('user_id', '=', $user_uni_id)->first();
        //  dd($product_status['return_valid_date']);
        $date =  date('Y-m-d');

        if (!empty($product_status)) {
            if ($product_status->return_valid_date >= $date) {
                $querystatus = array(
                    'description'          => 'Product is dispatch',
                    'status'              =>  $status,
                    'updated_at'          =>  $date,
                );


                $res = order::where('order_id', '=', $order_id)->where('user_id', '=', $user_uni_id)->update($querystatus);

                if ($res) {
                    $result = array(
                        'status' => 1,
                        'msg' => "Product return request accept.",
                    );
                } else {

                    $result = array(
                        'status'     => 0,
                        'msg'         => "Something went wrong",
                    );
                }
            } else {
                $result = array(
                    'status'     => 0,
                    'msg'         => "Sorry! Retrun date has expired.",
                );
            }
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Sorry! Invalid Product.",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    // this is userCallHistory
    public function userCallHistory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'call_type' => ['nullable'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $limit = config('constants.api_page_limit');
        $request->limit = $limit;
        $offset = !empty($request->offset) ? $request->offset : '0';

        $row = Api::userCallHistory($request);

        if (!empty($row)) {
            $result = array(
                'status'     => 1,
                'msg'         => "Successfully...",
                'offset'    => $offset  + $limit,
                'data'         => $row,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Data Not Found...",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    // this is astroCallHistory
    public function astroCallHistory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'call_type' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $limit = config('constants.api_page_limit');
        $request->limit = $limit;
        $offset = !empty($request->offset) ? $request->offset : '0';

        $records = Api::astroCallHistory($request);

        if ($records->count() > 0) {
            $result = array(
                'status'     => 1,
                'msg'         => "Successfully...",
                'offset'    => $offset  + $limit,
                'data'         => $records,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Data Not Found...",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is updateOnlineStatus
    public function updateOnlineStatus(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['nullable'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes = $request->all();

        $api_key            =   $attributes['api_key'];
        $astrologer_uni_id  =   $attributes['astrologer_uni_id'];
        $status             =   $attributes['status'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        if (!empty($status === '0')) {

            $astrodatetime = array(
                'online_status' =>   '0',
                'video_status' =>   '0',
                'call_status' =>   '0',
                'chat_status' =>   '0',
                'busy_status' =>   '0',
                'live_status' =>   '0',
            );
            Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->update($astrodatetime);
            Api::removeQueueList($request->astrologer_uni_id, 'Declined(Astrologer Offline)');
            foreach (array_keys($astrodatetime) as $value) {
                Api::userActivityUpdate($value, $request->astrologer_uni_id);
            }
        } else {
            Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->update(['online_status' => '1']);
            Api::userActivityCreate('online_status',  $request->status, $request->astrologer_uni_id);

            $attributes['type'] = 'online';
            Api::getNotificationTofollowers($attributes);
        }

        if ($status === '0') {
            $result = array(
                'status'     => 1,
                'msg'         => 'You are Offline',
            );
        } else {
            $result = array(
                'status'     => 1,
                'msg'         => 'You are Online',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is updateVideoCallStatus
    public function updateVideoCallStatus(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        if ($status == 0) {

            $astrologer = Astrologer::where('astrologer_uni_id', '=', $user_uni_id)->update(['video_status' =>   '0']);
            Api::userActivity('video_status',  $request->status, $request->astrologer_uni_id);
            $result = array(
                'status' => 1,
                'msg'     => 'You are Offline',
            );
        } else {

            $astrologer = Astrologer::where('astrologer_uni_id', '=', $user_uni_id)->update(['video_status'   =>   '1']);
            Api::userActivityCreate('video_status',  $request->status, $request->astrologer_uni_id);
            $result = array(
                'status' => 1,
                'msg'     => 'You are online',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    //this is updateCallStatus
    public function updateCallStatus(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        if ($status == 0) {

            $astrologer = Astrologer::where('astrologer_uni_id', '=', $user_uni_id)->update(['call_status'   =>   '0']);
            Api::userActivity('call_status',  $request->status, $request->astrologer_uni_id);
            $result = array(
                'status' => 1,
                'msg'     => 'You are Offline',
            );
        } else {

            $astrologer = Astrologer::where('astrologer_uni_id', '=', $user_uni_id)->update(['call_status'  =>   '1']);
            Api::userActivityCreate('call_status',  $request->status, $request->astrologer_uni_id);
            $result = array(
                'status' => 1,
                'msg'     => 'You are online',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    // this is updateChatStatus
    public function updateChatStatus(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        if ($status == 0) {

            $astrologer = Astrologer::where('astrologer_uni_id', '=', $user_uni_id)->update(['chat_status'  =>   '0']);
            Api::userActivity('chat_status',  $request->status, $request->astrologer_uni_id);
            $result = array(
                'status' => 1,
                'msg'     => 'You are Offline',
            );
        } else {

            $astrologer = Astrologer::where('astrologer_uni_id', '=', $user_uni_id)->update(['chat_status'  =>   '1']);
            Api::userActivityCreate('chat_status',  $request->status, $request->astrologer_uni_id);
            $result = array(
                'status' => 1,
                'msg'     => 'You are online',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is updateOnlineStatus
    public function updateOnlineStatus1(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['nullable'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $astrologer =   Astrologer::where('astrologer_uni_id', $request->astrologer_uni_id)->first();
        if (!empty($astrologer->online_status === 1) || ($request->status === 0)) {
            $arry = array(
                "online_status" => $request->status,
                'call_status' => 0,
                'chat_status' => 0,
                'video_status' => 0,
                'online_status' => 0,
            );
            $data =  $astrologer->update($arry);
            foreach (array_keys($arry) as $key => $value) {
                Api::userActivityUpdate($value, $request->astrologer_uni_id);
            }
            if ($data) {
                $result =  array(
                    'status' => 1,
                    'msg' => 'Status change Successfully & Your all Status Off',
                );
            } else {
                $result =  array(
                    'status' => 0,
                    'msg' => 'Something went wrong',
                );
            }
        } else {
            $data =  $astrologer->update(['online_status' => $request->status]);
            Api::userActivity('online_status',  $request->status, $request->astrologer_uni_id);
            if ($data) {
                $result =  array(
                    'status' => 1,
                    'msg' => 'Status change Successfully',
                );
            } else {
                $result =  array(
                    'status' => 0,
                    'msg' => 'Something went wrong',
                );
            }
        }


        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is updateVideoCallStatus
    public function updateVideoCallStatus1(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $astrologer =   Astrologer::where('astrologer_uni_id', $request->astrologer_uni_id)->first();
        // dd($astrologer);
        if ($astrologer->online_status == 1) {

            $data =  $astrologer->update(['video_status' => $request->status]);
            Api::userActivity('video_status',  $request->status, $request->astrologer_uni_id);
            if ($data) {
                $result =  array(
                    'status' => 1,
                    'msg' => 'Status change Successfully',
                );
            } else {
                $result =  array(
                    'status' => 0,
                    'msg' => 'Already update ',
                );
            }
        } else {
            $result =  array(
                'status' => 0,
                'msg' => 'Please first you active Online Status',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is updateCallStatus
    public function updateCallStatus1(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $astrologer =   Astrologer::where('astrologer_uni_id', $request->astrologer_uni_id)->first();
        // dd($astrologer);
        if ($astrologer->online_status == 1) {

            $data =  $astrologer->update(['call_status' => $request->status]);
            Api::userActivity('call_status',  $request->status, $request->astrologer_uni_id);
            if ($data) {
                $result =  array(
                    'status' => 1,
                    'msg' => 'Status change Successfully',
                );
            } else {
                $result =  array(
                    'status' => 0,
                    'msg' => 'Already update ',
                );
            }
        } else {
            $result =  array(
                'status' => 0,
                'msg' => 'Please first you active Online Status',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    // this is updateChatStatus
    public function updateChatStatus1(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'status' => ['required'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['astrologer_uni_id'];
        $status        =   $attributes['status'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $astrologer =   Astrologer::where('astrologer_uni_id', $request->astrologer_uni_id)->first();
        if ($astrologer->online_status == 1) {

            $data = $astrologer->update(['chat_status' => $request->status]);
            Api::userActivity('chat_status',  $request->status, $request->astrologer_uni_id);
            if ($data) {
                $result =  array(
                    'status' => 1,
                    'msg' => 'Status change Successfully',
                );
            } else {
                $result =  array(
                    'status' => 0,
                    'msg' => 'Already update ',
                );
            }
        } else {
            $result =  array(
                'status' => 0,
                'msg' => 'Please first you active Online Status',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is category
    public function categoryList(Request $request)
    {
        $api = saveapiLogs($request->all());
        // $validator = Validator::make($request->all(), [
        //     'id' => ['nullable'],
        //     'category_title' => ['nullable'],
        //     'astro_img' => ['nullable'],

        // ]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         "status" => 0,
        //         "errors" => $validator->errors(),
        //         "message" => 'Something went wrong'
        //     ]);
        // }
        // $attributes         =   $request->all();
        // $category_id = $attributes['id'];
        // if (!empty($category_id)) {
        //     $data = Category::where('id', '=', $category_id)->first();
        // }
        $data = [];
        $datas = Category::where('status', 1)->get();

        foreach ($datas as $key => $data) {

            $datas[$key]['category_images'] =  asset(config('constants.category_image_path') . $data->category_images);
        }

        if ($datas) {
            $result = array(
                'status' => 1,
                'data'     => $datas,
                'msg' => "all category List",
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'no data',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }


    //this is getBlog
    public function getBlog(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['nullable'],
            'user_uni_id' => ['nullable'],
            'astrologer_uni_id' => ['nullable'],
            'offset' => ['nullable'],
            'search' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        $offset = !empty($request->offset) ? $request->offset : '0';

        $limit = config('constants.api_page_limit');
        $attributes['status'] = 1;
        $attributes['limit'] =  $limit;
        $attributes['offset'] = $offset;
        $blogs = Api::getAllBlog($attributes);

        if (!empty($blogs) && $blogs->count() > 0) {
            $result = array(
                'status'    => 1,
                'msg'       => 'Result Found',
                'offset'    => $offset + $limit,
                'data'      => $blogs,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg' => 'No Record Found',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }
    public function getBlogs(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['nullable'],
            'user_uni_id' => ['nullable'],
            'astrologer_uni_id' => ['nullable'],
            'offset' => ['nullable'],
            'search' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        $offset = !empty($request->offset) ? $request->offset : '0';

        $limit = config('constants.api_page_limit');
        $request->status = 1;
        $request->limit =  $limit;
        $request->offset = $offset;
        $blogs = Api::getAllBlogNew($request);

        if (!empty($blogs) && $blogs->count() > 0) {
            $result = array(
                'status'    => 1,
                'msg'       => 'Result Found',
                'offset'    => $offset + $limit,
                'data'      => $blogs,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg' => 'No Record Found',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is saveAstrologerStep1
    public function saveAstrologerStep1(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'name' => ['required'],
            'email' => ['required'],
            'birth_date' => ['required'],
            'gender' => ['required'],
            'address' => ['required'],
            'house_no' => ['required'],
            'street_area' => ['required'],
            'landmark' => ['required'],
            'city' => ['nullable'],
            'state' => ['nullable'],
            'country' => ['nullable'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
            'pin_code' => ['required'],
            'experience' => ['required'],
            'display_name' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        $api_key                  =   $attributes['api_key'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        // if(){

        // }else{

        // }

        $astrologer = array(

            "display_name"    => !empty($attributes['display_name']) ? $attributes['display_name'] : '',
            "birth_date"      => !empty($attributes['birth_date']) ? $attributes['birth_date'] : '',
            "pin_code"        => !empty($attributes['pin_code']) ? $attributes['pin_code'] : '',
            "experience"      => !empty($attributes['experience']) ? $attributes['experience'] : '',
            "gender"          => !empty($attributes['gender']) ? $attributes['gender'] : '',
            "house_no"      => !empty($attributes['house_no']) ? $attributes['house_no'] : '',
            "street_area"     => !empty($attributes['street_area']) ? $attributes['street_area'] : '',
            "landmark"      => !empty($attributes['landmark']) ? $attributes['landmark'] : '',
            "address"            => !empty($attributes['address']) ? $attributes['address'] : '',
            "city"            => !empty($attributes['city']) ? $attributes['city'] : '',
            "state"            => !empty($attributes['state']) ? $attributes['state'] : '',
            "country"            => !empty($attributes['country']) ? $attributes['country'] : '',
            "longitude"      => !empty($attributes['longitude']) ? $attributes['longitude'] : '',
            "latitude"      => !empty($attributes['latitude']) ? $attributes['latitude'] : '',
        );

        $astroData = Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->first();
        if (!empty($astroData) && $astroData->process_status < 1) {
            $astrologer['process_status'] = 1;
        }

        $result = Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->update($astrologer);
        $users = array(
            "name"  => !empty($attributes['name']) ? $attributes['name'] : '',
            "email"  => !empty($attributes['email']) ? $attributes['email'] : '',
        );
        $result = User::where('user_uni_id', '=', $astrologer_uni_id)->update($users);



        if (($result)) {

            $filter = [];
            $filter['astrologer_uni_id'] = $astrologer_uni_id;
            $res = Api::getAstroData($filter, 1);

            $result = array(
                'status'    => 1,
                'data'      => $res,
                'msg'       => "User Data Successfully Updated",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'       => "Something Went wrong.. Try Again",
            );
        }
        // } else {
        //     $result = array(
        //         'status'     => 0,
        //         'msg'       => "Invalid Data",
        //     );
        // }
        updateapiLogs($api, $result);
        return response()->json($result);
    }
    //this is saveAstrologerStep2
    public function saveAstrologerStep2(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'language_id' => ['nullable'],
            'category_id' => ['nullable'],
            'skills_id' => ['nullable'],
            'long_biography' => ['nullable'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();


        $api_key                  =   $attributes['api_key'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];
        $language_id              =   $attributes['language_id'];
        $skills_id                =   $attributes['skills_id'];
        $category_id                 =   $attributes['category'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }



        $astro = Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->first();
        $id = $astro['id'];

        $language_id = str_replace('[', '', $language_id);
        $language_id = str_replace(']', '', $language_id);
        $language_id = str_replace(' ', '', $language_id);

        $category_id = str_replace('[', '', $category_id);
        $category_id = str_replace(']', '', $category_id);
        $category_id = str_replace(' ', '', $category_id);

        $skills_id = str_replace('[', '', $skills_id);
        $skills_id = str_replace(']', '', $skills_id);
        $skills_id = str_replace(' ', '', $skills_id);

        $languages  =   explode(",", $language_id);
        $categories  =   explode(",", $category_id);
        $skills  =   explode(",", $skills_id);

        $astro->languages()->sync($languages);
        $astro->skills()->sync($skills);
        $astro->categories()->sync($categories);

        $astrologer = array(
            "long_biography"           =>  $attributes['long_biography'],
        );

        if (!empty($astro) && $astro->process_status < 2) {
            $astrologer['process_status'] = 2;
        }

        $save = Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->update($astrologer);

        $filter = [];
        $filter['astrologer_uni_id'] = $astrologer_uni_id;
        $res =    Api::getAstroData($filter, 1);

        $result = array(
            'status'     => 1,
            'data' => $res,
            'msg'         => "User Data Successfully Updated",
        );


        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is saveAstrologerstep5
    public function saveAstrologerStep5(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'writing_experience' => ['nullable'],
            'writing_language' => ['nullable'],
            'writing_details' => ['nullable'],
            'teaching_experience' => ['nullable'],
            'teaching_subject' => ['nullable'],
            'teaching_year' => ['nullable'],
            'existing_website' => ['nullable'],
            'existing_fees' => ['nullable'],
            'associate_temple' => ['nullable'],
            'available_gadgets' => ['nullable'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key                  =   $attributes['api_key'];
        $astrologer_uni_id        =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $astrologer = array(
            "writing_experience"    => !empty($attributes['writing_experience']) ? $attributes['writing_experience'] : '',
            "writing_language"     => !empty($attributes['writing_language']) ? $attributes['writing_language'] : '',
            "writing_details"      => !empty($attributes['writing_details']) ? $attributes['writing_details'] : '',
            "teaching_experience"      => !empty($attributes['teaching_experience']) ? $attributes['teaching_experience'] : '',
            "teaching_subject"      => !empty($attributes['teaching_subject']) ? $attributes['teaching_subject'] : '',
            "teaching_year"      => !empty($attributes['teaching_year']) ? $attributes['teaching_year'] : '',
            "existing_website"  => !empty($attributes['existing_website']) ? $attributes['existing_website'] : '',
            "existing_fees"     => !empty($attributes['existing_fees']) ? $attributes['existing_fees'] : '',
            "associate_temple"  => !empty($attributes['associate_temple']) ? $attributes['associate_temple'] : '',
            "available_gadgets"        => !empty($attributes['available_gadgets']) ? $attributes['available_gadgets'] : '',
        );

        $step5 = Astrologer::where('astrologer_uni_id', '=', $astrologer_uni_id)->update($astrologer);
        $filter = [];
        $filter['astrologer_uni_id'] = $astrologer_uni_id;
        $res = Api::getAstroData($filter, 1);
        $result = array(
            'status'     => 1,
            'data' => $res,
            'msg'         => "User Data Successfully Updated",
        );


        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is customerEdit
    public function customerEdit(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'phone' => ['required'],
            'name' => ['required'],
            'email' => ['required'],
            'birth_date' => ['required'],
            'birth_time' => ['required'],
            'birth_place' => ['required'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
            'gender' => ['required'],
            // 'age' => ['nullable'],
            'customer_img' => ['nullable'],
            'city' => ['nullable'],
            'state' => ['nullable'],
            'country' => ['nullable'],
            'welcome_mail' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();

        $api_key                =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $cus_data       =   Customer::where('customer_uni_id', $user_uni_id)->first();
        $imgPath    =   public_path(config('constants.customer_image_path'));
        if (!empty($attributes['customer_img'])) {
            $img        =   'customer_img';
            $img_path   =   !empty($cus_data->customer_img) ? $imgPath . $cus_data->customer_img : '';

            if (file_exists($img_path)) {
                unlink($img_path);
            };

            $filename   =   UploadImage($request, $imgPath, $img);

            $attributes['customer_img'] = $filename;
        }
        $user_data = array(
            'name' => !empty($attributes['name']) ? $attributes['name'] : '',
            'user_uni_id' => !empty($attributes['user_uni_id']) ? $attributes['user_uni_id'] : '',
            'email' => !empty($attributes['email']) ? $attributes['email'] : '',
            'role_id' => 4,
            'phone' => !empty($attributes['phone']) ? $attributes['phone'] : '',
        );

        $user_cus = User::where('user_uni_id', '=', $user_uni_id)->first();
        $user_cus->update($user_data);

        $customer_data = array(
            'gender'       =>  !empty($attributes['gender']) ? $attributes['gender'] : '',
            'birth_date'   =>  !empty($attributes['birth_date']) ? $attributes['birth_date'] : '',
            'country'      =>  !empty($attributes['country']) ? $attributes['country'] : '',
            'state'        =>  !empty($attributes['state']) ? $attributes['state'] : '',
            'city'         =>  !empty($attributes['city']) ? $attributes['city'] : '',
            'customer_img' =>  !empty($attributes['customer_img']) ? $attributes['customer_img'] : '',
            'birth_place'  =>  !empty($attributes['birth_place']) ? $attributes['birth_place'] : '',
            'birth_time'   =>  !empty($attributes['birth_time']) ? $attributes['birth_time'] : '',
        );

        if (!empty($attributes['latitude'])) {
            $customer_data['latitude'] = $attributes['latitude'];
        }
        if (!empty($attributes['longitude'])) {
            $customer_data['longitude'] = $attributes['longitude'];
        }
        $process_status =   $user_cus->process_status;
        if (!empty($user_cus) && $user_cus->process_status < 1) {
            $customer_data['process_status'] = 1;
        }

        $user_data = Customer::where('customer_uni_id', '=', $user_uni_id)->update($customer_data);

        $filter_array = (object)[];
        $filter_array->user_uni_id = $user_uni_id;
        $data = Api::getUserData($filter_array, true);

        if (!empty($data)) {
            $user       =   Customer::where('customer_uni_id', $user_uni_id)->leftJoin('users', function ($join) {
                $join->on('users.user_uni_id', '=', 'customers.customer_uni_id');
            })->first();
            if ($user->welcome_mail != 1 && !empty($user->email) && $user_cus->process_status < 1) {
                $mail   =   MyCommand::SendNotification($user->user_uni_id, 'welcome-template-for-customer', 'welcome-template-for-customer');
                if ($mail) {
                    $attributes['welcome_mail'] =   1;
                    $mail =   array('welcome_mail' => $attributes['welcome_mail']);
                    User::where('user_uni_id', '=', $user_uni_id)->update($mail);
                }
            }
            $result = array(
                'status'     => 1,
                'data'         =>   $data,
                'msg'         => "User Data Successfully Updated",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function productCalculation(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'item' => ['required'],
            'vendor_uni_id' => ['required'],
            'product_id' => ['required'],
            'reference_id' => ['nullable'],
            'offer_code' => ['nullable'],
            'wallet_check' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();

        $api_key                =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result   =   Api::productCalculation($request);
        return response()->json($result);
    }

    public function productPurchase(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'item' => ['required'],
            'vendor_uni_id' => ['required'],
            'address_id' => ['required'],
            'product_id' => ['required'],
            'reference_id' => ['nullable'],
            'offer_code' => ['nullable'],
            'wallet_check' => ['nullable'],
            'payment_method' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();

        $api_key                =   $attributes['api_key'];
        $user_uni_id            =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $customerData = Customer::join('users', 'customers.customer_uni_id', '=', 'users.user_uni_id')->where('customer_uni_id', '=', $user_uni_id)->first();

        $result     =   Api::productPurchase($request);
        if(!empty($result)){
            $result->customerData = $customerData;

            if ($result->status == 1 && $result->payment_gateway_status == 0) {
                $order = Api::getProductOrderDetail($result->order_id);
                MyCommand::SendNotification($order->user_uni_id, 'product-order', 'product-order', $order);
                MyCommand::SendNotification($order->vendor_uni_id, 'vendor-product-order', 'vendor-product-order', $order);
            }elseif ($result->status == 1 && $result->payment_gateway_status == 1) {

                if ($request->payment_method == 'razorpay') {

                } elseif ($request->payment_method == 'CCAvenue') {

                    $CCAvenueGateway = new CCAvenueGateway();
                    $ccavenue_request = $CCAvenueGateway->request($result->payment_gateway);
                    $enc_val = '';

                    if (!empty($ccavenue_request->encRequest)) {
                        $enc_val = $ccavenue_request->encRequest;
                    }

                    if (!empty($enc_val)) {
                        $ccavenue_data = array(
                            'order_id'      => $result->order_id,
                            'access_code'   => config('ccavenue_access_code'),
                            'redirect_url'  => route("paymentresponseccavenueapp"),
                            'cancel_url'    => route("paymentresponseccavenueapp"),
                            'enc_val'       => $enc_val,
                            'merchant_id'   => config('ccavenue_merchant_id'),
                            'working_key'   => config('ccavenue_working_key'),
                            'currency'      => config('ccavenue_currency'),
                            'language'      => config('ccavenue_language'),
                        );

                        $result->ccavenue_data = $ccavenue_data;
                    } else {
                        $result->status = 0;
                        $result->msg = 'Something went Wrong on payment gateway. Please Try Again';
                    }

                } elseif ($request->payment_method == 'PhonePe') {

                    $PhonePeGateway = new PhonePeGateway();
                    $phonepe_data = $PhonePeGateway->requestApp($result->payment_gateway);

                    if($phonepe_data['status'] == 1){
                        $phonepe_data['order_id'] = $result->order_id;
                        $result->phonepe_data = $phonepe_data;
                    } else {
                        $result->status = 0;
                        $result->msg = 'Something went Wrong on payment gateway. Please Try Again';
                    }

                }

            }

        }

        return response()->json($result);
    }


    public function serviceCalculation(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
            'customer_uni_id' => ['required'],
            'service_assign_id' => ['required'],
            'wallet_check' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();

        $api_key                =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result   =   Api::serviceCalculation($request);
        return response()->json($result);
    }

    //this is servicePurchase
    public function servicePurchase(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'astrologer_uni_id' => ['required'],
            'customer_uni_id' => ['required'],
            'service_assign_id' => ['required'],
            'date' => ['required'],
            'time' => ['required'],
            'type' => ['nullable'],
            'order_id' => ['nullable'],
            'wallet_check' => ['nullable'],
            'payment_method' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $user_uni_id            =   $attributes['user_uni_id'];
        $customer_uni_id        =   $attributes['customer_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }


        $customerData = Customer::join('users', 'customers.customer_uni_id', '=', 'users.user_uni_id')->where('customer_uni_id', '=', $customer_uni_id)->first();

        $result     =   Api::servicePurchase($request);
        if(!empty($result)){
            if(empty($attributes['type'])){
                $result->customerData = $customerData;

                if ($result->status == 1 && $result->payment_gateway_status == 0) {

                }elseif ($result->status == 1 && $result->payment_gateway_status == 1){

                    if ($request->payment_method == 'razorpay') {

                    } elseif ($request->payment_method == 'CCAvenue') {

                        $CCAvenueGateway = new CCAvenueGateway();
                        $ccavenue_request = $CCAvenueGateway->request($result->payment_gateway);
                        $enc_val = '';

                        if (!empty($ccavenue_request->encRequest)) {
                            $enc_val = $ccavenue_request->encRequest;
                        }

                        if (!empty($enc_val)) {
                            $ccavenue_data = array(
                                'order_id'      => $result->order_id,
                                'access_code'   => config('ccavenue_access_code'),
                                'redirect_url'  => route("paymentresponseccavenueapp"),
                                'cancel_url'    => route("paymentresponseccavenueapp"),
                                'enc_val'       => $enc_val,
                                'merchant_id'   => config('ccavenue_merchant_id'),
                                'working_key'   => config('ccavenue_working_key'),
                                'currency'      => config('ccavenue_currency'),
                                'language'      => config('ccavenue_language'),
                            );

                            $result->ccavenue_data = $ccavenue_data;
                        } else {
                            $result->status = 0;
                            $result->msg = 'Something went Wrong on payment gateway. Please Try Again';
                        }

                    } elseif ($request->payment_method == 'PhonePe') {

                        $PhonePeGateway = new PhonePeGateway();
                        $phonepe_data = $PhonePeGateway->requestApp($result->payment_gateway);

                        if($phonepe_data['status'] == 1){
                            $phonepe_data['order_id'] = $result->order_id;
                            $result->phonepe_data = $phonepe_data;
                        } else {
                            $result->status = 0;
                            $result->msg = 'Something went Wrong on payment gateway. Please Try Again';
                        }

                    }

                }else{

                    if ($result->msg == "Order Already Exists") {
                        $result->error_code = 102;
                    }

                }
            }
        }

        return response()->json($result);
    }


    //this is customerServiceOrder
    public function customerServiceOrder(Request $request)
    {
        // dd($request);
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $user_uni_id            =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $customer_service     =   Api::customerServiceOrder($request);
        // dd($customer_service);
        if (!empty($customer_service)) {
            $result = array(
                'status' => 1,
                'data' => $customer_service,
                'msg' => "Customer Service Order List",
            );
        } else {
            $result = array(
                'status' => 0,
                'msg' => "Something went wrong.",
            );
        }


        return response()->json($result);
    }


    //this is astrologerServiceOrder
    public function astrologerServiceOrder(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $astrologer_uni_id            =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $astrologer_service = Api::astrologerServiceOrder($request);
        if (!empty($astrologer_service) && $astrologer_service->count() > 0) {
            $result = array(
                'status' => 1,
                'data' => $astrologer_service->toArray(),
                'msg' => "Astrologer Service Order List",
            );
        } else {
            $result = array(
                'status' => 0,
                'msg' => "Something went wrong.",
            );
        }

        return response()->json($result);
    }

    //this is serviceList
    public function serviceList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $astrologer_uni_id            =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result     =   Api::serviceList($request);

        return response()->json($result);
    }


    //this is serviceCategory
    public function serviceCategory(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();

        $records =  Api::ServiceCategory($request);
        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                // 'offset' =>  $offset + $page_limit,
                'msg'     => 'Address list',
            );
        } else {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'Address list',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is services
    public function services(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
            'search'        => ['nullable'],
            'service_category_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();

        $result = [];
        $offset = 0;
        $page_limit = config('constants.api_page_limit');

        $ServiceCat = Service::where('services.status', '1');
        $ServiceCat->select(
            "services.*",
            "service_categories.title",
        )
            // ->leftJoin("service_categories", "service_categories.id", "=", "services.service_category_id")->where('status', 1);
            ->leftJoin("service_categories", "service_categories.id", "=", "services.service_category_id");

        // dd($records);

        if (!empty($attributes['service_category_id'])) {
            $ServiceCat->where('services.service_category_id', $attributes['service_category_id']);
        }

        if (!empty($attributes['search'])) {
            $search = $attributes['search'];
                $ServiceCat->where(function ($query) use ($search) {
                $query->where('services.service_name', 'LIKE', '%' . $search . '%')
                    ->orwhere('services.service_category_id', 'LIKE', '%' . $search . '%');

            });
        }

        if (!empty($attributes['offset'])) {
            $offset = $attributes['offset'];
            $ServiceCat->offset($offset)->limit($page_limit);
        }

        $records = $ServiceCat->get();

        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'offset' =>  $offset + $page_limit,
                'msg'     => 'Service List',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'No Record Found',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }



    //this is servicesassign
    public function serviceAstrologerList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
            'service_id' => ['nullable'],
            'astrologer_uni_id' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();

        $result = [];

        $offset = 0;
        $page_limit = config('constants.api_page_limit');
        $attributes['limit'] = $page_limit;
        $attributes['offset'] = $attributes['offset'] ?? $offset;
        $records = ServiceAssign::getQuery($attributes);

        if (!empty($records) && $records->count() > 0) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'offset' =>  $attributes['offset'] + $page_limit,
                'msg'     => 'ServiceAssign List',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'No Record Found',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is assignService
    public function assignService(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'duration' => ['required'],
            'description' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes  =  $request->all();

        $service = ServiceAssign::where('astrologer_uni_id', $request->astrologer_uni_id)->where('service_id', $request->service_id)->first();
        // dd($service);
        if (empty($service)) {
            // dd('sss');
            $service_price = array(
                'service_id' => $request->service_id,
                'astrologer_uni_id' => $request->astrologer_uni_id,
                'price' => $request->price,
                'description' =>  !empty($request->description) ? $request->description : '',
                'duration' =>  !empty($request->duration) ? $request->duration : '',
            );
            $records = ServiceAssign::create($service_price);
            // dd($records);
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'ServiceAssign List',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Already Select This Assign Service',
            );
        }

        //  dd($result);



        // if (!empty($records)) {
        //     $result = array(
        //         'status' => 1,
        //         'data' => $records,
        //         'msg'     => 'ServiceAssign List',
        //     );
        // } else {
        //     $result = array(
        //         'status' => 0,
        //         'msg'     => 'No Record Found',
        //     );
        // }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    //remove service
    public function removeService(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'service_assign_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $astrologer_uni_id            =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $attributes         =   $request->all();

        $service_assign = ServiceAssign::find($attributes['service_assign_id']);

        if (!empty($service_assign)) {
            if ($service_assign->delete()) {
                $result = array(
                    'status' => 1,
                    'msg'     => 'Service delete successfully',
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'     => 'Something went wrong',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Invalid Service',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function serviceActive(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'service_assign_id' => ['required'],
            'status' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes             =   $request->all();
        $api_key                =   $attributes['api_key'];
        $astrologer_uni_id            =   $attributes['astrologer_uni_id'];
        if (!checkUserApiKey($api_key, $astrologer_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $attributes         =   $request->all();

        $service_assign = ServiceAssign::find($attributes['service_assign_id']);

        if (!empty($service_assign)) {
            $savaData['status'] = $attributes['status'];
            if ($service_assign->update($savaData)) {
                $msg = 'Inactive';
                if ($attributes['status'] == 1) {
                    $msg = 'Active';
                }

                $result = array(
                    'status' => 1,
                    'msg'     => 'Service ' . $msg . ' successfully',
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'     => 'Something went wrong',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Invalid Service',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    //this is upcomingLiveAstrologer
    public function upcomingLiveAstrologer(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
            // 'astrologer_uni_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();
        $offset =   0;
        $page_limit = config('constants.api_page_limit');

        $ServiceCat = LiveSchedule::with(['astrologer:id,astrologer_uni_id,display_name,slug,astro_img']);
        $ServiceCat->where('status', '1')->where('schedule_type', 'live');
            $ServiceCat->where(DB::raw("CONCAT(live_schedules.date, ' ', live_schedules.time)"), '>', config('current_datetime'));
            if (!empty($attributes['offset'])) {
                $offset = $attributes['offset'];
                $ServiceCat->offset($offset)->limit(config('constants.api_page_limit'));
            }
            // dd(getQueryWithBindings($ServiceCat));
            $records = $ServiceCat->get();
        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'msg'    => 'Upcoming Live Astrologer List',
                'offset' => $offset + $page_limit,
                'data'   => $records,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'    => 'No Record Found',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function kundaliCalulation(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'name' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
            'offer_code' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();

        $api_key                =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result   =   Api::KundaliCalulation($request);
        return response()->json($result);
    }

    public function kundaliPurchase(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'name' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
            'offer_code' => ['nullable'],
            'gender' => ['required'],
            'birth_date' => ['required'],
            'birth_time' => ['required'],
            'birth_place' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();

        $api_key                =   $attributes['api_key'];
        $user_uni_id            =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $result     =   Api::kundaliPurchase($request);
        return response()->json($result);
    }

    public function kundaliOrderList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $result = [];
        $showtime = 30;
        // $res = KundaliPayment::where([['user_uni_id', $user_uni_id], ['show_time', '<=', now()->subMinutes($showtime)->toDateTimeString()]])->orderBy('id', 'DESC')->get();

        $res = KundaliPayment::where([['user_uni_id', $user_uni_id]])->orderBy('id', 'DESC')->get();


        if (empty($res)) {
            $result = array(
                'status' => 0,
                'msg'     => 'No Record Found',
            );
        } else {
            $result = array(
                'status' => 1,
                'data' => $res,
                'msg'     => 'Kundali list',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function sendMailKundali(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'kundali_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes             =   $request->all();

        $api_key                =   $attributes['api_key'];
        $user_uni_id            =   $attributes['user_uni_id'];
        $kundali_uni_id            =   $attributes['kundali_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $result   =   Api::sendKundali($kundali_uni_id);
        return response()->json($result);
    }
    //

    public function editAddress(Request $request)
    {
        // dd($request);
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'id' => ['required'],
            'name' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
            'house_no' => ['nullable'],
            'street_area' => ['nullable'],
            'landmark' => ['nullable'],
            'address' => ['nullable'],
            'city' => ['nullable'],
            'state' => ['nullable'],
            'country' => ['nullable'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
            'pincode' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        unset($attributes['api_key'], $attributes['_token']);
        $address = UserAddress::where('id', $attributes['id'])->update($attributes);
        if (!empty($address)) {
            $result = array(
                'status' => 1,
                'data' => $address,
                'msg'     => 'User Address Updated',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Something went wrong',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function deleteAddress(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'address_id' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $address = UserAddress::where('id', $attributes['address_id'])->delete();
        if (!empty($address)) {
            $result = array(
                'status' => 1,
                'data' => $address,
                'msg'     => 'User Address Deleted Succeessfuly',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'Something went wrong',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function testimonials(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes         =   $request->all();

        $offset = 0;
        $page_limit = config('constants.api_page_limit');

        $thismodel = Testimonial::where('status', 1);
        if (!empty($attributes['offset'])) {
            $offset = $attributes['offset'];
            $thismodel->offset($offset)->limit($page_limit);
        }

        $records = $thismodel->get();


        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'offset' =>  $offset + $page_limit,
                'msg'     => 'testimonial list',
            );
        } else {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'testimonial list',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function prokeralaKundli(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'ayanamsa' => ['required'],
            'type' => ['required'],
            'coordinates' => ['required'],
            'language' => ['required'],
            'date' => ['nullable'],
            'time' => ['nullable'],
            'name' => ['nullable'],
            'is_save' => ['nullable'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        // dd($user_uni_id);
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }


        $access_token_arr = Api::prokerala_access_token();
        if (!empty($access_token_arr['access_token'])) {

            $response = Api::getProkerlaData($request, $access_token_arr);
            if ($response['status'] == 'ok') {

                if (!empty($attributes['is_save']) && $attributes['is_save'] == 1) {
                    unset($attributes['api_key']);
                    unset($attributes['is_save']);

                    $coordinates = explode(',', $attributes['coordinates']);
                    $attributes['latitude'] = !empty($coordinates[0]) ? $coordinates[0] : '';
                    $attributes['longitude'] = !empty($coordinates[1]) ? $coordinates[1] : '';
                    $attributes['content'] = json_encode($response);

                    UserKundali::create($attributes);
                }

                $user_data = Api::getCustomerById($user_uni_id);
                $user_data['api_key'] = $api_key;

                $result = array(
                    'status' => 1,
                    'msg'    => 'Kundli created successfully.',
                    'data'   => $response,
                    'user_data'      => $user_data,
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'    => 'Oops! Something is wrong please try again.',
                    'data'      => '',
                    'user_data' => '',
                );
            }
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function prokeralaChart(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'ayanamsa' => ['required'],
            'coordinates' => ['required'],
            'date' => ['required'],
            'time' => ['required'],
            'chart_type' => ['required'],
            'chart_style' => ['required'],
            'format' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $access_token_arr =  Api::prokerala_access_token();
        if (!empty($access_token_arr['access_token'])) {

            // $ayanamsa = '1';
            // $coordinates = '10.214747,78.097626';
            // $datetime = '2004-02-12T15:19:21%2B05:30';

            $ayanamsa = $attributes['ayanamsa'];
            $coordinates = $attributes['coordinates'];

            $date = $attributes['date'];
            $date = date('Y-m-d', strtotime($date));
            $time = $attributes['time'];
            $datetime = $date . 'T' . $time . '%2B05:30';


            $chart_type = $attributes['chart_type'];
            $chart_style = $attributes['chart_style'];
            $format = $attributes['format'];

            $url = "https://api.prokerala.com/v2/astrology/chart?ayanamsa=" . $ayanamsa . "&coordinates=" . $coordinates . "&datetime=" . $datetime . "&chart_type=" . $chart_type . "&chart_style=" . $chart_style . "&format=" . $format;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $access_token_arr['access_token']
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $user_data = Api::getCustomerById($user_uni_id);
            $user_data['api_key'] = $api_key;

            $result = array(
                'status'     => 1,
                'msg'        => 'Chart created successfully.',
                'data'       => $response,
                'user_data'  => $user_data,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'    => 'Oops! Something is wrong please try again.',
                'data'      => '',
                'user_data' => '',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function prokeralaPanchang(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }


        $arry = (object)array(
            "ayanamsa" => "1",
            "coordinates" => Config::get("company_lat") . ',' . Config::get("company_long"),
            "type" => "panchang",
            "date" => Config::get("current_date"),
            "time" => Config::get("current_time"),
            "language" => "en",
        );
        $access_token_arr = Api::prokerala_access_token();
        if (!empty($access_token_arr['access_token'])) {

            $response = Api::getProkerlaData($arry, $access_token_arr);
            // dd($response);
            if ($response['status'] == 'ok') {
                $user_data = Api::getCustomerById($user_uni_id);
                $user_data['api_key'] = $api_key;

                $result = array(
                    'status' => 1,
                    'msg'    => 'Panchang created successfully.',
                    'data'   => $response,
                    'user_data'      => $user_data,
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'    => 'Oops! Something is wrong please try again.',
                    'data'      => '',
                    'user_data' => '',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'    => 'Oops! Something is wrong please try again.',
                'data'      => '',
                'user_data' => '',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function prokeralaDailyHoroscope(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'sign' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        unset($attributes['_token']);
        $access_token_arr = Api::prokerala_access_token();
        if (!empty($access_token_arr['access_token'])) {
            $current = Config::get('current_datetime');
            $sign = $attributes['sign'];
            $date = date('Y-m-d', strtotime($current));
            $time = date('H:i:s', strtotime($current));
            $datetime = $date . 'T' . $time . '%2B05:30';
            $url = "https://api.prokerala.com/v2/horoscope/daily?datetime=" . $datetime . "&sign=" . $sign;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $access_token_arr['access_token']
                ),
            ));

            $data = curl_exec($curl);
            curl_close($curl);
            $response   =    json_decode($data, true);
            if ($response['status'] == 'ok') {
                $user_data = Api::getCustomerById($user_uni_id);
                $user_data['api_key'] = $api_key;

                $result = array(
                    'status' => 1,
                    'msg'    => 'Success',
                    'response'   => $response,
                    'user_data'  => $user_data,
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'    => 'Oops! Something is wrong please try again.',
                    'data'      => '',
                    'user_data' => '',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'    => 'Oops! Something is wrong please try again.',
                'data'      => '',
                'user_data' => '',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }



    public function prokeralaKundaliMatching(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'ayanamsa' => ['required'],
            'language' => ['required'],
            'girl_date' => ['required'],
            'girl_time' => ['required'],
            'girl_coordinates' => ['required'],
            'boy_date' => ['required'],
            'boy_time' => ['required'],
            'boy_coordinates' => ['required'],
            'name' => ['required'],
            'is_save' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $access_token_arr = Api::prokerala_access_token();
        if (!empty($access_token_arr['access_token'])) {
            $response = Api::getProkerlaKundaliMatchData($request, $access_token_arr);
            if ($response['status'] == 'ok') {

                if (!empty($attributes['is_save']) && $attributes['is_save'] == 1) {
                    unset($attributes['api_key']);
                    unset($attributes['is_save']);

                    $coordinates = explode(',', $attributes['coordinates']);
                    $attributes['latitude'] = !empty($coordinates[0]) ? $coordinates[0] : '';
                    $attributes['longitude'] = !empty($coordinates[1]) ? $coordinates[1] : '';
                    $attributes['content'] = json_encode($response);

                    UserKundali::create($attributes);
                }

                $user_data = Api::getCustomerById($user_uni_id);
                $user_data['api_key'] = $api_key;

                $result = array(
                    'status' => 1,
                    'msg'    => 'Success',
                    'response'   => $response,
                    'user_data'      => $user_data,
                );
            } else {
                $result = array(
                    'status' => 0,
                    'msg'    => 'Oops! Something is wrong please try again.',
                    'data'      => '',
                    'user_data' => '',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'    => 'Oops! Something is wrong please try again.',
                'data'      => '',
                'user_data' => '',
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function vedicAstroKundli(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'dob' => ['required'],
            'tob' => ['required'],
            'lat' => ['required'],
            'lon' => ['required'],
            'tz' => ['required'],

            'div' => ['required'],
            'color' => ['required'],
            'style' => ['required'],
            'font_size' => ['required'],
            'font_style' => ['required'],
            'colorful_planets' => ['required'],
            'size' => ['required'],
            'stroke' => ['required'],

            'lang' => ['required'],
            'is_save' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $dob        =  $attributes['dob'];
        $tob        =  $attributes['tob'];
        $lat        =  $attributes['lat'];
        $lon        =  $attributes['lon'];
        $tz         =  $attributes['tz'];
        $div        =  $attributes['div'];
        $color      =  $attributes['color'];
        $style      =  $attributes['style'];
        $font_size  =  $attributes['font_size'];
        $font_style =  $attributes['font_style'];
        $colorful_planets   =  $attributes['colorful_planets'];
        $size       =  $attributes['size'];
        $stroke     =  $attributes['stroke'];
        $lang     =  $attributes['lang'];

        $vedicAstro =  new vedicAstro();
        $personalCharacteristics = $vedicAstro->personalCharacteristics($dob, $tob, $lat, $lon, $tz, $lang);
        $chartImage = $vedicAstro->chartImage($dob, $tob, $lat, $lon, $tz, $div, $color, $style, $font_size, $font_style, $colorful_planets, $size, $stroke, $lang);
        $planetDetails = $vedicAstro->planetDetails($dob, $tob, $lat, $lon, $tz, $lang);
        $mahadasha = $vedicAstro->mahadasha($dob, $tob, $lat, $lon, $tz, $lang);
        $currentSadeSati = $vedicAstro->currentSadeSati($dob, $tob, $lat, $lon, $tz, $lang);

        if (!empty($personalCharacteristics->status) && $personalCharacteristics->status != 200) {
            $personalCharacteristics = '';
        }
        if (!empty($chartImage->message)) {
            $chartImage = '';
        }
        if (!empty($planetDetails->status) && $planetDetails->status != 200) {
            $planetDetails = '';
        }
        if (!empty($mahadasha->status) && $mahadasha->status != 200) {
            $mahadasha = '';
        }
        if (!empty($currentSadeSati->status) && $currentSadeSati->status != 200) {
            $currentSadeSati = '';
        }

        $vedicAstroData = array('personalCharacteristics' => $personalCharacteristics, 'chartImage' => $chartImage, 'planetDetails' => $planetDetails, 'mahadasha' => $mahadasha, 'currentSadeSati' => $currentSadeSati);

        if (!empty($personalCharacteristics) || !empty($planetDetails) || !empty($mahadasha) || !empty($currentSadeSati) || !empty($chartImage)) {

            if (!empty($attributes['is_save']) && $attributes['is_save'] == 1) {
                unset($attributes['api_key']);
                unset($attributes['is_save']);

                $attributes['content'] = json_encode($vedicAstroData);
                $attributes['birth_date'] = $attributes['dob'];
                $attributes['birth_time'] = $attributes['tob'];
                $attributes['latitude'] = $attributes['lat'];
                $attributes['longitude'] = $attributes['lon'];

                unset($attributes['dob']);
                unset($attributes['tob']);
                unset($attributes['lat']);
                unset($attributes['lon']);
                unset($attributes['tz']);
                unset($attributes['div']);
                unset($attributes['color']);
                unset($attributes['style']);
                unset($attributes['font_size']);
                unset($attributes['font_style']);
                unset($attributes['colorful_planets']);
                unset($attributes['size']);
                unset($attributes['stroke']);

                UserKundali::create($attributes);
            }

            $user_data = Api::getCustomerById($user_uni_id);
            $user_data['api_key'] = $api_key;

            $result = array(
                'status' => 1,
                'msg'    => 'Kundli created successfully.',
                'data'   => $vedicAstroData,
                'user_data' => $user_data,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'    => 'Oops! Something is wrong please try again.',
                'data'   => '',
                'user_data' => '',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function vedicAstroKundliMatching(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'boy_dob' => ['required'],
            'boy_tob' => ['required'],
            'boy_tz' => ['required'],
            'boy_lat' => ['required'],
            'boy_lon' => ['required'],
            'girl_dob' => ['required'],
            'girl_tob' => ['required'],
            'girl_tz' => ['required'],
            'girl_lat' => ['required'],
            'girl_lon' => ['required'],
            'boy_star' => ['required'],
            'girl_star' => ['required'],
            'lang' => ['required'],
            'is_save' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $boy_dob    =  $attributes['boy_dob'];
        $boy_tob    =  $attributes['boy_tob'];
        $boy_tz     =  $attributes['boy_tz'];
        $boy_lat    =  $attributes['boy_lat'];
        $boy_lon    =  $attributes['boy_lon'];
        $girl_dob   =  $attributes['girl_dob'];
        $girl_tob   =  $attributes['girl_tob'];
        $girl_tz    =  $attributes['girl_tz'];
        $girl_lat   =  $attributes['girl_lat'];
        $girl_lon   =  $attributes['girl_lon'];
        $boy_star   =  $attributes['boy_star'];
        $girl_star   =  $attributes['girl_star'];
        $lang       =  $attributes['lang'];

        $vedicAstro =  new vedicAstro();
        $northKundliMatchWithAstroDetails = $vedicAstro->northKundliMatchWithAstroDetails($boy_dob, $boy_tob, $boy_tz, $boy_lat, $boy_lon, $girl_dob, $girl_tob, $girl_tz, $girl_lat, $girl_lon, $lang);
        $southKundliMatchWithAstroDetails = $vedicAstro->southKundliMatchWithAstroDetails($boy_dob, $boy_tob, $boy_tz, $boy_lat, $boy_lon, $girl_dob, $girl_tob, $girl_tz, $girl_lat, $girl_lon, $lang);
        $aggregateMatch = $vedicAstro->aggregateMatch($boy_dob, $boy_tob, $boy_tz, $boy_lat, $boy_lon, $girl_dob, $girl_tob, $girl_tz, $girl_lat, $girl_lon, $lang);
        $nakshatraMatch = $vedicAstro->nakshatraMatch($boy_star, $girl_star, $lang);

        if (!empty($northKundliMatchWithAstroDetails->status) && $northKundliMatchWithAstroDetails->status != 200) {
            $northKundliMatchWithAstroDetails = '';
        }
        if (!empty($southKundliMatchWithAstroDetails->status) && $southKundliMatchWithAstroDetails->status != 200) {
            $southKundliMatchWithAstroDetails = '';
        }
        if (!empty($aggregateMatch->status) && $aggregateMatch->status != 200) {
            $aggregateMatch = '';
        }
        if (!empty($nakshatraMatch->status) && $nakshatraMatch->status != 200) {
            $nakshatraMatch = '';
        }

        $vedicAstroData = array('northKundliMatchWithAstroDetails' => $northKundliMatchWithAstroDetails, 'southKundliMatchWithAstroDetails' => $southKundliMatchWithAstroDetails, 'aggregateMatch' => $aggregateMatch, 'nakshatraMatch' => $nakshatraMatch);


        if (!empty($northKundliMatchWithAstroDetails) || !empty($southKundliMatchWithAstroDetails) || !empty($aggregateMatch) || !empty($nakshatraMatch)) {

            if (!empty($attributes['is_save']) && $attributes['is_save'] == 1) {
                unset($attributes['api_key']);
                unset($attributes['is_save']);

                $attributes['content'] = json_encode($vedicAstroData);
                // $attributes['birth_date'] = $attributes['dob'];
                // $attributes['birth_time'] = $attributes['tob'];
                // $attributes['latitude'] = $attributes['lat'];
                // $attributes['longitude'] = $attributes['lon'];

                unset($attributes['boy_dob']);
                unset($attributes['boy_tob']);
                unset($attributes['boy_tz']);
                unset($attributes['boy_lat']);
                unset($attributes['boy_lon']);
                unset($attributes['girl_dob']);
                unset($attributes['girl_tob']);
                unset($attributes['girl_tz']);
                unset($attributes['girl_lat']);
                unset($attributes['girl_lon']);
                unset($attributes['boy_star']);
                unset($attributes['girl_star']);

                UserKundali::create($attributes);
            }

            $user_data = Api::getCustomerById($user_uni_id);
            $user_data['api_key'] = $api_key;

            $result = array(
                'status' => 1,
                'msg'    => 'Success',
                'data'  => $vedicAstroData,
                'user_data' => $user_data,
            );
        } else {
            $msg = 'Oops! Something is wrong please try again.';
            $result = array(
                'status' => 0,
                'msg'    => $msg,
                'data'  => '',
                'user_data' => '',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function saveKundliData(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'name' => ['required_if:kundali_type,kundli'],
            'dob' => ['required_if:kundali_type,kundli'],
            'tob' => ['required_if:kundali_type,kundli'],
            'lat' => ['required_if:kundali_type,kundli'],
            'lon' => ['required_if:kundali_type,kundli'],
            'timezone' => ['required_if:kundali_type,kundli'],
            'place' => ['required_if:kundali_type,kundli'],

            'boy_name' => ['required_if:kundali_type,kundli_matching'],
            'boy_dob' => ['required_if:kundali_type,kundli_matching'],
            'boy_tob' => ['required_if:kundali_type,kundli_matching'],
            'boy_tz' => ['required_if:kundali_type,kundli_matching'],
            'boy_lat' => ['required_if:kundali_type,kundli_matching'],
            'boy_lon' => ['required_if:kundali_type,kundli_matching'],
            'boy_place' => ['required_if:kundali_type,kundli_matching'],
            'girl_name' => ['required_if:kundali_type,kundli_matching'],
            'girl_dob' => ['required_if:kundali_type,kundli_matching'],
            'girl_tob' => ['required_if:kundali_type,kundli_matching'],
            'girl_tz' => ['required_if:kundali_type,kundli_matching'],
            'girl_lat' => ['required_if:kundali_type,kundli_matching'],
            'girl_lon' => ['required_if:kundali_type,kundli_matching'],
            'girl_place' => ['required_if:kundali_type,kundli_matching'],

            'kundali_method' => ['required'],
            'kundali_type' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key             =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        unset($attributes['api_key']);

        $attributes['request_body'] = json_encode($attributes);

        if($attributes['kundali_type'] == 'kundli'){
            unset($attributes['dob']);
            unset($attributes['tob']);
            unset($attributes['lat']);
            unset($attributes['lon']);
            unset($attributes['place']);
            unset($attributes['timezone']);
        }elseif($attributes['kundali_type'] == 'kundli_matching'){
            $attributes['name'] = $attributes['boy_name'].' and '.$attributes['girl_name'];
            unset($attributes['boy_name']);
            unset($attributes['boy_dob']);
            unset($attributes['boy_tob']);
            unset($attributes['boy_tz']);
            unset($attributes['boy_lat']);
            unset($attributes['boy_lon']);
            unset($attributes['boy_place']);
            unset($attributes['girl_name']);
            unset($attributes['girl_dob']);
            unset($attributes['girl_tob']);
            unset($attributes['girl_tz']);
            unset($attributes['girl_lat']);
            unset($attributes['girl_lon']);
            unset($attributes['girl_place']);
        }


        $user_kundli = UserKundali::create($attributes);

        if (!empty($user_kundli)) {

            $user_data = Api::getCustomerById($user_uni_id);
            $user_data['api_key'] = $api_key;

            $result = array(
                'status' => 1,
                'msg'    => 'Kundli data save successfully.',
                'user_data' => $user_data,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'    => 'Oops! Something is wrong please try again.',
                'user_data' => '',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }



    //this is notice
    public function getNotice(Request $request)
    {
        $api = saveapiLogs($request->all());

        $thismodel = Notice::where('status', 1);
        $records = $thismodel->first();

        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'notice list',
            );
        } else {
            $result = array(
                'status' => 1,
                'data' => $records,
                'msg'     => 'notice list',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function cronRazorpayPayment(Request $request)
    {
        $filter = $request->query();

        ini_set('max_execution_time', 0);
        ini_set('max_input_time ', 0);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $offset = 0;
        $page_limit = 100;
        $date = date('Y-m-d H:i:s', strtotime('-30 minutes'));
        $dayBefore = date('Y-m-d H:i:s', strtotime('-7 minutes'));

        $thismodel = Wallet::orderBy('id', 'DESC');

        if (!empty($filter['id'])) {
            $thismodel->where('id', $filter['id']);
        }

        if (!empty($filter['gateway_order_id'])) {
            $thismodel->where('gateway_order_id', $filter['gateway_order_id']);
        }

        if (!empty($filter['gateway_payment_id'])) {
            $thismodel->where('gateway_payment_id', $filter['gateway_payment_id']);
        }

        if (!empty($filter['user_uni_id'])) {
            $thismodel->where('user_uni_id', $filter['user_uni_id']);
        }

        if (!empty($filter['page'])) {
            $offset = ($filter['page'] - 1) * $page_limit;
        }


        $thismodel->where('created_at', '<=', $date);
        // $thismodel->where('gateway_order_id', 'like', 'order_%');
        $thismodel->where('transaction_code', '=', 'add_wallet');
        $thismodel->where('status', '=', '0');

        $thismodel->offset($offset)->limit($page_limit);
        // dd(getQueryWithBindings($thismodel));
        $wallets = $thismodel->get();
        echo $wallets->count();
        // pr($wallets);die;
        if ($wallets->count() > 0) {
            foreach ($wallets as $wallet) {

                if($wallet->payment_method == 'razorpay'){

                    echo '<br>gateway_order_id: ' . $wallet->gateway_order_id;
                    ob_start();
                    $RazorpayApi = new RazorpayApi();
                    $getApiData = $RazorpayApi->fetchOrderId($wallet->gateway_order_id);
                    ob_end_clean();
                    // pr($getApiData);die;
                    if (!empty($getApiData['status'] && !empty($getApiData['data']['items']))) {
                        $res = $getApiData['data']['items'];
                        $findFlag = false;
                        foreach ($res as $re) {
                            // echo '<br>stastus: '.$re['status'];
                            if (!empty($re['status']) && $re['status'] == 'captured') {
                                $updateData = [];
                                $updateData['status'] = '1';
                                $updateData['gateway_payment_id'] = $re['id'];
                                // $updateData['gateway_order_id'] = $wallet->reference_id;
                                // $updateData['reference_id'] = '';

                                // GST Amount = ( Original Cost * GST% ) / 100
                                // GST Amount = Original Cost – (Original Cost * (100 / (100 + GST% ) ) )
                                // if (!empty($res['items'][0]['amount']) && $res['items'][0]['amount'] > 0) {
                                //     $updateData['transaction_amount'] = round($res['items'][0]['amount'] / 100, 2);
                                //     // $updateData['gst_amount'] = ($updateData['transaction_amount'] * config('gst')) / 100;
                                //     $updateData['gst_amount'] = $updateData['transaction_amount'] - ($updateData['transaction_amount'] * (100 / (100 + config('gst'))));
                                // }

                                Wallet::where('id', $wallet->id)->update($updateData);
                                $findFlag = true;
                            }
                        }

                        if ($wallet->created_at < $dayBefore && $findFlag == false) {
                            $updateData = [];
                            $updateData['status'] = '2';
                            // $updateData['gateway_order_id'] = $wallet->reference_id;
                            // $updateData['reference_id'] = '';
                            Wallet::where('id', $wallet->id)->update($updateData);
                        }
                    } else {
                        $updateData = [];
                        $updateData['status'] = '2';
                        // $updateData['gateway_order_id'] = $wallet->reference_id;
                        // $updateData['reference_id'] = '';
                        Wallet::where('id', $wallet->id)->update($updateData);
                    }

                }elseif($wallet->payment_method == 'CCAvenue'){



                }elseif($wallet->payment_method == 'PhonePe'){
                    $attributes = array();
                    $attributes['transactionId'] = $wallet->gateway_order_id;
                    $attributes['merchantId'] = config('phonepe_merchant_id');

                    $PhonePeGateway = new PhonePeGateway();
                    $phonepe_response = $PhonePeGateway->response($attributes);
                    if (!empty($phonepe_response['payment_id'])) {
                        $phonepe_response['payment_id'] = $phonepe_response['payment_id'];
                        $phonepe_response['payment_method'] = 'PhonePe';
                        Api::updateOnlinePayment($phonepe_response);
                    } else {
                        $updateData = [];
                        $updateData['status'] = '2';
                        Wallet::where('id', $wallet->id)->update($updateData);
                    }
                }


            }
        }


        Api::cronRefreshCall();
        Api::cronRefreshPendingService();
        // pr($wallets);
        // echo '<br>Done';
        // die;
    }

    public function withdrawalRequest(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'request_amount' => ['required'],
            'request_message' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $withdrawal = array(
            'user_uni_id' => $request->user_uni_id,
            'request_amount' => $request->request_amount,
            'request_message' => $request->request_message,
        );
        $res = WithdrawalRequest::create($withdrawal);

        if (!empty($res)) {
            $result = array(
                'status' => 1,
                'msg' => 'Withdrawal request saved successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "NO record",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function unregisteredUser(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'device_id' => ['required'],
            'user_fcm_token' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $attributes['status'] = 1;

        $resuser = UnregisteredUser::create($attributes);

        if (!empty($resuser)) {
            $result = array(
                'status' => 1,
                'msg' => 'Unregistered user saved successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "NO record",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }
    public function videoSections(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes = $request->all();
        $offset = $attributes['offset'];
        $page_limit = config('constants.api_page_limit');

        $records = Api::videoSections($request);

        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'offset' =>  $offset + $page_limit,
                'msg'     => 'video section list',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'NO record found',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function addWithdrawalRequest(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
            'request_amount' => ['required'],
            'request_message' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes  = $request->all();
        $api_key = $attributes['api_key'];
        $user_uni_id = $attributes['astrologer_uni_id'];
        $request_amount = $attributes['request_amount'];
        $request_message = $attributes['request_message'];
        $requestcheck = WithdrawalRequest::where('user_uni_id', $user_uni_id)->where('status', '0')
            ->first();
        if (empty($requestcheck)) {
            if (!checkUserApiKey($api_key, $user_uni_id)) {
                $result = array(
                    'status' => 0,
                    'error_code' => 101,
                    'msg'     => 'Unauthorized User... Please login again',
                );
                return response()->json($result);
            }

            $withdrawal = array(
                'user_uni_id' => $user_uni_id,
                'request_amount' => $request_amount,
                'request_message' => $request_message,
            );

            $balance = Api::getTotalBalanceById($user_uni_id);
            if ($balance >= $request_amount) {
                $res = WithdrawalRequest::create($withdrawal);
                if (!empty($res)) {
                    $result = array(
                        'status' => 1,
                        'msg' => 'Withdrawal request saved successfully',
                    );
                } else {
                    $result = array(
                        'status' => 0,
                        'msg'     => "Something went wrong",
                    );
                }
            } else {
                $result = array(
                    'status' => 0,
                    'msg'     => "Low balance, Please Check balance",
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "Already Exists",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function getWithdrawalRequest(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
            'api_key' => ['required'],
            'astrologer_uni_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes = $request->all();
        $user_uni_id = $attributes['astrologer_uni_id'];
        $offset = !empty($attributes['offset']) ? $attributes['offset'] : 0;
        $page_limit = config('constants.api_page_limit');

        $records = Api::getWithdrawalRequest($request);

        $ysday = date('Y-m-d', strtotime('-1 day'));
        $currentdate = date('Y-m-d');
        $amount_balance = Api::astroIncome($user_uni_id, $ysday, $ysday);
        $yesterday_earning = $amount_balance;


        $amount_balance = Api::astroIncome($user_uni_id, $currentdate, $currentdate);
        $today_earning = $amount_balance;

        $amount_balance = Api::astroIncome($user_uni_id);
        $total_earning = $amount_balance;
        $total_balance =  Api::getTotalBalanceById($user_uni_id);

        $income['today_earning'] = !empty($today_earning) ? round($today_earning, 2) : 0;
        $income['yesterday_earning'] = !empty($yesterday_earning) ? round($yesterday_earning, 2) : 0;
        $income['total_earning'] = !empty($total_earning) ? round($total_earning, 2) : 0;
        $income['total_balance'] = !empty($total_balance) ? round($total_balance, 2) : 0;



        if (!empty($records)) {
            $result = array(
                'status' => 1,
                'data' => $records,
                'offset' =>  $offset + $page_limit,
                'income' =>  $income,
                'msg'     => 'Withdrawal Request list',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => 'NO record found',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function suggestionsRequest(Request $request)
    {

        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'feedback' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();

        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }

        $withdrawal = array(
            'user_uni_id' => $request->user_uni_id,
            'feedback' => $request->feedback
        );
        $res = Suggestion::create($withdrawal);

        if (!empty($res)) {

            $template = (object) array(
                'subject' => 'Suggestions Request',
                'content' => 'User ID : ' . $request->user_uni_id . '<br>' . $request->feedback,
                'template_code' => 'default',
            );
            MyCommand::sendMailToAdmin($template);

            $result = array(
                'status' => 1,
                'msg' => 'Suggestion request saved successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "NO record",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }

    public function userkundaliRequest(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'offset' => ['nullable'],
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
            'for_id' => ['required'],
            'kundali_method' => ['required'],
            'kundali_type' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        // $offset = 0;
        $limit = config('constants.api_page_limit');
        $request->limit = $limit;
        $offset = !empty($attributes['offset']) ? $attributes['offset'] : '0';

        $api_key       =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }


        $UserKundali = UserKundali::where('user_uni_id', "=", $request->for_id)->where('kundali_method', "=", $request->kundali_method)->where('kundali_type', "=", $request->kundali_type);

        $UserKundali->offset($offset)->limit($limit);

        $records = $UserKundali->get();

        // $res['for_id'] = $request->for_id;

        // dd($res);
        if (!empty($records)) {

            for($i=0;$i<count($records);$i++){
                $records[$i]->request_body = json_decode($records[$i]->request_body);
            }

            $result = array(
                'status' => 1,
                'data' => $records,
                'offset'    => $offset + $limit,
                'msg' => 'UserKundali data get successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg'     => "NO record",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }





    public function vedicAstroPrediction(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'zodiac' => ['required'],
            'prediction_date' => ['required'],
            'show_same' => ['required'],
            'prediction_type' => ['required'],
            'lang' => ['required'],
            'is_app' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes = $request->all();

        $zodiac             = $attributes['zodiac'];
        $prediction_date    = $attributes['prediction_date']; // For prediction_type Weekly : thisweek, nextweek | For prediction_type Yearly : Ex 2024 etc
        $show_same          = $attributes['show_same'];
        $prediction_type    = $attributes['prediction_type']; // Daily | Weekly | Yearly
        $lang               = $attributes['lang'];
        $content_type       = 'prediction';
        $is_app             = $attributes['is_app'];

        if ($is_app == "false") {
            if ($prediction_type == 'Daily') {
                $prediction_date = date('d/m/Y', strtotime($prediction_date));
            } elseif ($prediction_type == 'Tomorrow') {
                $prediction_date = date('d/m/Y', strtotime($prediction_date . ' +1 day'));
            } elseif ($prediction_type == 'Yearly') {
                $prediction_date = date('Y', strtotime($prediction_date));
            }
        }

        $vedicAstroPredictionData = Prediction::where([['zodiac', $zodiac], ['content_date', $prediction_date], ['show_same', $show_same], ['prediction_type', $prediction_type], ['lang', $lang], ['content_type', $content_type]])->first();

        $response = '';
        if (!empty($vedicAstroPredictionData)) {
            $response = json_decode($vedicAstroPredictionData->content);
        } else {
            $vedicAstro =  new vedicAstro();
            if ($prediction_type == 'Daily' || $prediction_type == 'Tomorrow') {
                $response = $vedicAstro->predictionDailyMoon($zodiac, $prediction_date, $show_same, $lang);
            } elseif ($prediction_type == 'Weekly') {
                $response = $vedicAstro->predictionWeeklyMoon($zodiac, $prediction_date, $show_same, $lang);
            } elseif ($prediction_type == 'Yearly') {
                $response = $vedicAstro->predictionYearlyMoon($zodiac, $prediction_date, $show_same, $lang);
            }

            if (!empty($response->status) && $response->status == 200) {
                $response = $response->response;

                Prediction::where([['zodiac', $zodiac], ['show_same', $show_same], ['prediction_type', $prediction_type], ['lang', $lang], ['content_type', $content_type]])->delete();

                $attributes['content'] = json_encode($response);
                $attributes['content_date'] = $prediction_date;
                $attributes['content_type'] = $content_type;
                unset($attributes['prediction_date']);

                Prediction::create($attributes);
            } else {
                $response = '';
            }
        }

        $zodiac_name = '';
        if ($zodiac == 1) {
            $zodiac_name = 'aries';
        } elseif ($zodiac == 2) {
            $zodiac_name = 'taurus';
        } elseif ($zodiac == 3) {
            $zodiac_name = 'gemini';
        } elseif ($zodiac == 4) {
            $zodiac_name = 'cancer';
        } elseif ($zodiac == 5) {
            $zodiac_name = 'leo';
        } elseif ($zodiac == 6) {
            $zodiac_name = 'virgo';
        } elseif ($zodiac == 7) {
            $zodiac_name = 'libra';
        } elseif ($zodiac == 8) {
            $zodiac_name = 'scorpio';
        } elseif ($zodiac == 9) {
            $zodiac_name = 'sagittarius';
        } elseif ($zodiac == 10) {
            $zodiac_name = 'capricorn';
        } elseif ($zodiac == 11) {
            $zodiac_name = 'aquarius';
        } elseif ($zodiac == 12) {
            $zodiac_name = 'pisces';
        }



        // dd($res);
        if (!empty($response)) {

            if ($is_app == "false") {
                $response = view('front_theme.' . env('THEME') . '.home.kundali.vedicastropredictionget', compact('response', 'prediction_type', 'zodiac_name'))->render();
            }

            $zodiac_name = ucwords($zodiac_name);

            $result = array(
                'status' => 1,
                'prediction_type' => $prediction_type,
                'zodiac' => $zodiac,
                'zodiac_name' => $zodiac_name,
                'data' => $response,
                'msg' => 'Prediction Fetched Successfully',
            );
        } else {
            $result = array(
                'status' => 0,
                'msg' => "Oops! Something went wrong please try again",
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }
    public function vedicAstroPanchangTest(Request $request)
    {
        $api = saveapiLogs($request->all());

        $user = (object) array(
            'name' => $request->name,
            'email' => $request->email,
        );


        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);
        $other['reset_link'] = URL::to('reset-password/' . $token);
        $result = MyCommand::SendNotification($user->user_uni_id, 'forget-password', 'forget-password', $other);


        $result = array(
            'status' => 1,
            'msg' => 'Mail send Successfully',
            'data' => $result,
        );
        return response()->json($result);
        die();

        if ($result) {
            $result = array(
                'status' => 1,
                'msg' => 'Mail send Successfully',
                'data' => $result,
            );
        } else {
            $result = array(
                'status' => 0,
                'msg' => 'Please check your Mail Credentials',
            );
        }

        updateapiLogs($api, $result);
        return response()->json($result);
    }


    public function getpayoutList(Request $request)
    {
        $api = saveapiLogs($request->all());
        $validator = Validator::make($request->all(), [
            'api_key' => ['required'],
            'user_uni_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }
        $attributes         =   $request->all();
        $api_key            =   $attributes['api_key'];
        $user_uni_id        =   $attributes['user_uni_id'];
        if (!checkUserApiKey($api_key, $user_uni_id)) {
            $result = array(
                'status' => 0,
                'error_code' => 101,
                'msg'     => 'Unauthorized User... Please login again',
            );
            return response()->json($result);
        }
        $res =  Wallet::where([['user_uni_id',$user_uni_id],['transaction_code','remove_wallet_by_payout']])->orderBy('created_at', 'DESC')->get();
        if (!empty($res)) {
            $result = array(
                'status'     => 1,
                'data'     => $res,
                'msg'         => "List",
            );
        } else {
            $result = array(
                'status'     => 0,
                'msg'         => "Something Went wrong.. Try Again",
            );
        }
        updateapiLogs($api, $result);
        return response()->json($result);
    }




    public function testFirebase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mydata' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "errors" => $validator->errors(),
                "message" => 'Something went wrong',
                "msg" => implode('\n', $validator->messages()->all()),
            ]);
        }

        $attributes =   $request->all();
        $mydata     =   $attributes['mydata'];

        $previousData = array('mydata' => $mydata);

        $saveData = [];
        $saveData['data'] = json_encode($previousData);
        Temp::create($saveData);

        $result = array(
            'status'    => 1,
            'msg'       => "Success",
        );
        
        return response()->json($result);
    }



}

