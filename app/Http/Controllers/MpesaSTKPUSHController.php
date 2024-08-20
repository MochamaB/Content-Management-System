<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Mpesa\STKPush;
use App\Models\MpesaSTK;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Iankumu\Mpesa\Facades\Mpesa;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\PaymentService;

class MpesaSTKPUSHController extends Controller
{
    public $result_code = 1;
    public $result_desc = 'An Error Occured';
    protected $controller;
    protected $model;
    private $paymentService;
    protected $paymentMethod;

    public function __construct(PaymentService $paymentService = null)
    {
        $this->model = MpesaSTK::class;
        $this->controller = collect([
            '0' => 'mpesaSTK', // Use a string for the controller name
            '1' => ' MPESA',
        ]);

        $this->paymentService = $paymentService;
    }

    

    public function MpesaPayment($id)
    {
        $invoice = Invoice::find($id);

        $amountPaid = $invoice->payments->sum('totalamount');
        $amountdue = $invoice->totalamount - $amountPaid;
        if ($amountdue <= 0) {
            return redirect()->back()->with('statuserror', 'Invoice has already been fully paid');
        }
        $paymentMethod = $this->getPaymentMethod($invoice->id);
        
       

       
        //   dd($shortcode);
        return View('admin.Lease.mpesapayment', compact('invoice','paymentMethod'));
    }


