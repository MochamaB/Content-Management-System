<?php

// app/Services/LeaseMoveOutService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Lease;
use Illuminate\Http\Request;

class LeaseMoveOutService
{
    public function __construct(){

    }
    public function terminateLease() {}

    public function checkOutstandingBalances(Lease $lease)
    {
        // Query all unpaid or partially paid invoices for the lease
        $outstandingInvoices = Invoice::where('unit_id', $lease->unit_id)
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIALLY_PAID])
            ->get();

        // Calculate the total outstanding amount
        $totalOutstanding = $outstandingInvoices->sum(function ($invoice) {
            return $invoice->totalamount - $invoice->payments->sum('totalamount');
        });

        // Return the financial data
        return [
            'outstandingInvoices' => $outstandingInvoices,
            'totalOutstanding' => $totalOutstanding,
        ];
    }
    public function financeCheck(Request $request, Lease $lease)
    {
        // Check if the outstanding amount is zero
        $outstanding = $request->input('outstanding', 0);
        if ($outstanding > 0) {
            return redirect()->back()->with('statuserror', 'Outstanding dues must be cleared before proceeding.');
        }

        // If no issues, proceed to the next tab
        return redirect()->route('lease.moveout', ['id' => $lease->id, 'active_tab' => 1])
        ->with('status', 'Finance check complete.');
    }
            

}