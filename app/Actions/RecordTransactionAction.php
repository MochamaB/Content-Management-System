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
use Illuminate\Support\Facades\Log;

class RecordTransactionAction
{
    use AsAction;

    public function transaction(Model $model, Unitcharge $unitcharge = null)
    {
        ////1.GET MODEL CLASS AND MODEL NAME
        $class = get_class($model);
        $modelName = class_basename($model);

        /////2.  GET MODEL ITEMS

        // For other models (like Invoice), get the items as before
        $items = $model->getItems;

        foreach ($items as $item) {
            //// In model payment make sure transactions have the same unitcharge_id
            if ($model instanceof Payment) {
                $chargeid = PaymentItems::where('payment_id', $model->id)->first();
            }

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
            $transaction = new Transaction([
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id ?? null,
                'unitcharge_id' =>  $unitcharge->id ?? $chargeid->unitcharge_id ??  null,
                'charge_name' => $item->charge_name ?? $item->description ?? $model->name,
                'transactionable_id' => $model->id,
                'transactionable_type' => $class, ///Model Name Invoice
                'description' => $account->account_name, ///Description of the charge
                'debitaccount_id' => $debitAccountId, ///Increase the Income Accounts
                'creditaccount_id' => $creditAccountId, /// Decrease the Accounts Payable that was increased wehn invoices
                'amount' => $item->amount,
                'created_at' => $model->created_at, // Set the created_at timestamp to match the model
                'updated_at' => $model->created_at // Optionally, set the updated_at timestamp to match the model as well
            ]);
            // Set the created_at and updated_at explicitly
            $transaction->created_at = $model->created_at;

            // Save the transaction
            $transaction->save();

            // Record ledger entry for debit
             $ledgerone = new LedgerEntry([
                'property_id' => $transaction->property_id,
                'unit_id' => $transaction->unit_id,
                'chartofaccount_id' => $debitAccountId,
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'entry_type' => 'debit',
                'created_at' => $transaction->created_at, // Set the created_at timestamp to match the model
                'updated_at' => $transaction->created_at 
            ]);
            $ledgerone->created_at = $model->created_at;
            $ledgerone->save();

            // Record ledger entry for credit
            $ledgertwo = new LedgerEntry([
                'property_id' => $transaction->property_id,
                'unit_id' => $transaction->unit_id,
                'chartofaccount_id' => $creditAccountId, // Change this to the appropriate account ID
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'entry_type' => 'credit',
                'created_at' => $transaction->created_at, // Set the created_at timestamp to match the model
                'updated_at' => $transaction->created_at 
            ]);
            $ledgertwo->created_at = $model->created_at;
            $ledgertwo->save();
        }
    }

    public function payments(Payment $payment, Model $model)
    {
        ////1.GET MODEL CLASS AND MODEL NAME
        $class = get_class($model);
        $modelName = class_basename($model);
        $paymentModelName = class_basename($payment);

        /////2.  GET MODEL ITEMS

        // For other models (like Invoice), get the items as before
        $items = $model->getItems;
        $firstItem = $items->first();

        $remainingAmount = $payment->totalamount;

        foreach ($items as $item) {


            $transactionType = TransactionType::where('model', $paymentModelName)
                ->where('account_type', $item->accounts->account_type)
                ->first();

            $debitAccountId = null;
            $creditAccountId = null;
            if ($transactionType) {
                $debitAccountId = $transactionType->debitaccount_id;

                $creditAccountId = $transactionType->creditaccount_id;
            }
            // Calculate the amount to apply to this item
            $amountToApply = min($remainingAmount, $item->amount);


            $transaction = Transaction::create([
                'property_id' => $payment->property_id,
                'unit_id' => $payment->unit_id ?? null,
                'unitcharge_id' =>  $firstItem->unitcharge_id ??  null,
                'charge_name' => $item->charge_name ?? $item->description ?? $model->name,
                'transactionable_id' => $payment->id,
                'transactionable_type' => get_class($payment), ///Model Name Invoice
                'description' => $item->accounts->account_name . ' Payment', ///Description of the charge
                'debitaccount_id' => $debitAccountId, ///Increase the Income Accounts
                'creditaccount_id' => $creditAccountId, /// Decrease the Accounts Payable that was increased wehn invoices
                'amount' => $amountToApply,
            ]);
            // Record ledger entry for debit
            LedgerEntry::create([
                'property_id' => $transaction->property_id,
                'unit_id' => $transaction->unit_id,
                'chartofaccount_id' => $debitAccountId,
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'entry_type' => 'debit',
            ]);

            // Record ledger entry for credit
            LedgerEntry::create([
                'property_id' => $transaction->property_id,
                'unit_id' => $transaction->unit_id,
                'chartofaccount_id' => $creditAccountId, // Change this to the appropriate account ID
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'entry_type' => 'credit',
            ]);
            $remainingAmount -= $amountToApply;
            if ($remainingAmount <= 0) {
                break;
            }
        }
    }


    //Edit Transaction -->
    public function updateTransaction(Model $model)
    {
        Transaction::where('transactionable_id', $model->id)
            ->where('transactionable_type', get_class($model))
            ->delete();
            
             // Create new transactions
            
                $this->transaction($model);
             
            
    }  
    public function updatePaymentTransaction($payment, Model $model)
    {
        Transaction::where('transactionable_id', $model->id)
            ->where('transactionable_type', get_class($model))
            ->delete();

             // Create new transactions
                $this->payments($payment,  $model);
    }  
   
   



}
