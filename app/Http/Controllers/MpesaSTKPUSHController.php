<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Mpesa\STKPush;
use App\Models\MpesaSTK;
use App\Models\PaymentMethod;
use Iankumu\Mpesa\Facades\Mpesa;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'phonenumber' => 'required|string|min:10',
        ], [
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be more than zero.',
            'phonenumber.required' => 'Phone number is required.',
            'phonenumber.string' => 'Phone number must be a string.',
            'phonenumber.min' => 'Phone number must be at least 10 characters.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('statuserror', 'Check Validation Errors');
        }
        $amount = $request->input('amount');
        $phoneno = $request->input('phonenumber');
        // Check if the phone number starts with 0 and replace it with 254
        if (strpos($phoneno, '0') === 0) {
            $phoneno = '254' . substr($phoneno, 1);
        }
        $account_number = $request->input('account_number');

        $client = new Client([
            'verify' => false // This disables SSL verification
        ]);
        try {
            $timestamp = $this->getTimestamp();
            $password = $this->generatePassword($timestamp);

            $response = $client->post(
                Config::get('mpesa.environment') === 'sandbox'
                    ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
                    : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
                [
                    'json' => [
                        'BusinessShortCode' => Config::get('mpesa.shortcode'),
                        'Password' => $password,
                        'Timestamp' => $timestamp,
                        'TransactionType' => 'CustomerPayBillOnline',
                        'Amount' => $amount,
                        'PartyA' => 254708374149,
                        'PartyB' => Config::get('mpesa.shortcode'),
                        'PhoneNumber' => $phoneno,
                        'CallBackURL' => Config::get('mpesa.callback_url'),
                        'AccountReference' => $account_number,
                        'TransactionDesc' => 'Test STK Push'
                    ],
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->generateAccessToken(),
                        'Accept' => 'application/json',
                    ]
                ]
            );

            $result = json_decode($response->getBody(), true);
            // Save initial transaction details
            $mpesaTransaction = MpesaSTK::create([
                'merchant_request_id' => $result['MerchantRequestID'],
                'checkout_request_id' => $result['CheckoutRequestID'],
                'referenceno' =>  $account_number, // Make sure to pass this from the frontend
                'amount' => $amount,
                'phonenumber' => $phoneno,
                'status' => 'pending',
                // Add any other relevant fields
            ]);
            //  return response()->json($result); // For debugging
            return response()->json([
                'success' => true,
                'message' => 'STK Push initiated successfully',
                'transaction_id' => $mpesaTransaction->id
            ]);
        } catch (RequestException $e) {
            Log::error('Mpesa API Error: ' . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error('Mpesa API Response: ' . $e->getResponse()->getBody());
            }
            // return response()->json(['error' => $e->getMessage()], 500); //For Debugging
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate STK Push',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function generateAccessToken()
    {
        $consumer_key = Config::get('mpesa.mpesa_consumer_key');
        $consumer_secret = Config::get('mpesa.mpesa_consumer_secret');
        $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
        $url = Config::get('mpesa.environment') === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        return $result->access_token;
    }
    private function generatePassword()
    {
        $shortcode = Config::get('mpesa.shortcode');
        $passkey = Config::get('mpesa.passkey');
        $timestamp = $this->getTimestamp();
        return base64_encode($shortcode . $passkey . $timestamp);
    }

    private function getTimestamp()
    {
        return date('YmdHis');
    }

    public function STKConfirm(Request $request)
    {
        Log::info('M-Pesa Callback received: ' . json_encode($request->all()));

        $callbackData = $request->Body['stkCallback'] ?? null;

        if (!$callbackData) {
            return response()->json(['status' => 'error', 'message' => 'Invalid callback data']);
        }

        $resultCode = $callbackData['ResultCode'];
        $resultDesc = $callbackData['ResultDesc'];
        $merchantRequestID = $callbackData['MerchantRequestID'];
        $checkoutRequestID = $callbackData['CheckoutRequestID'];

        $transaction = MpesaSTK::where('merchant_request_id', $merchantRequestID)
            ->where('checkout_request_id', $checkoutRequestID)
            ->first();

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaction not found']);
        }

        if ($resultCode == 0) {
            // Transaction was successful
            $amount = $callbackData['CallbackMetadata']['Item'][0]['Value'] ?? null;
            $mpesaReceiptNumber = $callbackData['CallbackMetadata']['Item'][1]['Value'] ?? null;
            $transactionDate = $callbackData['CallbackMetadata']['Item'][3]['Value'] ?? null;
            $phoneNumber = $callbackData['CallbackMetadata']['Item'][4]['Value'] ?? null;

            $transaction->update([
                'result_desc' => $resultDesc,
                'result_code' => $resultCode,
                'amount' => $amount,
                'mpesa_receipt_number' => $mpesaReceiptNumber,
                'transaction_date' => $transactionDate,
                'phone_number' => $phoneNumber,
                'status' => 'completed',
            ]);

            // Mark invoice as paid
            //   $paymentService = new PaymentService();
            //   $paymentService->markInvoiceAsPaid($transaction->invoice_id, $amount, $mpesaReceiptNumber);
        } else {
            // Transaction failed
            $transaction->update([
                'status' => 'failed',
                'result_desc' => $resultDesc,
                'result_code' => $resultCode,
            ]);
        }

        return response()->json(['status' => 'success']);
    }


    public function checkPaymentStatus(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $transaction = MpesaSTK::findOrFail($transactionId);
        $accessToken = $this->generateAccessToken();

        $url = "https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query";
        $client = new Client([
            'verify' => false // This disables SSL verification
        ]);
        $requestData = [
            'BusinessShortCode' => config('mpesa.shortcode'),
            'Password' => $this->generatePassword(),
            'Timestamp' => $this->getTimestamp(),
            'CheckoutRequestID' => $transaction->checkout_request_id
        ];


        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData
            ]);
            $responseBody = $response->getBody()->getContents();
            $result = json_decode($responseBody, true);
            // Log the full request and response for debugging
            Log::debug('M-Pesa API Request', [
                'url' => $url,
                'data' => $requestData,
                'headers' => $response->getHeaders()
            ]);


            Log::debug('M-Pesa API Response', [
                'status' => $response->getStatusCode(),
                'body' => $result
            ]);

            //   $result = json_decode($responseBody, true);


            if ($response->getStatusCode() == 200) {
                // Update the transaction status based on the response
                $transaction->result_code = $result['ResultCode'];
                $transaction->result_desc = $result['ResultDesc'];
                if ($result['ResultCode'] == 0) {

                    $transaction->status = 'completed';

                    // The MpesaReceiptNumber and TransactionDate might be available in some responses
                    $transaction->mpesa_receipt_number = $result['MpesaReceiptNumber'] ?? null;
                    $transaction->transaction_date = $result['TransactionDate'] ?? null;
                } elseif ($result['ResultCode'] == 1032) {
                    $transaction->status = 'pending';
                } else {
                    $transaction->status = 'failed';
                }

                $transaction->save();

                
                return response()->json([
                    'status' => $result['ResultCode'] == 0 ? 'completed' : 'failed',
                    'message' => $result['ResultDesc'],
                    'data' => $result  // Include full response data for debugging
                ]);
            } else {
                // Log the error response
                Log::error('M-Pesa API Error Response', [
                    'status' => $response->getStatusCode(),
                    'body' => $responseBody
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to check status',
                    'error_details' => [
                        'status_code' => $response->getStatusCode(),
                        'response_body' => $result
                    ]
                ], $response->getStatusCode());
            }
        } catch (RequestException $e) {
            // Log any exceptions
            Log::error('Exception in checkPaymentStatus', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking the payment status',
                'error_details' => [
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }
}
