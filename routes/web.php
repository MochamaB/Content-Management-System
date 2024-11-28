<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\WebsiteController;
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
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\IncomeStatementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\MeterReadingController;
use App\Http\Controllers\TenantDetailsController;
use App\Http\Controllers\UnitChargeController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\MpesaSTKPUSHController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsCreditController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TextMessageController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\VendorCategoryController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WorkOrderController;

////Test Email View////////////
use App\Models\MeterReading;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Unitcharge;
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


Route::group(['middleware' => ['auth', 'permission','verified']], function () {
    
    Route::resource('dashboard', DashboardController::class);

//<!-------------------------------- Website Module ---------------------------------------------->////
    Route::group(['groupName' => 'Website'], function () {
        Route::resource('amenity', AmenityController::class);
        Route::resource('propertytype', PropertyTypeController::class);
        Route::resource('Website', WebsiteController::class);
        Route::resource('slider', SliderController::class);
        Route::resource('testimonial', TestimonialController::class);
    });

//<!-------------------------------- Communication Module ---------------------------------------------->////
    Route::group(['groupName' => 'Communication'], function () {
      
        Route::post('notification/text/sendText', [NotificationController::class, 'sendText']);
        Route::resource('notification', NotificationController::class);
        Route::resource('email', EmailController::class);
        Route::resource('textmessage', TextMessageController::class);
        Route::post('textmessage/check-credits', [TextMessageController::class, 'checkCredits']);
        Route::resource('smsCredit', SmsCreditController::class);
        Route::post('/notification/mark-as-read/{id}', function($id) {
            $notification = auth()->user()->notifications()->where('id', $id)->first();
            
            
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['status' => 'success']);
            }
        
            return response()->json(['status' => 'error', 'message' => 'Notification not found.'], 404);
        });
        
       
    });

//<!-------------------------------- Accounting Module ---------------------------------------------->////
    Route::group(['groupName' => 'Accounting'], function () {
        Route::resource('chartofaccount', ChartOfAccountController::class);

        Route::get('payment-method/create/{id?}/{model?}', [
            'as' => 'payment-method.create',
            'uses' => 'App\Http\Controllers\PaymentMethodController@create'
        ]);
        Route::resource('tax', TaxController::class);
        Route::resource('payment-method', PaymentMethodController::class);
        Route::resource('transaction', TransactionController::class);
        Route::resource('general-ledger', GeneralLedgerController::class);
        Route::resource('income-statement', IncomeStatementController::class);
        Route::get('expense/create/{id?}/{model?}', [
            'as' => 'expense.create',
            'uses' => 'App\Http\Controllers\ExpenseController@create'
        ])->middleware('check.create.variables');
        Route::resource('expense', ExpenseController::class, ['except' => 'create']);
        Route::resource('transaction-type', TransactionTypeController::class);

    });

