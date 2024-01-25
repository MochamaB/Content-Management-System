<?php

// app/Services/PaymentService.php

namespace App\Services;

use Carbon\Carbon;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Actions\UpdateDueDateAction;
use App\Actions\UpdateNextDateAction;
use App\Actions\RecordTransactionAction;
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
        $today = Carbon::now();
        $invoicenodate = $today->format('ym');
        $unitnumber = $model->unit->unit_number;
        $referenceno = 'RCT-' . $model->id . '-' . $invoicenodate . $unitnumber;
        $className = get_class($model);
        $user = Auth::user();

        $paymentCode = $validatedData['payment_code'];
        $PaymentMethod = $validatedData['payment_method_id'];

        return [
            'property_id' => $model->property_id,
            'unit_id' => $model->unit_id,
            'model_type' => $className, ///This has plymorphism because an invoice can also be sent to a vendor.
            'model_id' => $model->id,
            'referenceno' => $referenceno,
            'payment_method_id' => $PaymentMethod,
            'payment_code' => $paymentCode,
            'totalamount' => null,
            'received_by' => $user->email,
            'reviewed_by' => null,
        ];
    }

    private function createPayment($data)
    {
        return Payment::create($data);
    }



    private function createPaymentItems($model, $payment, $validatedData)
    {
        // Create Payment items
        $items = $model->getItems;
        $perPaymentAmounts = $validatedData['amount'];

        foreach ($items as $key => $item) {
            // Get the corresponding amount
            $amount = $perPaymentAmounts[$key];
            PaymentItems::create([
                'payment_id' => $payment->id,
                'unitcharge_id' => $item->unitcharge_id,
                'chartofaccount_id' => 1,
                'charge_name' => $item->charge_name,
                'description' => '',
                'amount' => $amount,
            ]);
        }
    }
}
