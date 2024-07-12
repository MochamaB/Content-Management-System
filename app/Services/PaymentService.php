<?php

// app/Services/PaymentService.php

namespace App\Services;

use Carbon\Carbon;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Actions\UpdateDueDateAction;
use App\Actions\UpdateNextDateAction;
use App\Actions\RecordTransactionAction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentItems;
use App\Models\PaymentMethod;
use App\Notifications\InvoiceGeneratedNotification;
use App\Notifications\PaymentNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class PaymentService
{
    private $calculateTotalAmountAction;
    private $updateDueDateAction;
    private $updateNextDateAction;
    private $recordTransactionAction;


    public function __construct(
        CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        UpdateNextDateAction $updateNextDateAction,
        UpdateDueDateAction $updateDueDateAction,
        RecordTransactionAction $recordTransactionAction
    ) {
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
        $this->updateNextDateAction = $updateNextDateAction;
        $this->updateDueDateAction = $updateDueDateAction;
        $this->recordTransactionAction = $recordTransactionAction;
    }

    ///////// ALLOCATED PAYMENTS /////////////////////
    public function generatePayment(Model $model, $validatedData = null, $mpesaTransaction = null)
    {

        $paymentData = $this->getPaymentHeaderData($model, $validatedData,$mpesaTransaction);


        //1. Create Payment Header Data
        $payment = $this->createPayment($paymentData);

        //2. Create Payment items
        $this->createPaymentItems($model, $payment, $validatedData,$mpesaTransaction);

        //3. Update Total Amount in Payment Header
        $this->calculateTotalAmountAction->payment($payment, $model);

        //4. Create Transactions for ledger
        $this->recordTransactionAction->transaction($payment);

        //5. Send Email/Notification to the Tenant containing the receipt.
        $this->paymentEmail($payment);


        return $payment;
    }

    




    //////4. GET DATA FOR PAYMENT HEADER DATA
    private function getPaymentHeaderData($model, $validatedData,$mpesaTransaction)
    {
        $className = get_class($model);
        $user = Auth::user();
        ////REFRENCE NO
        if (!is_null($validatedData)) {
            $referenceno = $validatedData['referenceno'];
            $paymentCode = $validatedData['payment_code'];
            $paymentMethod = $validatedData['payment_method_id'];
            $amount = $validatedData['amount'];

            return [
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'model_type' => $className, ///This has plymorphism because payment can be an invoice,expense or voucher
                'model_id' => $model->id,
                'referenceno' => $model->referenceno,
                'payment_method_id' => $paymentMethod,
                'payment_code' => $paymentCode,
                'totalamount' =>  $amount,
                'received_by' => $user->email,
                'reviewed_by' => null,
                'invoicedate' => $model->created_at,
            ];
        } else if (!is_null($mpesaTransaction)) {
            $mpesa = PaymentMethod::where('property_id',$model->property_id)
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



    private function createPaymentItems($model, $payment, $validatedData,$mpesaTransaction)
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


        $viewContent = View::make('email.payment', [
            'payment' => $payment,
        ])->render();

        //   try {
        $user->notify(new PaymentNotification($payment, $user, $viewContent));
        //   } catch (\Exception $e) {
        // Log the error or perform any necessary actions
        //       Log::error('Failed to send payment notification: ' . $e->getMessage());
        //   }


    }
}
