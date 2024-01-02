<?php

// app/Actions/CalculateInvoiceTotalAmountAction.php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Paymentvoucher;
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

    public function paymentVoucher(PaymentVoucher $paymentVoucher)
    {
        // Calculate the total amount for the given invoice
        $totalAmount = DB::table('paymentvoucher_items')
            ->where('paymentvoucher_id', $paymentVoucher->id)
            ->sum('amount');

        // Update the totalamount field in the invoice header
        $paymentVoucher->update(['totalamount' => $totalAmount]);

    }
    public function payment(Payment $payment)
    {
        // Calculate the total amount for the given invoice
        $totalAmount = DB::table('payment_items')
            ->where('payment_id', $payment->id)
            ->sum('amount');

        // Update the totalamount field in the invoice header
        $payment->update(['totalamount' => $totalAmount]);

    }
}
