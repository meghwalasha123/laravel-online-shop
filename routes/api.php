<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
// use App\Http\Controllers\Backend\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/test',[ApiController::class,'test']);
Route::post('/welcome', [ApiController::class, 'welcome']);
Route::post('/customer-register', [ApiController::class, 'CustomerRegister']);
Route::post('/startVideoCall', [ApiController::class, 'startVideoCall']);
Route::post('/callWithLiveTest', [ApiController::class, 'callWithLiveTest']);

Route::post('/otpSend', [ApiController::class, 'otpSend']);
Route::Post('/customerLogin', [ApiController::class, 'customerLogin']);
Route::Post('/astrologerLogin', [ApiController::class, 'astrologerLogin']);
Route::Post('/getAllAstrologer', [ApiController::class, 'getAllAstrologer']);
Route::Post('/getAllAstrologers', [ApiController::class, 'getAllAstrologers']);
Route::Post('/getChatAstrologer', [ApiController::class, 'getChatAstrologer']);
Route::Post('/getLiveAstrologer', [ApiController::class, 'getLiveAstrologer']);
Route::Post('/getCallAstrologer', [ApiController::class, 'getCallAstrologer']);
Route::Post('/getAstrologerDetail', [ApiController::class, 'getAstrologerDetail']);
Route::Post('/saveAstrologerStep3', [ApiController::class, 'saveAstrologerStep3']);
Route::Post('/customerEdit', [ApiController::class, 'customerEdit']);
Route::Post('/astrologerEditProfile', [ApiController::class, 'astrologerEditProfile']);
Route::Post('/cityList', [ApiController::class, 'cityList']);
Route::Post('/stateList', [ApiController::class, 'stateList']);
Route::Post('/countryList', [ApiController::class, 'countryList']);
Route::Post('/languageList', [ApiController::class, 'languageList']);
Route::Post('/giftItem', [ApiController::class, 'giftItem']);
Route::Post('/banner', [ApiController::class, 'banner']);
Route::Post('/addBlog', [ApiController::class, 'addBlog']);
Route::Post('/getBlog', [ApiController::class, 'getBlog']);
Route::Post('/getBlogs', [ApiController::class, 'getBlogs']);
Route::Post('/blogLike', [ApiController::class, 'blogLike']);
Route::Post('/getMyBlog', [ApiController::class, 'getMyBlog']);
Route::Post('/deleteBlog', [ApiController::class, 'deleteBlog']);
Route::Post('/notificationList', [ApiController::class, 'notificationList']);
Route::Post('/getNotification', [ApiController::class, 'getNotification']);
Route::Post('/astrologerFollow', [ApiController::class, 'astrologerFollow']);
Route::Post('/astrologerUnfollow', [ApiController::class, 'astrologerUnfollow']);
Route::Post('/rechargeVoucher', [ApiController::class, 'rechargeVoucher']);
Route::Post('/addReviews', [ApiController::class, 'addReviews']);
Route::Post('/getReviews', [ApiController::class, 'getReviews']);
Route::Post('/saveReport', [ApiController::class, 'saveReport']);
Route::Post('/proceedPaymentRequest', [ApiController::class, 'proceedPaymentRequest']);
Route::Post('/page', [ApiController::class, 'page']);
Route::Post('/startChat', [ApiController::class, 'startChat']);
Route::Post('/reciveChat', [ApiController::class, 'reciveChat']);
Route::Post('/endChat', [ApiController::class, 'endChat']);
Route::Post('/getWalletBalance', [ApiController::class, 'getWalletBalance']);
Route::Post('/astrologerOnlineStatus', [ApiController::class, 'astrologerOnlineStatus']);
Route::Post('/updateNextOnlineTime', [ApiController::class, 'updateNextOnlineTime']);
Route::Post('/updateOnlineStatus', [ApiController::class, 'updateOnlineStatus']);
Route::Post('/updateVideoCallStatus', [ApiController::class, 'updateVideoCallStatus']);
Route::Post('/updateCallStatus', [ApiController::class, 'updateCallStatus']);
Route::Post('/updateChatStatus', [ApiController::class, 'updateChatStatus']);
Route::Post('/startVoiceCallExotel', [ApiController::class, 'startVoiceCallExotel']);
Route::Post('/directCall', [ApiController::class, 'DirectCall']);
Route::Post('/serviceCall', [ApiController::class, 'ServiceCall']);
Route::Post('/updatePayment', [ApiController::class, 'updatePayment']);
Route::Post('/reciveVideoCall', [ApiController::class, 'reciveVideoCall']);
Route::Post('/endVideoCall', [ApiController::class, 'endVideoCall']);
Route::Post('/startVoiceCall', [ApiController::class, 'startVoiceCall']);
Route::Post('/startVoiceCallWithLive', [ApiController::class, 'startVoiceCallWithLive']);
Route::Post('/endVoiceCallWithLive', [ApiController::class, 'endVoiceCallWithLive']);
Route::Post('/reciveVoiceCallWithLive', [ApiController::class, 'reciveVoiceCallWithLive']);
Route::Post('/startLiveStream', [ApiController::class, 'startLiveStream']);
Route::Post('/joinLiveStream', [ApiController::class, 'joinLiveStream']);

