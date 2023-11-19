<?php

// app/Actions/CalculateInvoiceTotalAmountAction.php

namespace App\Actions;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

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
}