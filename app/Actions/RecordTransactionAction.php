<?php
// app/Actions/UpdateNextDateAction.php

namespace App\Actions;

use App\Models\Chartofaccount;
use App\Models\Expense;
use App\Models\InvoiceItems;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentItems;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Unitcharge;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Model;

class RecordTransactionAction
{
    use AsAction;

   

    public function deposit(Model $model, Unitcharge $unitcharge = null)
    {
        //     Debit: Bank Account (Asset)
        //     Credit: Security Deposit Liability (Liability)

      
        $className = get_class($model);
        $transactionType = TransactionType::where('model', 'Deposit')->first();
       
            $description = Chartofaccount::where('id', $model->chartofaccount_id)->first();
            Transaction::create([
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id ?? null,
                'unitcharge_id' => $unitcharge->id ?? null,
                'charge_name' => $model->name,
                'transactionable_id' => $model->id,
                'transactionable_type' => $className, ///Model Name Unitcharge
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => $transactionType->debitaccount_id, //// decsrease the Bank Account amount
                'creditaccount_id' => $model->chartofaccount_id, //Increase the Liability account
                'amount' => $model->totalamount,
            ]);
        
        
    }

    public function invoiceCharges(Invoice $invoice, Unitcharge $unitcharge)
    {
        $className = get_class($invoice);
        $invoiceitems = InvoiceItems::where('invoice_id', $invoice->id)->get();
        $transactionType = TransactionType::where('name', 'Invoice Charges')->first();
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
                'debitaccount_id' => $transactionType->debitaccount_id,/// increase the Accounts Payable
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


    public function expense(Expense $expense)
    {
        
            $className = get_class($expense);
            $transactionType = TransactionType::where('name', 'Expenses')->first();
            $description = Chartofaccount::where('id', $expense->chartofaccount_id)->first();
            Transaction::create([
                'property_id' => $expense->property_id,
                'unit_id' => $expense->unit_id ?? null,
                'unitcharge_id' => null,
                'charge_name' => $expense->name,
                'transactionable_id' => $expense->id,
                'transactionable_type' => $className, ///Model Name Unitcharge
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => $expense->chartofaccount_id ?? $transactionType->debitaccount_id ,/// increase the Accounts Payable
                'creditaccount_id' =>$transactionType->creditaccount_id, ///All the income accounts
                'amount' => $expense->totalamount,
            ]);
        
    }

    public function payexpenses(Payment $payment, Model $model,)
    {
        $className = get_class($payment);
       
            $description = Chartofaccount::where('id', $model->chartofaccount_id)->first();
            $transactionType = TransactionType::where('name', 'Payment Of Expenses')->first();
            
            Transaction::create([
                'property_id' => $payment->property_id,
                'unit_id' => $payment->unit_id ?? null,
                'unitcharge_id' => null,
                'charge_name' => $model->name,
                'transactionable_id' => $payment->id,
                'transactionable_type' =>$className, ///Model Name Invoice
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => $transactionType->debitaccount_id, ///Increase the Accounts Payable
                'creditaccount_id' => $transactionType->creditaccount_id,/// Decrease the bank account
                'amount' => $payment->totalamount,
            ]);
        
    }

}