Route::Post('/joinLiveStreamWeb', [ApiController::class, 'joinLiveStreamWeb']);

Route::Post('/startLiveStreamAgora', [ApiController::class, 'startLiveStreamAgora']);
Route::Post('/connectLiveStream', [ApiController::class, 'connectLiveStream']);
Route::Post('/disconnectLiveStream', [ApiController::class, 'disconnectLiveStream']);
Route::Post('/userCallHistory', [ApiController::class, 'userCallHistory']);
Route::Post('/getWalletHistory', [ApiController::class, 'getWalletHistory']);
Route::Post('/getRechargeHistory', [ApiController::class, 'getRechargeHistory']);
Route::Post('/getChatRequest', [ApiController::class, 'getChatRequest']);
Route::Post('/declineChatRequest', [ApiController::class, 'declineChatRequest']);
Route::Post('/sendGiftAstro', [ApiController::class, 'sendGiftAstro']);
Route::Post('/productCategory', [ApiController::class, 'productCategory']);
Route::Post('/products', [ApiController::class, 'products']);
Route::Post('/userAddressList', [ApiController::class, 'userAddressList']);
Route::Post('/productOrderList', [ApiController::class, 'productOrderList']);
Route::Post('/categoryList', [ApiController::class, 'categoryList']);
Route::Post('/getNotice', [ApiController::class, 'getNotice']);
Route::Post('/checkCallDetail', [ApiController::class, 'checkCallDetail']);
Route::Post('/getChatChannels', [ApiController::class, 'getChatChannels']);
Route::Post('/getChatChannelHistory', [ApiController::class, 'getChatChannelHistory']);
Route::Post('/getUserProfileData', [ApiController::class, 'getUserProfileData']);
Route::Post('/saveAstrologerProfileData', [ApiController::class, 'saveAstrologerProfileData']);
Route::Post('/statusCallback', [ApiController::class, 'statusCallback']);
Route::Post('/saveAstrologerStep1', [ApiController::class, 'saveAstrologerStep1']);
Route::Post('/saveAstrologerStep2', [ApiController::class, 'saveAstrologerStep2']);
Route::Post('/saveAstrologerStep4', [ApiController::class, 'saveAstrologerStep4']);
Route::Post('/orderStatusProcess', [ApiController::class, 'orderStatusProcess']);
Route::Post('/orderReturnProcess', [ApiController::class, 'orderReturnProcess']);
Route::Post('/refundRequest', [ApiController::class, 'refundRequest']);
Route::Post('/skillList', [ApiController::class, 'skillList']);
Route::Post('/updateOnlinePayment', [ApiController::class, 'updateOnlinePayment']);
Route::Post('/astroCallHistory', [ApiController::class, 'astroCallHistory']);
Route::Post('/userGiftHistory', [ApiController::class, 'userGiftHistory']);
Route::Post('/astroGiftHistory', [ApiController::class, 'astroGiftHistory']);
Route::Post('/getAstroDashbord', [ApiController::class, 'getAstroDashbord']);
Route::Post('/saveChat', [ApiController::class, 'saveChat']);
Route::Post('/addAddress', [ApiController::class, 'addAddress']);
Route::Post('/productCalculation', [ApiController::class, 'productCalculation']);
Route::Post('/productPurchase', [ApiController::class, 'productPurchase']);
Route::Post('/serviceCategory', [ApiController::class, 'serviceCategory']);
Route::Post('/services', [ApiController::class, 'services']);
Route::Post('/upcomingLiveAstrologer', [ApiController::class, 'upcomingLiveAstrologer']);
Route::Post('/serviceAstrologerList', [ApiController::class, 'serviceAstrologerList']);
Route::Post('/serviceCalculation', [ApiController::class, 'serviceCalculation']);
Route::Post('/servicePurchase', [ApiController::class, 'servicePurchase']);
Route::Post('/serviceList', [ApiController::class, 'serviceList']);
Route::Post('/assignService', [ApiController::class, 'assignService']);
Route::Post('/removeService', [ApiController::class, 'removeService']);
Route::Post('/serviceActive', [ApiController::class, 'serviceActive']);
Route::Post('/saveAstrologerStep5', [ApiController::class, 'saveAstrologerStep5']);
Route::Post('/customerDashbord', [ApiController::class, 'customerDashbord']);
Route::Post('/kundaliCalulation', [ApiController::class, 'kundaliCalulation']);
Route::Post('/kundaliPurchase', [ApiController::class, 'kundaliPurchase']);
Route::Post('/kundaliOrderList', [ApiController::class, 'kundaliOrderList']);
Route::Post('/sendMailKundali', [ApiController::class, 'sendMailKundali']);
Route::Post('/editAddress', [ApiController::class, 'editAddress']);
Route::Post('/deleteAddress', [ApiController::class, 'deleteAddress']);
Route::Post('/testimonials', [ApiController::class, 'testimonials']);
Route::Post('/prokeralaKundli', [ApiController::class, 'prokeralaKundli']);
Route::Post('/prokeralaChart', [ApiController::class, 'prokeralaChart']);
Route::Post('/customerServiceOrder', [ApiController::class, 'customerServiceOrder']);
Route::Post('/customerServiceOrders', [ApiController::class, 'customerServiceOrders']);
Route::Post('/astrologerServiceOrder', [ApiController::class, 'astrologerServiceOrder']);
Route::Post('/astrologerServiceOrders', [ApiController::class, 'astrologerServiceOrders']);
Route::Post('/prokeralaPanchang', [ApiController::class, 'prokeralaPanchang']);
Route::Post('/prokeralaDailyHoroscope', [ApiController::class, 'prokeralaDailyHoroscope']);
Route::Post('/prokeralaKundaliMatching', [ApiController::class, 'prokeralaKundaliMatching']);
Route::Post('/faqcategory', [ApiController::class, 'faqcategory']);
Route::Post('/faqs', [ApiController::class, 'faqs']);

