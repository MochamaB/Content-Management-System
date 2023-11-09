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
use App\Models\MeterReading;

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


Route::get('/', [ App\Http\Controllers\client\HomeController::class, 'index']);







Route::group(['middleware' => ['auth','permission']], function () {

    Route::group(['groupName' => 'Communication'], function () {
        Route::resource('notification', NotificationController::class); 
    });

    Route::group(['groupName' => 'Accounting'], function () {
        Route::resource('chartofaccount', ChartOfAccountController::class); 
    });

    Route::group(['groupName' => 'Leasing'], function () {
        Route::resource('lease', LeaseController::class);
        ///lease wizard///////
        Route::post('cosigner', [LeaseController::class, 'cosigner']); 
        Route::post('rent', [LeaseController::class, 'rent']); 
        Route::post('deposit', [LeaseController::class, 'deposit']); 
        Route::post('assignutilities', [LeaseController::class, 'assignUtilities']); 
        Route::post('savelease', [LeaseController::class, 'saveLease']); 
        Route::get('skiprent', [LeaseController::class, 'skiprent']);
        ///////////////
        Route::resource('unitcharge', UnitChargeController::class); 
        Route::resource('utility', utilityController::class);
        Route::get('meter-reading/create/{id}', ['as' => 'meter-reading.create',
            'uses' => 'App\Http\Controllers\MeterReadingController@create'
        ]);
        Route::resource('meter-reading', MeterReadingController::class, ['except' => 'create']); 
        Route::resource('invoice', InvoiceController::class);
        Route::post('generateinvoice', [InvoiceController::class, 'generateInvoice']); 
        
    });

    Route::group(['groupName' => 'Property'], function () {
      
        Route::resource('property',PropertyController::class);
        Route::post('update-amenities/{id}', [PropertyController::class, 'updateAmenities']);
        Route::resource('unit',UnitController::class);
        Route::resource('unitdetail',UnitDetailsController::class);
    });
    Route::group(['groupName' => 'Settings'], function () {
        Route::resource('propertytype',PropertyTypeController::class);
        Route::resource('amenity',AmenityController::class);
        Route::resource('setting',SettingController::class, ['except' => 'show','create']); 
        Route::get('setting/{model}', ['as' => 'setting.show',
            'uses' => 'App\Http\Controllers\SettingController@show'
        ]);
        Route::get('setting/create/{model}', ['as' => 'setting.create',
            'uses' => 'App\Http\Controllers\SettingController@create'
        ]);
        Route::resource('websitesetting',WebsiteSettingController::class);
        Route::resource('slider',SliderController::class);
        Route::resource('testimonial',TestimonialController::class);
        
    });
    Route::group(['groupName' => 'User'], function () {

        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);
        Route::resource('user',UserController::class);
        Route::resource('tenantdetail',TenantDetailsController::class);

        //// user wizard
        Route::post('roleuser', [UserController::class, 'roleuser']); 
        Route::post('userinfo', [UserController::class, 'userinfo']); 
        Route::post('propertyaccess', [UserController::class, 'propertyaccess']); 
        
        /////role wizard
        Route::post('assignpermission', [RoleController::class, 'assignpermission']); 
        Route::post('assignreports', [RoleController::class, 'assignreports']); 

    });

    Route::group(['groupName' => 'Other'], function () {
        Route::resource('dashboard',DashboardController::class);
        
    });
    

});  

Route::post('api/fetch-leaserent', [LeaseController::class, 'fetchleaserent']);
Route::post('api/fetch-units', [LeaseController::class, 'fetchunits']);
Route::post('api/check-chargename', [LeaseController::class, 'checkchargename']);
Route::post('api/fetch-meterReading', [MeterReadingController::class, 'fetchmeterReading']);


require __DIR__.'/auth.php';
