<?php

// app/Services/LeaseMoveOutService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeaseItem;
use Illuminate\Http\Request;

class LeaseMoveOutService
{
    public function __construct() {}
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
    public function propertyCondition(Request $request, Lease $lease)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'item' => 'required|array',
            'item.*' => 'required|string|max:255',
            'condition' => 'required|array',
            'condition.*' => 'required|in:Good,Needs Repair,Damaged,Needs Replacement',
            'cost' => 'required|array',
            'cost.*' => 'required|numeric|min:0',
        ]);

        // Save each item to the database
        $totalCost = 0;
        foreach ($validatedData['item'] as $index => $itemDescription) {
            $condition = $validatedData['condition'][$index];
            $cost = $validatedData['cost'][$index];
            $totalCost += $cost;

            LeaseItem::create([
                'lease_id' => $lease->id,
                'item_description' => $itemDescription,
                'condition' => $condition,
                'cost' => $cost,
            ]);
        }



        // Redirect to the next step
        return redirect()->route('lease.moveout', ['id' => $lease->id, 'active_tab' => 2])
            ->with('status', 'Property condition recorded successfully.');
    }
}
