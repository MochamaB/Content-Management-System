<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaSTKPUSHController;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


    //<!-------------------------------- MPESA Module ---------------------------------------------->////
  
      
        Route::post('/v1/mpesatest/stk/push', [MpesaSTKPUSHController::class, 'STKPush'])->name('mpesa.initiate');
        // Mpesa STK Push Callback Route
        Route::post('/v1/confirm', [MpesaSTKPUSHController::class, 'STKConfirm'])->name('mpesa.confirm');
        Route::post('check-payment-status/', [MpesaSTKPUSHController::class, 'checkPaymentStatus'])->name('mpesa.checkStatus');
        Route::post('confirm-payment/', [MpesaSTKPUSHController::class, 'paymentConfirm'])->name('mpesa.paymentconfirm');
        
       
    

