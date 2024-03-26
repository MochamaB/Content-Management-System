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
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\VendorCategoryController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WorkOrderController;
////Test Email View////////////
use App\Models\MeterReading;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\VendorCategory;
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

//<!-------------------------------- Website Module ---------------------------------------------->////
    Route::group(['groupName' => 'Website'], function () {
        Route::resource('amenity', AmenityController::class);
        Route::resource('propertytype', PropertyTypeController::class);
        Route::resource('websitesetting', WebsiteSettingController::class);
        Route::resource('slider', SliderController::class);
        Route::resource('testimonial', TestimonialController::class);
    });

//<!-------------------------------- Communication Module ---------------------------------------------->////
    Route::group(['groupName' => 'Communication'], function () {
        //  Route::resource('notification', NotificationController::class);
        Route::get('/notification', [NotificationController::class, 'index'])->name('notification.index');
        Route::get('/email', [NotificationController::class, 'email'])->name('notification.email');
        Route::get('/text', [NotificationController::class, 'text'])->name('notification.texts');
        Route::get('/email/{id}', [NotificationController::class, 'show'])
            ->where('id', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}')
            ->name('notification.show');
    });

//<!-------------------------------- Accounting Module ---------------------------------------------->////
    Route::group(['groupName' => 'Accounting'], function () {
        Route::resource('chartofaccount', ChartOfAccountController::class);
        Route::resource('payment-method', PaymentMethodController::class);
     //   Route::resource('transaction', TransactionController::class);
        Route::get('general-ledger', [TransactionController::class, 'ledger'])->name('transaction.ledger');
        Route::get('income-statement', [TransactionController::class, 'incomeStatement'])->name('transaction.incomestatement');
    
    });

//<!-------------------------------- Leasing Module ---------------------------------------------->////
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
        Route::get('skipdeposit', [LeaseController::class, 'skipdeposit']);

        ///////////////
        Route::get('unitcharge/create/{id?}', [
            'as' => 'unitcharge.create',
            'uses' => 'App\Http\Controllers\UnitchargeController@create'
        ])->middleware('check.create.variables');
        Route::resource('unitcharge', UnitChargeController::class, ['except' => 'create']);
        /////////////////

        Route::resource('utility', UtilityController::class);
        ///////////////////////
        Route::get('meter-reading/create/{id?}/{model?}', [
            'as' => 'meter-reading.create',
            'uses' => 'App\Http\Controllers\MeterReadingController@create'
        ])->middleware('check.create.variables');
        Route::resource('meter-reading', MeterReadingController::class, ['except' => 'create']);
        ////////////////////////////
        Route::resource('invoice', InvoiceController::class);
        Route::post('generateinvoice', [InvoiceController::class, 'generateInvoice']);
        ///////////////////
       
        Route::resource('paymentvoucher', PaymentVoucherController::class);
        Route::post('generatepaymentvoucher', [PaymentVoucherController::class, 'generatePaymentVoucher']);
        ////////////////////////
        Route::get('payment/create/{id}', [
            'as' => 'payment.create',
            'uses' => 'App\Http\Controllers\PaymentController@create'
        ]);
        Route::resource('payment', PaymentController::class, ['except' => 'create']);
    });

//<!-------------------------------- Property Module ---------------------------------------------->////
    Route::group(['groupName' => 'Property'], function () {

        Route::resource('property', PropertyController::class);
        Route::post('update-amenities/{id}', [PropertyController::class, 'updateAmenities']);
        Route::resource('unit', UnitController::class);
        Route::resource('unitdetail', UnitDetailsController::class);
    });

//<!-------------------------------- Files Module ---------------------------------------------->////
    Route::group(['groupName' => 'Files'], function () {
        Route::get('media/create/{model?}', [
            'as' => 'media.create',
            'uses' => 'App\Http\Controllers\MediaController@create'
        ]);
        Route::resource('media', MediaController::class, ['except' => 'create']);
    });

//<!-------------------------------- Maintenance Module ---------------------------------------------->////
    Route::group(['groupName' => 'Maintenance'], function () {

        Route::resource('vendor-category', VendorCategoryController::class);
        Route::resource('vendors', VendorController::class);

        Route::get('ticket/create/{id?}', [
            'as' => 'ticket.create',
            'uses' => 'App\Http\Controllers\TicketController@create'
        ]);
        Route::resource('ticket', TicketController::class, ['except' => 'create']);
        Route::get('ticket/assign/{id}', [TicketController::class, 'assign'])->name('ticket.assign');
        Route::put('update-assign/{id}', [TicketController::class, 'updateassign']);

        Route::get('work-order/create/{id}', [
            'as' => 'work-order.create',
            'uses' => 'App\Http\Controllers\WorkOrderController@create'
        ]);
        Route::resource('work-order', WorkOrderController::class, ['except' => 'create']);
        Route::get('workorder-expense/create/{id}', [WorkOrderController::class, 'expense'])->name('workorder.expense');
        Route::post('workorder-expense', [WorkOrderController::class, 'postexpense']);
        


    });



    //<!-------------------------------- Tasks Module ---------------------------------------------->////
    Route::group(['groupName' => 'Tasks'], function () {

        Route::resource('task', TaskController::class);
        Route::post('linkmonitor/{task}', [TaskController::class, 'linkmonitor']);
    });

    //<!-------------------------------- Settings Module ---------------------------------------------->////
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

     //<!-------------------------------- User Module ---------------------------------------------->////
    Route::group(['groupName' => 'User'], function () {

        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);
        Route::resource('user', UserController::class);
        Route::resource('tenantdetail', TenantDetailsController::class);

        //// user wizard
        Route::post('roleuser', [UserController::class, 'roleuser']);
        Route::post('userinfo', [UserController::class, 'userinfo']);
        Route::post('assignProperties', [UserController::class, 'assignProperties']);

        /////role wizard
        Route::post('assignpermission', [RoleController::class, 'assignpermission']);
        Route::post('assignreports', [RoleController::class, 'assignreports']);

        ////Tenant
        Route::resource('tenant', TenantController::class);
    });

     //<!-------------------------------- Report Module ---------------------------------------------->////
    Route::group(['groupName' => 'Report'], function () {
        Route::resource('report', ReportController::class);

    });

     //<!-------------------------------- Other Module ---------------------------------------------->////

    Route::group(['groupName' => 'Other'], function () {
        Route::resource('dashboard', DashboardController::class);

        Route::get('cards', [DashboardController::class, 'cards']);
    });
});

Route::post('api/fetch-leaserent', [LeaseController::class, 'fetchleaserent']);
Route::post('api/fetch-units', [LeaseController::class, 'fetchunits']);

Route::post('api/check-chargename', [LeaseController::class, 'checkchargename']);
Route::post('api/fetch-meterReading', [MeterReadingController::class, 'fetchmeterReading']);
Route::post('api/fetch-propertyMeterReading', [MeterReadingController::class, 'fetchpropertyMeterReading']);
Route::post('api/fetch-allunits', [MeterReadingController::class, 'fetchAllUnits']);


///Send Email
Route::get('/invoice/{invoice}/sendmail', [InvoiceController::class, 'sendInvoice']);
Route::get('notification', [NotificationController::class, 'index']);

//////View Your email notification

//Route::get('/notification', function () {
//    $user = User::find(1);
//   $tenant = User::find(1);
//   $payment = Payment::find(1);


//    return (new PaymentNotification($payment, $tenant))
//       ->toMail($user->user);
//});

require __DIR__ . '/auth.php';