Route::post('/userkundaliRequest', [ApiController::class, 'userkundaliRequest']);
Route::post('/suggestionsRequest', [ApiController::class, 'suggestionsRequest']);
Route::post('/withdrawalRequest', [ApiController::class, 'withdrawalRequest']);
Route::post('/unregisteredUser', [ApiController::class, 'unregisteredUser']);
Route::post('/videoSections', [ApiController::class, 'videoSections']);
Route::post('/addWithdrawalRequest', [ApiController::class, 'addWithdrawalRequest']);
Route::post('/getWithdrawalRequest', [ApiController::class, 'getWithdrawalRequest']);

Route::post('/updateStatusOnline', [ApiController::class, 'updateStatusOnline']);
Route::post('/getCustomerQueueList', [ApiController::class, 'getCustomerQueueList']);
Route::post('/getAstrologerQueueList', [ApiController::class, 'getAstrologerQueueList']);
Route::post('/astrologerQueueRefresh', [ApiController::class, 'astrologerQueueRefresh']);
Route::post('/getFollowing', [ApiController::class, 'getFollowing']);
Route::post('/getFollowers', [ApiController::class, 'getFollowers']);
Route::post('/joinLiveCall', [ApiController::class, 'joinLiveCall']);
Route::post('/acceptLiveCall', [ApiController::class, 'acceptLiveCall']);

Route::Post('/vedicAstroPanchangTest', [ApiController::class, 'vedicAstroPanchangTest']);
Route::Post('/vedicAstroKundli', [ApiController::class, 'vedicAstroKundli']);
Route::Post('/saveKundliData', [ApiController::class, 'saveKundliData']);
Route::Post('/vedicAstroKundliMatching', [ApiController::class, 'vedicAstroKundliMatching']);
Route::Post('/vedicAstroPrediction', [ApiController::class, 'vedicAstroPrediction']);
Route::Post('/refundRequest', [ApiController::class, 'refundRequest']);
Route::Post('/getpayoutList', [ApiController::class, 'getpayoutList']);

Route::any('/paymentresponseccavenueapp', [ApiController::class, 'paymentResponseCCAvenueApp'])->name('paymentresponseccavenueapp');
Route::any('/paymentresponseccavenuewebhook', [ApiController::class, 'paymentResponseCCAvenueWebhook'])->name('paymentresponseccavenuewebhook');
Route::post('/paymentresponsephonepeapp', [ApiController::class, 'paymentResponsePhonePeApp'])->name('paymentresponsephonepeapp');
Route::post('/testFirebase', [ApiController::class, 'testFirebase'])->name('testFirebase');

// Callback And Crons
Route::Post('/endCallSendBird', [ApiController::class, 'endCallSendBird']);
Route::get('/cronRazorpayPayment', [ApiController::class, 'cronRazorpayPayment']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