//<!-------------------------------- Leasing Module ---------------------------------------------->////
    Route::group(['groupName' => 'Leasing'], function () {
        Route::resource('lease', LeaseController::class);
        ///lease wizard///////
        Route::post('leasedetails', [LeaseController::class, 'leasedetails']);
        Route::post('cosigner', [LeaseController::class, 'cosigner']);
        Route::post('rent', [LeaseController::class, 'rent']);
        Route::post('securitydeposit', [LeaseController::class, 'securitydeposit']);
        Route::post('assignutilities', [LeaseController::class, 'assignUtilities']);
        Route::post('savelease', [LeaseController::class, 'saveLease']);
        Route::get('skiprent', [LeaseController::class, 'skiprent']);
        Route::get('skipdeposit', [LeaseController::class, 'skipdeposit']);

        ///////////////
        Route::get('unitcharge/create/{id?}/{model?}', [
            'as' => 'unitcharge.create',
            'uses' => 'App\Http\Controllers\UnitChargeController@create'
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
       
        Route::get('deposit/create/{id?}/{model?}', [
            'as' => 'deposit.create',
            'uses' => 'App\Http\Controllers\DepositController@create'
        ])->middleware('check.create.variables');
        Route::resource('deposit', DepositController::class, ['except' => 'create']);
        Route::post('generateDeposit', [DepositController::class, 'generateDeposit']);
        ////////////////////////
        Route::get('payment/create/{id?}/{model?}', [
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

        Route::get('ticket/create/{id?}/{model?}', [
            'as' => 'ticket.create',
            'uses' => 'App\Http\Controllers\TicketController@create'
        ])->middleware('check.create.variables');
        Route::resource('ticket', TicketController::class, ['except' => 'create']);
        Route::get('ticket/assign/{id}', [TicketController::class, 'assign'])->name('ticket.assign');
        Route::put('update-assign/{id}', [TicketController::class, 'updateassign']);
        // In routes/web.php
       

        Route::get('work-order/create/{id}', [
            'as' => 'work-order.create',
            'uses' => 'App\Http\Controllers\WorkOrderController@create'
        ]);
        Route::resource('work-order', WorkOrderController::class, ['except' => 'create']);
        Route::get('workorder-expense/create/{id}', [WorkOrderController::class, 'expense'])->name('work-order.expense');
        Route::post('workorder-expense', [WorkOrderController::class, 'postexpense']);
        


    });



    //<!-------------------------------- Tasks Module ---------------------------------------------->////
    Route::group(['groupName' => 'Tasks'], function () {

        Route::resource('task', TaskController::class);
        Route::post('linkmonitor/{task}', [TaskController::class, 'linkmonitor']);
    });

    //<!-------------------------------- Settings Module ---------------------------------------------->////
    Route::group(['groupName' => 'Settings'], function () {


        Route::get('setting/create/{model_type?}', [
            'as' => 'setting.create',
            'uses' => 'App\Http\Controllers\SettingController@create'
        ]);
        Route::get('setting/{model_type}', [
            'as' => 'setting.show',
            'uses' => 'App\Http\Controllers\SettingController@show'
        ]);

        Route::resource('setting', SettingController::class, ['except' => ['create', 'show']]);
        Route::get('system', [SettingController::class, 'systemsetting'])->name('setting.system');
        Route::post('update-systemsetting}', [SettingController::class, 'updateSystemSettings']);

        Route::put('system-setting/update', [
            'as' => 'system-setting.update',
            'uses' => 'App\Http\Controllers\SystemSettingController@update'
        ]);
        Route::resource('system-setting', SystemSettingController::class);
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
        Route::put('updateAssignedUnits/{id}', [UserController::class, 'updateAssignedUnits']);
        Route::put('updateRole/{id}', [UserController::class, 'updateRole']);
       

        /////role wizard
        Route::post('assignpermission', [RoleController::class, 'assignpermission']);
        Route::post('assignreports', [RoleController::class, 'assignreports']);

        ////Tenant
        Route::resource('tenant', TenantController::class);
    });

     //<!-------------------------------- Report Module ---------------------------------------------->////
    Route::group(['groupName' => 'Reports'], function () {
        Route::resource('report', ReportController::class);

    });

    Route::get('mpesa-payment/{id}', [MpesaSTKPUSHController::class, 'MpesaPayment'])->name('mpesa.view');

     //<!-------------------------------- Other Module ---------------------------------------------->////

    Route::group(['groupName' => 'Other'], function () {
        

        
    });
});

Route::post('api/fetch-leaserent', [LeaseController::class, 'fetchleaserent']);
Route::post('api/fetch-units', [LeaseController::class, 'fetchunits']);

Route::post('api/check-chargename', [LeaseController::class, 'checkchargename']);
Route::post('api/fetch-meterReading', [MeterReadingController::class, 'fetchmeterReading']);
Route::post('api/fetch-propertyMeterReading', [MeterReadingController::class, 'fetchpropertyMeterReading']);
Route::post('api/fetch-allunits', [MeterReadingController::class, 'fetchAllUnits']);
Route::post('api/fetch-charge', [UnitChargeController::class, 'fetchCharge']);
Route::post('api/fetch-setting', [SettingController::class, 'fetchSetting']);
Route::post('closewizard/{routePart}', [SettingController::class, 'closewizard'])->name('closewizard');

Route::get('mpesareceipt/{payment}', [MpesaSTKPUSHController::class, 'Receipt'])->name('mpesa.receipt');



///Send Email
Route::get('/invoice/{invoice}/sendmail', [InvoiceController::class, 'sendInvoice']);
Route::get('/payment/{payment}/sendmail', [PaymentController::class, 'sendPayment']);
//Route::get('notification', [NotificationController::class, 'index']);
Route::get('/invoicemail', [InvoiceController::class, 'invoicemail']);
//////View Your email notification

Route::get('/notificationview', function () {
    $user = User::find(1);
   $tenant = User::find(1);
   $payment = Payment::find(1);


  // Render the email template
  $viewContent = view('email.payment', compact('payment'))->render();

  return $viewContent;
});

Route::get('/invoiceview', function () {
    $user = User::find(1);
   $tenant = User::find(1);
   $payment = Payment::find(2);
   $invoice = Invoice::find(4);



   return View('email.invoice',compact('invoice'));
});

Route::get('/statementview', function () {
    $user = User::find(1);
   $tenant = User::find(1);
   $payment = Payment::find(2);
   $invoice = Invoice::find(1);
    $lease = Lease::find(1);
    $unitcharge =Unitcharge::find(1);
    $sixMonths = now()->subMonths(6);
    $transactions = Transaction::where('created_at', '>=', $sixMonths)
            ->where('unit_id', $invoice->unit_id)
            ->where('unitcharge_id', $unitcharge->id)
            ->get();
        $groupedInvoiceItems = $transactions->groupBy('unitcharge_id');
        $openingBalance = 0;
          //// Data for the Payment Methods
        $PaymentMethod = PaymentMethod::all();
        $viewContent = view('email.statement', [
            'user' => $user,
            'invoice' => $invoice,
            'transactions' => $transactions,
            'groupedInvoiceItems' => $groupedInvoiceItems,
            'openingBalance' => $openingBalance,
            'PaymentMethod' => $PaymentMethod,
        ])->render();



        return $viewContent;
});
Route::get('/testview', function () {



   return View('Admin.Accounting.test');
});

require __DIR__ . '/auth.php';
