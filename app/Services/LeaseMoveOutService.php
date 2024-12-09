<?php

// app/Services/LeaseMoveOutService.php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeaseItem;
use Illuminate\Http\Request;
use App\Actions\CalculateInvoiceTotalAmountAction;
use Carbon\Carbon;

class LeaseMoveOutService
{
    private $calculateTotalAmountAction;

    public function __construct(CalculateInvoiceTotalAmountAction $calculateTotalAmountAction) {
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
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
    public function propertyCondition(Request $request, Lease $lease)
    {
       
        // Retrieve the inputs from the request
            // Potential fixes to try:
        $leaseItemIds = $request->input('default_item_id', []);
        $conditions = $request->input('condition', []);
        $costs = $request->input('cost', []);


        // Loop through each lease item and update
        foreach ($leaseItemIds as $index => $leaseItemId) {
            $leaseItem = LeaseItem::where('lease_id',$lease->id)
                ->where('default_item_id',$leaseItemId)
                ->first();
            
            if ($leaseItem) {
                $leaseItem->update([
                    'condition' => $conditions[$index],
                    'cost' => $costs[$index],
                ]);
            }
        }

        // Optionally, add any logic here like returning success message or redirecting
        return redirect()->route('lease.moveout', ['id' => $lease->id, 'active_tab' => 2])
            ->with('status', 'Property items condition updated successfully.');
    }
    public function completeMoveOut(Request $request, Lease $lease)
    {
        // 1. Terminate the lease
        $lease->update(['status' => Lease::STATUS_TERMINATED,'enddate' => Carbon::now()]);

        // 2. Calculate the total repair costs
        $totalRepairCosts = LeaseItem::where('lease_id', $lease->id)->sum('cost');

        // 3. Retrieve the associated deposit
        $deposit = Deposit::where('unit_id', $lease->unit_id)
            ->where('model_id', $lease->user_id)
            ->first();
            if ($deposit) {
            // 4. Update deposit with repair costs
                $this->updateDepositItemsWithRepair($deposit, $request->all(), $totalRepairCosts);
            }
             //5. Update Total Amount in Payment Header
        $this->calculateTotalAmountAction->total($deposit);
        
        return redirect()->route('lease.index')
            ->with('status', 'Tenant lease move out successfull');
    }
    private function updateDepositItemsWithRepair($deposit, $validatedData, $repairCosts)
    {
        // Get all current deposit items
        $currentItems = $deposit->getItems()->get();

        // Keep track of updated and new item IDs
        $updatedItemIds = [];

        // Process incoming validated deposit items
    
        // Add Repair Costs as a negative item
        if ($repairCosts > 0) {
            $repairItemData = [
                'chartofaccount_id' => 10, // Fetch the correct chart account
                'description' => 'Property Repair Costs',
                'amount' => -1 * $repairCosts, // Negative value for repair costs
            ];

            // Check if a repair costs item already exists
            $existingRepairItem = $currentItems->first(function ($item) {
                return $item->description === 'Property Repair Costs';
            });

            if ($existingRepairItem) {
                $existingRepairItem->update($repairItemData);
                $updatedItemIds[] = $existingRepairItem->id;
            } else {
                // Create a new repair cost item
                $newRepairItem = $deposit->getItems()->create($repairItemData);
                $updatedItemIds[] = $newRepairItem->id;
            }
        }

        // Refresh the deposit relationship
        $deposit->load('getItems');
    }
}
