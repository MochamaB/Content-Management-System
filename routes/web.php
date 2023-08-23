<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\SettingSiteController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;


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


Route::get('/', [ App\Http\Controllers\client\HomeController::class, 'index'])->name('Home.index');







Route::group(['middleware' => ['auth','permission']], function () {

    Route::group(['groupName' => 'Property'], function () {
      
        Route::resource('property',PropertyController::class);
        Route::post('update-amenities/{id}', [PropertyController::class, 'updateAmenities'])->name('property.update_amenities');
        Route::resource('unit',UnitController::class);
    });
    Route::group(['groupName' => 'Settings'], function () {
        Route::resource('amenity',AmenityController::class);
        Route::resource('setting',SettingController::class);
        Route::resource('settingsite',SettingSiteController::class);
        Route::resource('slider',SliderController::class);
        Route::resource('testimonial',TestimonialController::class);
        
    });
    Route::group(['groupName' => 'User'], function () {

        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);
        Route::resource('user',UserController::class);
        
    });

    Route::group(['groupName' => 'Other'], function () {
        Route::resource('dashboard',DashboardController::class);
        
    });
    
    
  
   
   
   
    
  
   


});  

require __DIR__.'/auth.php';
