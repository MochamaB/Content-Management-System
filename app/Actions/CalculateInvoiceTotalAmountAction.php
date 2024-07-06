<?php

// app/Actions/CalculateInvoiceTotalAmountAction.php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Deposit;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Model;

class CalculateInvoiceTotalAmountAction
{
    use AsAction;
    public function handle(Invoice $invoice)
    {
        // Calculate the total amount for the given invoice
        $totalAmount = DB::table('invoice_items')
            ->where('invoice_id', $invoice->id)
            ->sum('amount');

        // Update the totalamount field in the invoice header
        $invoice->update(['totalamount' => $totalAmount]);
    }

    public function total(Model $model)
    {
        // Calculate the total amount for the given model
        $items = $model->getItems;
        $totalAmount = $items->sum('amount');
        // Update the totalamount field in the invoice header
        $model->update(['totalamount' => $totalAmount]);
    }


    public function payment(Payment $payment)
    {
        // Calculate the total amount for the given invoice
        $totalAmount = DB::table('payment_items')
            ->where('payment_id', $payment->id)
            ->sum('amount');

        // Update the totalamount field in the payment header
        $payment->update(['totalamount' => $totalAmount]);

        ///get the corresponding invoice, expense or deposit
        $refinvoice =$payment->model;
            ////Update Status /////
        if ($totalAmount < $refinvoice->totalamount) {
            // Partially paid
            $refinvoice->update(['status' => 'partially_paid']);
        } elseif ($totalAmount > $refinvoice->totalamount) {
            // Overpaid
            $refinvoice->update(['status' => 'over_paid']);
        } elseif ($totalAmount == $refinvoice->totalamount) {
            // Fully paid
            $refinvoice->update(['status' => 'paid']);
        }
    }

    public function ticket(Ticket $ticket)
    {
        // Calculate the total amount for the given invoice
        $totalAmount = DB::table('workorder_expenses')
            ->where('ticket_id', $ticket->id)
            ->sum('amount');

        // Update the totalamount field in the invoice header
        $ticket->update(['totalamount' => $totalAmount]);
    }
}
