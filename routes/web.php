<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\WebsiteSettingController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitDetailsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\MeterReadingController;
use App\Http\Controllers\TenantDetailsController;
use App\Http\Controllers\UnitChargeController;
use App\Http\Controllers\utilityController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\PaymentController;

////Test Email View////////////
use App\Models\MeterReading;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Notifications\LeaseAgreementNotification;
use App\Notifications\UserCreatedNotification;
use App\Notifications\InvoiceGeneratedNotification;
use App\Notifications\PaymentNotification;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [App\Http\Controllers\client\HomeController::class, 'index']);



Route::group(['middleware' => ['auth', 'permission']], function () {

    Route::group(['groupName' => 'Website'], function () {
        Route::resource('amenity', AmenityController::class);
        Route::resource('propertytype', PropertyTypeController::class);
        Route::resource('websitesetting', WebsiteSettingController::class);
        Route::resource('slider', SliderController::class);
        Route::resource('testimonial', TestimonialController::class);
    });

    Route::group(['groupName' => 'Communication'], function () {
        Route::resource('notification', NotificationController::class);
    });

    Route::group(['groupName' => 'Accounting'], function () {
        Route::resource('chartofaccount', ChartOfAccountController::class);
        Route::resource('transaction', TransactionController::class);
        Route::resource('payment-type', PaymentTypeController::class);
    });

    Route::group(['groupName' => 'Leasing'], function () {
        Route::resource('lease', LeaseController::class);
        ///lease wizard///////
        Route::post('leasedetails', [LeaseController::class, 'leasedetails']);
        Route::post('cosigner', [LeaseController::class, 'cosigner']);
        Route::post('rent', [LeaseController::class, 'rent']);
        Route::post('deposit', [LeaseController::class, 'deposit']);
        Route::post('assignutilities', [LeaseController::class, 'assignUtilities']);
        Route::post('savelease', [LeaseController::class, 'saveLease']);
        Route::get('skiprent', [LeaseController::class, 'skiprent']);
        ///////////////
        Route::resource('unitcharge', UnitChargeController::class);
        Route::resource('utility', UtilityController::class);
        Route::get('meter-reading/create/{id}', [
            'as' => 'meter-reading.create',
            'uses' => 'App\Http\Controllers\MeterReadingController@create'
        ]);
        Route::resource('meter-reading', MeterReadingController::class, ['except' => 'create']);
        Route::resource('invoice', InvoiceController::class);
        Route::post('generateinvoice', [InvoiceController::class, 'generateInvoice']);
        Route::resource('paymentvoucher', PaymentVoucherController::class);
        Route::post('generatepaymentvoucher', [PaymentVoucherController::class, 'generatePaymentVoucher']);

        Route::get('payment/create/{id}', [
            'as' => 'payment.create',
            'uses' => 'App\Http\Controllers\PaymentController@create'
        ]);
        Route::resource('payment', PaymentController::class, ['except' => 'create']);
    });

    Route::group(['groupName' => 'Property'], function () {

        Route::resource('property', PropertyController::class);
        Route::post('update-amenities/{id}', [PropertyController::class, 'updateAmenities']);
        Route::resource('unit', UnitController::class);
        Route::resource('unitdetail', UnitDetailsController::class);
    });

    Route::group(['groupName' => 'Files'], function () {

        Route::resource('media', MediaController::class);
    });

    Route::group(['groupName' => 'Tasks'], function () {

        Route::resource('task', TaskController::class);
        Route::post('linkmonitor/{task}', [TaskController::class, 'linkmonitor']);
    });


    Route::group(['groupName' => 'Settings'], function () {


        Route::get('setting/create/{id}/{model}', [
            'as' => 'setting.create',
            'uses' => 'App\Http\Controllers\SettingController@create'
        ]);
        Route::get('setting/{model}', [
            'as' => 'setting.show',
            'uses' => 'App\Http\Controllers\SettingController@show'
        ]);

        Route::resource('setting', SettingController::class, ['except' => 'show', 'create']);
    });
    Route::group(['groupName' => 'User'], function () {

        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);
        Route::resource('user', UserController::class);
        Route::resource('tenantdetail', TenantDetailsController::class);

        //// user wizard
        Route::post('roleuser', [UserController::class, 'roleuser']);
        Route::post('userinfo', [UserController::class, 'userinfo']);
        Route::post('propertyaccess', [UserController::class, 'propertyaccess']);

        /////role wizard
        Route::post('assignpermission', [RoleController::class, 'assignpermission']);
        Route::post('assignreports', [RoleController::class, 'assignreports']);
    });

    Route::group(['groupName' => 'Other'], function () {
        Route::resource('dashboard', DashboardController::class);

        Route::get('cards', [DashboardController::class, 'cards']);
    });
});

Route::post('api/fetch-leaserent', [LeaseController::class, 'fetchleaserent']);
Route::post('api/fetch-units', [LeaseController::class, 'fetchunits']);
Route::post('api/check-chargename', [LeaseController::class, 'checkchargename']);
Route::post('api/fetch-meterReading', [MeterReadingController::class, 'fetchmeterReading']);

///Send Email
Route::get('/invoice/{invoice}/sendmail', [InvoiceController::class, 'sendInvoice']);

//////View Your email notification

Route::get('/notification', function () {
    $user = User::find(1);
    $tenant = User::find(1);
    $payment = Payment::find(1);


    return (new PaymentNotification($payment, $tenant))
        ->toMail($user->user);
});

require __DIR__ . '/auth.php';
