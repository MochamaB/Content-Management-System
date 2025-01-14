<?php

// app/Services/expenseService.php

namespace App\Services;


use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Actions\RecordTransactionAction;
use App\Models\Expense;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Models\ExpenseItems;
use App\Models\Property;
use App\Models\User;
use App\Actions\UploadMediaAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseService
{
    private $calculateTotalAmountAction;
    private $recordTransactionAction;
    protected $uploadMediaAction;


    public function __construct(
        CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        RecordTransactionAction $recordTransactionAction,
        UploadMediaAction $uploadMediaAction
    ) {
        $this->recordTransactionAction = $recordTransactionAction;
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
        $this->uploadMediaAction = $uploadMediaAction;
    }

    public function generateExpense($model = null, User $user = null, $validatedData = null,$request =null)
    {


        $ExpenseData = $this->getExpenseHeaderData($model, $user, $validatedData);

        //1. Create Expense Header Data
       
        $expense = $this->createExpense($ExpenseData);

        //2. Create expense items
            $this->createExpenseItems($expense, $model, $validatedData);

            
        /////3. UPLOAD RECEIPT ///////////////////
        
        if($expense->unit_id === null){
            $model = Property::find($expense->property_id);
        }else{
            $model = Unit::find($expense->unit_id);
        }
        if($request){
        $this->uploadMediaAction->handle($model, 'receipt', 'Receipt', $request);
        }

        //4. Update Total Amount in Payment Header
            $this->calculateTotalAmountAction->total($expense);

        //5. Create Transactions for ledger


            $this->recordTransactionAction->transaction($expense);
            

        return $expense;
    }




    //////2. GET DATA FOR VOUCHER HEADER DATA
    private function getExpenseHeaderData($model, $user, $validatedData)
    {
        if (!is_null($validatedData)) {
            return [
                'property_id' => $validatedData['property_id'],
                'unit_id' => $validatedData['unit_id'] ?? null,
                'unitcharge_id' => null,
                'model_type' => $validatedData['model_type'],
                'model_id' => $validatedData['model_id'],
                'name' => $validatedData['name'], ///Generated from securityexpense
                'totalamount' => null,
             //   'status' => 'unpaid',
                'duedate' => $validatedData['duedate'] ?? null,
            ];
        } else {
            $doc = 'EXP-';
            $propertynumber = 'P' . str_pad($model->property_id, 2, '0', STR_PAD_LEFT);
            $unitnumber = $model->unit_id ?? 'N';
            $date = Carbon::now()->format('ymd');
            $referenceno = $doc . $propertynumber . $unitnumber . '-' . $date;
            $modelname = get_class($model);

            return [
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id ?? 0,
                'unitcharge_id' => $model->unit_id ?? null,
                'model_type' => $modelname,
                'model_id' => $model->id,
                'name' => $model->charge_name ?? $model->category, ///Generated from securityexpense
                'totalamount' => null,
            //    'status' => 'unpaid',
                'duedate' => null,
            ];
        }
    }

    private function createExpense($data)
    {
        return Expense::create($data);
    }

    private function createExpenseItems($expense, $model, $validatedData)
    {

        if (!is_null($validatedData)) {
                // Create expense items from the form input names
                foreach ($validatedData['chartofaccount_id'] as $index => $item) {
                    $expenseItem = new ExpenseItems([
                        'expense_id' => $expense->id,
                        'unitcharge_id' => null,
                        'chartofaccount_id' => $item ?? null,
                        'description' => $validatedData['description'][$index] ?? null,
                        'amount' => $validatedData['amount'][$index] ?? null,
                    ]);
                    $expenseItem->save();
                }
            
        } else {
            // Get items from the model (e.g., invoices)
            $items = $model->getItems;

            // Create expense items from the model items
            foreach ($items as $item) {
                $expenseItem = new ExpenseItems([
                    'expense_id' => $expense->id,
                    'unitcharge_id' => $model->unitcharge_id ?? null,
                    'chartofaccount_id' => $item->chartofaccounts_id ?? $model->chartofaccount_id,
                    'description' => $item->charge_name ?? $item->item,
                    'amount' => $item->amount,
                ]);
                $expenseItem->save();
            }
        }
    }

    public function updateExpense($expenseId, $validatedData, $user)
    {
        DB::beginTransaction();

        try {
            $expense = Expense::findOrFail($expenseId);
            

            // Update deposit header
            $headerData = $this->getExpenseHeaderData(null, $user, $validatedData);
            $expense->update($headerData);

            // Update or create deposit items
            $this->updateExpenseItems($expense, $validatedData);

            //3. Update Total Amount in Payment Header
            $this->calculateTotalAmountAction->total($expense);

            // Update associated transaction
            $this->recordTransactionAction->updateTransaction($expense);


            DB::commit();
            return $expense;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating expense: " . $e->getMessage());
            throw $e;
        }
    }

    private function updateExpenseItems($expense, $validatedData)
    {
        // Get all current deposit items
        $currentItems = $expense->getItems()->get();
        
        // Keep track of updated and new item IDs
        $updatedItemIds = [];
    
        foreach ($validatedData['chartofaccount_id'] as $index => $chartOfAccountId) {
            $itemData = [
                'chartofaccount_id' => $chartOfAccountId,
                'description' => $validatedData['description'][$index] ?? null,
                'amount' => $validatedData['amount'][$index] ?? null,
            ];
    
            // Check if there's a matching existing item
            $existingItem = $currentItems->first(function ($item) use ($chartOfAccountId, $itemData) {
                return $item->chartofaccount_id == $chartOfAccountId &&
                       $item->description == $itemData['description'];
            });
    
            if ($existingItem) {
                // Update existing item
                $existingItem->update($itemData);
                $updatedItemIds[] = $existingItem->id;
            } else {
                // Create new item
                $newItem = $expense->getItems()->create($itemData);
                $updatedItemIds[] = $newItem->id;
            }
        }
    
        // Delete items that weren't updated or created
        $expense->getItems()->whereNotIn('id', $updatedItemIds)->delete();
    
        // Refresh the deposit relationship
        $expense->load('getItems');
    }



}
