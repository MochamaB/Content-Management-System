<?php

// app/Services/PaymentService.php

namespace App\Services;

use Carbon\Carbon;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Actions\UpdateDueDateAction;
use App\Actions\UpdateNextDateAction;
use App\Actions\RecordTransactionAction;
use App\Actions\CalculateTaxAction;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\PaymentItems;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Transaction;
use App\Notifications\InvoiceGeneratedNotification;
use App\Notifications\PaymentNotification;
use App\Notifications\PaymentTextNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Services\InvoiceService;

class PaymentService
{
    private $calculateTotalAmountAction;
    private $calculateTaxAction;
    private $updateDueDateAction;
    private $updateNextDateAction;
    private $recordTransactionAction;
    private $invoiceService;


    public function __construct(
        CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        UpdateNextDateAction $updateNextDateAction,
        UpdateDueDateAction $updateDueDateAction,
        RecordTransactionAction $recordTransactionAction,
        CalculateTaxAction $calculateTaxAction,
        InvoiceService $invoiceService
    ) {
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
        $this->updateNextDateAction = $updateNextDateAction;
        $this->updateDueDateAction = $updateDueDateAction;
        $this->recordTransactionAction = $recordTransactionAction;
        $this->calculateTaxAction = $calculateTaxAction;
        $this->invoiceService = $invoiceService;
    }

    ///////// ALLOCATED PAYMENTS /////////////////////
    public function generatePayment(Model $model, $validatedData = null, $mpesaTransaction = null)
    {

        $paymentData = $this->getPaymentHeaderData($model, $validatedData, $mpesaTransaction);


        //1. Create Payment Header Data
        $payment = $this->createPayment($paymentData);

       
        //2. Update Payment Status in Model Headers
          $this->calculateTotalAmountAction->payment($payment, $model);

        //3. Calculate Taxes payment
        $this->calculateTaxAction->calculateTax($payment);

        //4. Create Transactions for ledger
        $this->recordTransactionAction->payments($payment, $model);

        //5. Send Email/Notification to the Tenant containing the receipt.
        $this->paymentEmail($payment);


        return $payment;
    }






    //////4. GET DATA FOR PAYMENT HEADER DATA
    private function getPaymentHeaderData($model, $validatedData, $mpesaTransaction)
    {
        $className = get_class($model);
        $user = Auth::user();
        ////REFRENCE NO
        if (!is_null($validatedData)) {
            $referenceno = $validatedData['referenceno'];
            $paymentCode = $validatedData['payment_code'];
            $paymentMethod = $validatedData['payment_method_id'];
            $totalamount = $validatedData['totalamount'];

            return [
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'model_type' => $className, ///This has plymorphism because payment can be an invoice,expense or voucher
                'model_id' => $model->id,
                'referenceno' => $model->referenceno,
                'payment_method_id' => $paymentMethod,
                'payment_code' => $paymentCode,
                'totalamount' =>  $totalamount,
                'taxamount' => null,
                'received_by' => $user->email,
                'reviewed_by' => null,
                'invoicedate' => $model->created_at,
            ];
        } else if (!is_null($mpesaTransaction)) {
            $mpesa = PaymentMethod::where('property_id', $model->property_id)
                ->whereRaw('LOWER(name) LIKE ?', ['%m%pesa%'])
                ->first();
            return [
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'model_type' => $className, ///This has plymorphism because payment can be an invoice,expense or voucher
                'model_id' => $model->id,
                'referenceno' => $model->referenceno,
                'payment_method_id' => $mpesa->id,
                'payment_code' => $mpesaTransaction->mpesa_receipt_number,
                'totalamount' =>  $mpesaTransaction->amount,
                'taxamount' => null,
                'received_by' => $user->email ?? $model->model->email,
                'reviewed_by' => null,
                'invoicedate' => $model->created_at,
            ];
        }
    }

    private function createPayment($data)
    {
        return Payment::create($data);
    }



    private function createPaymentItems($model, $payment, $validatedData, $mpesaTransaction)
    {
        // Create Payment items
        $items = $model->getItems;

        $perPaymentAmounts = $validatedData['amount'] ?? $mpesaTransaction->amount;

        foreach ($items as $key => $item) {
            // Get the corresponding amount
            $amount = $perPaymentAmounts[$key];
            PaymentItems::create([
                'payment_id' => $payment->id,
                'unitcharge_id' => $item->unitcharge_id ?? null,
                'chartofaccount_id' => $item->chartofaccount_id,
                'charge_name' => $item->charge_name ?? $item->description,
                'description' => $item->description,
                'amount' => $amount,
            ]);
        }
    }

    /////////Send Email
    public function paymentEmail($payment)
    {
        $user = $payment->model->model;

        if($payment->model instanceof Invoice)
         {
            $invoice = $payment->model;
            $lease = Lease::where('unit_id',$invoice->unit_id)->first();
            $unitchargeId = $payment->model->getItems->pluck('unitcharge_id')->first();
            //    dd($unitchargeIds);
            $sixMonths = now()->subMonths(6);
            $transactions = Transaction::where('created_at', '>=', $sixMonths)
                ->where('unit_id', $payment->model->unit_id)
                ->where('unitcharge_id', $unitchargeId)
                ->get();
            $groupedInvoiceItems = $transactions->groupBy('unitcharge_id');
    
            ////Opening Balance
            $openingBalance = $this->invoiceService->calculateOpeningBalance($payment->model);
                //// Data for the Payment Methods
            $viewContent = View::make('email.statement', [
                'user' => $user,
                'invoice' => $invoice,
                'transactions' => $transactions,
                'groupedInvoiceItems' => $groupedInvoiceItems,
                'openingBalance' => $openingBalance,
                ])->render();

                //CHECK IF EMAILS FOR THE LEASE ARE ENABLED
                $emailNotificationsEnabled = Setting::getSettingForModel(get_class($lease), $lease->id, 'invoiceemail');
                //CHECK IF EMAILS FOR THE LEASE ARE ENABLED
               $textNotificationsEnabled = Setting::getSettingForModel(get_class($lease), $lease->id, 'invoicetexts');
                }else{
            $viewContent = View::make('email.payment', [
                'payment' => $payment,
            ])->render();
            }

        try {
            if ($emailNotificationsEnabled !== 'NO') {
                $user->notify(new PaymentNotification($payment, $user, $viewContent));
            }
            if ($textNotificationsEnabled !== 'NO') {
                $user->notify(new PaymentTextNotification($payment, $user, $viewContent));
            }
        } catch (\Exception $e) {
            // Log the error or perform any necessary actions
            Log::error('Failed to send payment notification: ' . $e->getMessage());
        }
    }
}
