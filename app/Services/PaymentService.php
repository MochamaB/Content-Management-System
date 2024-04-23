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
use App\Notifications\InvoiceGeneratedNotification;
use App\Notifications\PaymentNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;



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


    public function generatePayment(Model $model, $validatedData)
    {

        $paymentData = $this->getPaymentHeaderData($model, $validatedData);


        //1. Create Payment Header Data
        $payment = $this->createPayment($paymentData);

        //2. Create Payment items
        $this->createPaymentItems($model, $payment, $validatedData);

        //3. Update Total Amount in Invoice Header
        $this->calculateTotalAmountAction->payment($payment);

        //4. Create Transactions for ledger
        $this->recordTransactionAction->payments($payment);

        //5. Send Email/Notification to the Tenant containing the receipt.
        $user = $payment->model->model;
        $user->notify(new PaymentNotification($payment, $user));


        return $payment;
    }



    //////4. GET DATA FOR PAYMENT HEADER DATA
    private function getPaymentHeaderData($model, $validatedData)
    {
        ////REFRENCE NO

        $referenceno = $validatedData['referenceno'];
        $className = get_class($model);
        $user = Auth::user();
        $paymentCode = $validatedData['payment_code'];
        $paymentMethod = $validatedData['payment_method_id'];

        return [
            'property_id' => $model->property_id,
            'unit_id' => $model->unit_id,
            'model_type' => $className, ///This has plymorphism because payment can be an invoice,expense or voucher
            'model_id' => $model->id,
            'referenceno' => $referenceno,
            'payment_method_id' => $paymentMethod,
            'payment_code' => $paymentCode,
            'totalamount' => null,
            'received_by' => $user->email,
            'reviewed_by' => null,
            'invoicedate' => $model->created_at,
        ];
    }

    private function createPayment($data)
    {
        return Payment::create($data);
    }



    private function createPaymentItems($model, $payment, $validatedData)
    {
        // Create Payment items
        // Check if model is an instance of Expense
        if ($model instanceof Invoice) {
          // For other models (like Invoice), get the items as before
          $items = $model->getItems;
        } else {
           // For expenses, treat the entire expense as a single item
            $items = collect([$model]);
        }
        $perPaymentAmounts = $validatedData['amount'];

        foreach ($items as $key => $item) {
            // Get the corresponding amount
            $amount = $perPaymentAmounts[$key];
            PaymentItems::create([
                'payment_id' => $payment->id,
                'unitcharge_id' => $item->unitcharge_id ?? null,
                'chartofaccount_id' => $item->chartofaccount_id,
                'charge_name' => $item->charge_name ?? $item->name,
                'description' => '',
                'amount' => $amount,
            ]);
        }
    }

    /////////Pay for expenses
    public function generateExpensePayment(Model $model, $validatedData)
    {
        $user = Auth::user();
        $payment = new Payment();
        $payment->fill($validatedData);
        $payment->received_by = $user->email;
        $payment->save();
        //2. Create Transactions for ledger
        $this->recordTransactionAction->payexpenses($payment, $model);

        //3. Send Email/Notification to the Tenant containing the receipt.
        $user = $payment->model->model;
        $user->notify(new PaymentNotification($payment, $user));
    }
}
