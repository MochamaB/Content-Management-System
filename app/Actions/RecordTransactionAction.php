<?php
// app/Actions/UpdateNextDateAction.php

namespace App\Actions;

use App\Models\Chartofaccount;
use App\Models\InvoiceItems;
use App\Models\PaymentVoucherItems;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Unitcharge;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Model;

class RecordTransactionAction
{
    use AsAction;

    public function securitydeposit(Model $model)
    {
        //     Debit: Bank Account (Asset)
        //     Credit: Security Deposit Liability (Liability)

        $paymentvoucheritems = PaymentVoucherItems::where('paymentvoucher_id', $model->id)->get();
        $modelname = class_basename($model);

        foreach ($paymentvoucheritems as $item) {
            $description = Chartofaccount::where('id', $item->chartofaccount_id)->first();
            Transaction::create([
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'charge_name' => $model->voucher_type,
                'transactionable_id' => $model->id,
                'transactionable_type' => $modelname, ///Model Name Unitcharge
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => 1, ////Bank Account
                'creditaccount_id' => $item->chartofaccount_id,
                'amount' => $item->amount,
            ]);
        }
    }

    public function invoiceCharges(Invoice $invoice)
    {
        $className = get_class($invoice);
        $invoiceitems = InvoiceItems::where('invoice_id', $invoice->id)->get();

        foreach ($invoiceitems as $item) {
            $description = Chartofaccount::where('id', $item->chartofaccount_id)->first();
            Transaction::create([
                'property_id' => $invoice->property_id,
                'unit_id' => $invoice->unit_id,
                'charge_name' => $item->charge_name,
                'transactionable_id' => $invoice->id,
                'transactionable_type' =>$className, ///Model Name Invoice
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => 2,///Accounts Payable
                'creditaccount_id' => $item->chartofaccount_id,
                'amount' => $item->amount,
            ]);
        }
    }
}