    public function STKPush(Request $request)
    {
        
        $amount = $request->input('amount');
        $phoneno = $request->input('phonenumber');
        // Check if the phone number starts with 0 and replace it with 254
        if (strpos($phoneno, '0') === 0) {
            $phoneno = '254' . substr($phoneno, 1);
        }
        $account_number = $request->input('account_number');
        $invoice_id = $request->input('invoice_id');
        $paymentMethod = $this->getPaymentMethod($invoice_id);

        //// Configurations for the stk call /////
        $businessShortCode = $paymentMethod->config->mpesa_shortcode ?? Config::get('mpesa.shortcode');
        $accountReference = null;
        $transactionType = null;
        if ($paymentMethod) {
            if ($paymentMethod->type == 'paybill') {
                $accountReference = $paymentMethod->config->mpesa_account_number ?? $account_number;
                $transactionType = 'CustomerPayBillOnline';
            } elseif ($paymentMethod->type == 'till') {
                $accountReference = null; // Explicitly set to null for till type
                $transactionType = 'CustomerBuyGoodsOnline';
            }
        }
        $client = new Client([
            'verify' => false // This disables SSL verification
        ]);
        try {
            $timestamp = $this->getTimestamp();
            $password = $this->generatePassword($invoice_id);

            $response = $client->post(
                Config::get('mpesa.environment') === 'sandbox'
                    ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
                    : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
                [
                    'json' => [
                        'BusinessShortCode' => $businessShortCode,
                        'Password' => $password,
                        'Timestamp' => $timestamp,
                        'TransactionType' => $transactionType,
                        'Amount' => $amount,
                        'PartyA' => $phoneno,
                        'PartyB' => $businessShortCode,
                        'PhoneNumber' => $phoneno,
                        'CallBackURL' => Config::get('mpesa.callback_url'),
                        'AccountReference' => $accountReference,
                        'TransactionDesc' => 'Test STK Push'
                    ],
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->generateAccessToken($invoice_id),
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
    private function generateAccessToken($invoice_id)
    {
        $paymentMethod = $this->getPaymentMethod($invoice_id);

        $consumer_key = $paymentMethod->config->consumer_key ?? Config::get('mpesa.mpesa_consumer_key');
        $consumer_secret = $paymentMethod->config->consumer_secret ?? Config::get('mpesa.mpesa_consumer_secret');
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
    private function generatePassword($invoice_id)
    {
        $paymentMethod = $this->getPaymentMethod($invoice_id);

        $shortcode = $paymentMethod->config->mpesa_shortcode ??  Config::get('mpesa.shortcode');
        $passkey = $paymentMethod->config->passkey ??  Config::get('mpesa.passkey');
        $timestamp = $this->getTimestamp();
        return base64_encode($shortcode . $passkey . $timestamp);
    }

    private function getTimestamp()
    {
        return date('YmdHis');
    }

    public function STKConfirm(Request $request)
    {

        // Log the entire request for debugging
        Log::info('MPESA STK Callback received: ' . json_encode($request->all()));

        // Validate the request
        $callbackData = $request->all();

        if (!isset($callbackData['Body']['stkCallback'])) {
            Log::error('Invalid STK Callback data received');
            return response()->json(['success' => false, 'message' => 'Invalid callback data'], 400);
        }

        $resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
        $resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'];
        $merchantRequestID = $callbackData['Body']['stkCallback']['MerchantRequestID'];
        $checkoutRequestID = $callbackData['Body']['stkCallback']['CheckoutRequestID'];

        // Find the corresponding transaction
        $transaction = MpesaSTK::where('merchant_request_id', $merchantRequestID)
            ->where('checkout_request_id', $checkoutRequestID)
            ->first();

        if (!$transaction) {
            Log::error('Transaction not found for MerchantRequestID: ' . $merchantRequestID);
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        // Update the transaction status
        if ($resultCode == 0) {
            // Transaction was successful
            $transaction->result_code = $resultCode;
            $transaction->status = 'completed';
            $transaction->result_desc = $resultDesc;

            // Extract additional details if needed
            if (isset($callbackData['Body']['stkCallback']['CallbackMetadata'])) {
                $metadata = collect($callbackData['Body']['stkCallback']['CallbackMetadata']['Item']);


                $transaction->mpesa_receipt_number = $metadata->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;
                $transaction->transaction_date = $metadata->firstWhere('Name', 'TransactionDate')['Value'] ?? null;
                // Add any other fields you want to save
            }
        } else {
            // Transaction failed
            $transaction->status = 'failed';
            $transaction->result_desc = $resultDesc;
        }

        $transaction->save();

        // You can add additional logic here, such as notifying the user or updating other parts of your system

        // Always respond with a success to acknowledge receipt of the callback
        return response()->json([
            'success' => true,
            'message' => 'Callback processed successfully'
        ]);
    }

    public function checkPaymentStatus(Request $request)
    {
        $invoice_id = $request->input('invoice_id');
        $transactionId = $request->input('transaction_id');
        $transaction = MpesaSTK::findOrFail($transactionId);
        $accessToken = $this->generateAccessToken($invoice_id);
        $paymentMethod = $this->getPaymentMethod($invoice_id);

        $url = Config::get('mpesa.environment') === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query'
            : 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query';

        $client = new Client([
            'verify' => false // This disables SSL verification
        ]);
        $requestData = [
            'BusinessShortCode' => $paymentMethod->config->mpesa_shortcode ?? config('mpesa.shortcode'),
            'Password' => $this->generatePassword($invoice_id),
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

            // Check if ResultCode exists in the response
            if (isset($result['ResultCode'])) {
                $transaction->result_code = $result['ResultCode'];
                $transaction->result_desc = $result['ResultDesc'] ?? 'No description provided';

                switch ($result['ResultCode']) {
                    case 0:
                        $transaction->status = 'completed';
                        $success = true;
                        $message = 'Payment has been received';
                        break;
                    case 1:
                        $transaction->status = 'insufficient_funds';
                        $success = false;
                        $message = 'You have insufficient funds to complete this payment';
                        break;
                    case 1032:
                        $transaction->status = 'cancelled';
                        $success = false;
                        $message = 'You cancelled the payment transaction';
                        break;
                    case 1037:
                        $transaction->status = 'timeout';
                        $success = false;
                        $message = 'The transaction has timed out';
                        break;
                    default:
                        $transaction->status = 'failed';
                        $success = false;
                        $message = 'The payment has failed. Try again';
                        break;
                }

                $transaction->save();

                // Return error if transaction is not completed
                if ($transaction->status !== 'completed') {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'data' => $result
                    ]);
                } else {
                    // check if the referenceno
                    $model = Invoice::where('referenceno', $transaction->referenceno)->firstOrFail();
                    if($model){
                        $payment = $this->paymentService->generatePayment($model, null, $transaction);
                    }
                  
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'transaction_id' => $transaction->id,
                        'payment_id' => $payment->id, // Include payment ID
                        'data' => $result  // Include full response data for debugging
                    ]);
                }
            } else {
                // If ResultCode is not in the response, consider it an error
                Log::error('M-Pesa API Error: ResultCode not found in response', [
                    'response' => $result
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from MPESA API',
                    'data' => $result
                ], 400);
            }
        } catch (RequestException $e) {
            // Log any exceptions
            Log::error('Exception in checkPaymentStatus', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking the payment status',
                'error_details' => [
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    public function paymentConfirm(Request $request)
    {

        $transaction = MpesaSTK::findOrFail($request->transactionid2);
        //Find the invoice
        if ($transaction->status !== 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction is not completed'
            ], 400);
        }
        if ($transaction->mpesa_receipt_number == 'null') {
            $transaction->mpesa_receipt_number = $request->mpesa_receipt_number;
            $transaction->save;
        }
        $model = Invoice::where('referenceno', $transaction->referenceno)->firstOrFail();
        $payment = $this->paymentService->generatePayment($model, null, $transaction);
    }

    public function receipt($id)
    {
      //  $id = 1;
        $payment = Payment::find($id);
        return View('email.payment', compact('payment'));

    }

    protected function getPaymentMethod($invoice_id)
    {
        if (!$this->paymentMethod) {
            $invoice = Invoice::findOrFail($invoice_id);
            
            $this->paymentMethod = PaymentMethod::where('property_id', $invoice->property_id)
                ->whereRaw('LOWER(name) LIKE ?', ['%m%pesa%'])
                ->with('config')
                ->first();
        }
        
        return $this->paymentMethod;
    }
}
