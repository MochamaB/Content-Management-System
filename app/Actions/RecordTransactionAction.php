<?php
// app/Actions/UpdateNextDateAction.php

namespace App\Actions;

use App\Models\Chartofaccount;
use App\Models\Expense;
use App\Models\InvoiceItems;

use App\Models\Invoice;
use App\Models\LedgerEntry;
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

    public function transaction(Model $model, Unitcharge $unitcharge = null)
    {
        ////1.GET MODEL CLASS AND MODEL NAME
        $class = get_class($model);
        $modelName = class_basename($model);

        /////2. CHECK IF THE MODEL HAS MODEL ITEMS
        if ($modelName === 'Invoice' || $modelName === 'Payment') {
            // For other models (like Invoice), get the items as before
            $items = $model->getItems;
        } else {
            // For expenses, treat the entire expense as a single item
            $items = collect([$model]);
        }
        foreach ($items as $item) {
            $account = Chartofaccount::where('id', $item->chartofaccount_id)->first();
            $transactionType = TransactionType::where('model', $modelName)
                ->where('account_type', $item->accounts->account_type)
                ->first();

            // Initialize variables for debit and credit account IDs
            $debitAccountId = null;
            $creditAccountId = null;

            if ($transactionType) {
                /* Check if the debit account type matches the account type of the $item->chartofaccount_id
                - if it matches then use the account for the particular transaction 
                else use the default for the account type in transaction table */
                if ($transactionType->debit->account_type === $item->accounts->account_type) {
                    $debitAccountId = $item->chartofaccount_id;
                } else {
                    // Use the debit account ID from the transaction type table
                    $debitAccountId = $transactionType->debitaccount_id;
                }

               /* Check if the credit account type matches the account type of the $item->chartofaccount_id
                - if it matches then use the account for the particular transaction 
                else use the default for the account type in transaction table */
                if ($transactionType->credit->account_type === $item->accounts->account_type) {
                    $creditAccountId = $item->chartofaccount_id;
                } else {
                    // Use the credit account ID from the transaction type
                    $creditAccountId = $transactionType->creditaccount_id;
                }
            }
            $transaction = Transaction::create([
                'property_id' => $item->property_id ?? $model->property_id,
                'unit_id' => $item->unit_id ?? $model->unit_id,
                'unitcharge_id' =>  $unitcharge->id ?? null,
                'charge_name' => $item->charge_name ?? $item->name,
                'transactionable_id' => $model->id,
                'transactionable_type' => $class, ///Model Name Invoice
                'description' => $account->account_name, ///Description of the charge
                'debitaccount_id' => $debitAccountId, ///Increase the Income Accounts
                'creditaccount_id' => $creditAccountId, /// Decrease the Accounts Payable that was increased wehn invoices
                'amount' => $item->amount ?? $item->totalamount,
            ]);

            // Record ledger entry for debit
            LedgerEntry::create([
                'property_id' => $item->property_id ?? $model->property_id,
                'unit_id' => $item->unit_id ?? $model->unit_id,
                'chartofaccount_id' => $debitAccountId,
                'transaction_id' => $transaction->id,
                'amount' => $item->amount ?? $item->totalamount,
                'entry_type' => 'debit',
            ]);

            // Record ledger entry for credit
            LedgerEntry::create([
                'property_id' => $item->property_id ?? $model->property_id,
                'unit_id' => $item->unit_id ?? $model->unit_id,
                'chartofaccount_id' => $creditAccountId, // Change this to the appropriate account ID
                'transaction_id' => $transaction->id,
                'amount' => $item->amount ?? $item->totalamount,
                'entry_type' => 'credit',
            ]);
        }
    }

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
                'transactionable_type' => $className, ///Model Name Invoice
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => $transactionType->debitaccount_id, /// increase the Accounts Payable
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
                'transactionable_type' => $className, ///Model Name Invoice
                'description' => $description->account_name, ///Description of the charge
                'debitaccount_id' => $item->chartofaccount_id, ///Increase the Income Accounts
                'creditaccount_id' => 2, /// Decrease the Accounts Payable that was increased wehn invoices
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
            'debitaccount_id' => $expense->chartofaccount_id ?? $transactionType->debitaccount_id, /// increase the Accounts Payable
            'creditaccount_id' => $transactionType->creditaccount_id, ///All the income accounts
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
            'transactionable_type' => $className, ///Model Name Invoice
            'description' => $description->account_name, ///Description of the charge
            'debitaccount_id' => $transactionType->debitaccount_id, ///Increase the Accounts Payable
            'creditaccount_id' => $transactionType->creditaccount_id, /// Decrease the bank account
            'amount' => $payment->totalamount,
        ]);
    }
}
