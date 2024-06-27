<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Mpesa\STKPush;
use App\Models\MpesaSTK;
use App\Models\PaymentMethod;
use Iankumu\Mpesa\Facades\Mpesa;
use Illuminate\Http\Request;

class MpesaSTKPUSHController extends Controller
{
    public $result_code = 1;
    public $result_desc = 'An Error Occured';

    public function MpesaPayment($id)
    {
        $invoice = Invoice::find($id);
        $MpesaCode = PaymentMethod::where('property_id', $invoice->property_id)
        ->whereRaw('LOWER(name) LIKE ?', ['%m%pesa%'])
        ->pluck('account_number')
        ->first();
     //   dd($shortcode);
        return View('admin.Lease.mpesapayment', compact('invoice'));
    }
    public function STKPush(Request $request)
    {
       // dd('reached');
        $amount = $request->input('amount');
        $phoneno = $request->input('phonenumber');
        $account_number =  $request->input('account_number');

        $response = Mpesa::stkpush($phoneno, $amount, $account_number);
        
        /** @var \Illuminate\Http\Client\Response $response */
        $result = $response->json(); 

      //  MpesaSTK::create([
        //    'merchant_request_id' =>  $result['MerchantRequestID'],
      //      'checkout_request_id' =>  $result['CheckoutRequestID']
      //  ]);

        return $result;
    }

    public function STKConfirm(Request $request)
    {
        $stk_push_confirm = (new STKPush())->confirm($request);

        if ($stk_push_confirm) {

            $this->result_code = 0;
            $this->result_desc = 'Success';
        }
        return response()->json([
            'ResultCode' => $this->result_code,
            'ResultDesc' => $this->result_desc
        ]);
    }
}
