<?php

// app/Actions/UpdateDueDateAction.php

namespace App\Actions;

use App\Models\Chartofaccount;
use App\Models\Expense;
use App\Models\ExpenseItems;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Tax;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Model;

class CalculateTaxAction
{
    use AsAction;

    public function calculateTax(Model $model)
    {
        $applicableTaxes = Tax::findApplicableTaxes($model);
       // Get the old tax amount before recalculating the new one
        $oldTax = $model->taxamount ?? 0;
        $totalTax = 0;
        foreach ($applicableTaxes  as $tax) {
            $totalTax += $model->totalamount * ($tax->rate / 100);
        }
        // Update the due_date column in the invoices table
        $model->update(['taxamount' => $totalTax]);
        // Create Tax Expense
       // Handle the Expense creation/update and return the Expense
       return $this->handleTaxExpense($model, $oldTax, $totalTax, $applicableTaxes);
    }
    

    public function handleTaxExpense($model, $oldTax, $totalTax, $applicableTaxes)
    {
        // Assuming $model is the taxable entity (e.g., Payment or Invoice)
        $property = $model->property;
        $currentMonth = Carbon::now()->startOfMonth();
        $expenses = collect();
        foreach ($applicableTaxes as $tax) {
            $taxAmount = $model->totalamount * ($tax->rate / 100);

        // Find or create the Expense for the current month
        $expense = Expense::firstOrCreate(
            [
                'property_id' => $property->id,
                'unit_id' => null,
                'model_type' => get_class($tax),
                'model_id' => $tax->id,
                'name' => $tax->name.' - '.$property->property_name.' - '. $currentMonth->format('F Y'),
                'status' => 'pending',
                'duedate' => $currentMonth->endOfMonth(),
            ]
        );
         // Subtract the old tax amount (if it's not null or zero)
         if ($oldTax > 0) {
            $expense->totalamount -= $oldTax;
        }
         // Update the total amount of the Expense by adding the new tax amount
         $expense->totalamount += $taxAmount;
         $expense->save();

        
        // Find the appropriate Chart of Account for taxes
        $taxCoA = Chartofaccount::where('account_name', 'Taxes')->first();

        if (!$taxCoA) {
            throw new \Exception('Chart of Account for Taxes not found');
        }

        // Create or update the ExpenseItem
        // Check if the ExpenseItem already exists
        $expenseItem = ExpenseItems::where('expense_id', $expense->id)
            ->where('chartofaccount_id', $taxCoA->id)
            ->first();
            if ($oldTax > 0) {
                $expenseItem->amount -= $oldTax;
            }

        if ($expenseItem) {
            // If it exists, increment the amount
            $expenseItem->amount += $taxAmount;
        } else {
            // If it doesn't exist, create a new one
            $expenseItem = ExpenseItems::create([
                'expense_id' => $expense->id,
                'chartofaccount_id' => $taxCoA->id,
                'description' => $tax->name,
                'amount' => $taxAmount,
            ]);
        }

        $expenseItem->save();


       $expenses->push($expense);
    }

        return $expense;
    }



   

}