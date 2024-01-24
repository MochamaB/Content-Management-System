<?php
// app/Actions/UpdateNextDateAction.php

namespace App\Actions;

use App\Models\Chartofaccount;
use App\Models\InvoiceItems;
use App\Models\PaymentVoucherItems;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentItems;
use App\Models\Transaction;
use App\Models\Unitcharge;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Model;

class RecordTransactionAction
{
    use AsAction;

    public function voucherCharges(Model $model, Unitcharge $unitcharge)
    {
        //     Debit: Bank Account (Asset)
        //     Credit: Security Deposit Liability (Liability)

        $paymentvoucheritems = PaymentVoucherItems::where('paymentvoucher_id', $model->id)->get();
        $className = get_class($model);
        ///Instead of VoucherItems recordtransaction on Invoice
                    ///Model ////////
        foreach ($paymentvoucheritems as $item) {
            $description = Chartofaccount::where('id', $item->chartofaccount_id)->first();
            Transaction::create([
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'unitcharge_id' => $unitcharge->id,
                'charge_name' => $item->charge_name,
                'transactionable_id' => $model->id,
                'transactionable_type' => $className, ///Model Name Unitcharge
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => 1, //// decsrease the Bank Account amount
                'creditaccount_id' => $item->chartofaccount_id, //Increase the Liability account
                'amount' => $item->amount,
            ]);
        }
    }

    public function invoiceCharges(Invoice $invoice, Unitcharge $unitcharge)
    {
        $className = get_class($invoice);
        $invoiceitems = InvoiceItems::where('invoice_id', $invoice->id)->get();

        foreach ($invoiceitems as $item) {
            $description = Chartofaccount::where('id', $item->chartofaccount_id)->first();
            Transaction::create([
                'property_id' => $invoice->property_id,
                'unit_id' => $invoice->unit_id,
                'unitcharge_id' => $unitcharge->id,
                'charge_name' => $item->charge_name,
                'transactionable_id' => $invoice->id,
                'transactionable_type' =>$className, ///Model Name Invoice
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => 2,/// increase the Accounts Payable
                'creditaccount_id' => $item->chartofaccount_id, ///Decrease the income accounts
                'amount' => $item->amount,
            ]);
        }
    }

    public function payments(Payment $payment)
    {
        $className = get_class($payment);
        $paymentitems = PaymentItems::where('payment_id', $payment->id)->get();
        $chargeid = PaymentItems::where('payment_id', $payment->id)->first();

        foreach ($paymentitems as $item) {
            $description = Chartofaccount::where('id', $item->chartofaccount_id)->first();
            Transaction::create([
                'property_id' => $payment->property_id,
                'unit_id' => $payment->unit_id,
                'unitcharge_id' => $chargeid->unitcharge_id,
                'charge_name' => $item->charge_name,
                'transactionable_id' => $payment->id,
                'transactionable_type' =>$className, ///Model Name Invoice
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => $item->chartofaccount_id, ///Increase the Income Accounts
                'creditaccount_id' => 2,/// Decrease the Accounts Payable that was increased wehn invoices
                'amount' => $item->amount,
            ]);
        }
    }

    public function voucherChargesIncome(Model $model, Unitcharge $unitcharge)
    {
       
        $paymentvoucheritems = PaymentVoucherItems::where('paymentvoucher_id', $model->id)->get();
        $className = get_class($model);
        ///Instead of VoucherItems recordtransaction on Invoice
                    ///Model ////////
        foreach ($paymentvoucheritems as $item) {
            $description = Chartofaccount::where('id', $item->chartofaccount_id)->first();
            Transaction::create([
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'unitcharge_id' => $unitcharge->id,
                'charge_name' => $item->charge_name,
                'transactionable_id' => $model->id,
                'transactionable_type' => $className, ///Model Name Unitcharge
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => 2,/// increase the Accounts Payable
                'creditaccount_id' => $item->chartofaccount_id, ///All the income accounts
                'amount' => $item->amount,
            ]);
        }
    }

}
