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

    public function generateExpense(Model $model = null, User $user = null, $validatedData = null, $formreferenceno = null,$request =null)
    {


        $ExpenseData = $this->getExpenseHeaderData($model, $user, $validatedData, $formreferenceno);

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
        $this->uploadMediaAction->handle($model, 'receipt', 'Receipt', $request);

        //4. Update Total Amount in Payment Header
            $this->calculateTotalAmountAction->total($expense);

        //5. Create Transactions for ledger


            $this->recordTransactionAction->transaction($expense);

        return $expense;
    }




    //////2. GET DATA FOR VOUCHER HEADER DATA
    private function getExpenseHeaderData($model, $user, $validatedData, $formreferenceno)
    {
        if (!is_null($validatedData)) {
            return [
                'property_id' => $validatedData['property_id'],
                'unit_id' => $validatedData['unit_id'] ?? null,
                'unitcharge_id' => null,
                'model_type' => $validatedData['model_type'],
                'model_id' => $validatedData['model_id'],
                'referenceno' => $formreferenceno,
                'name' => $validatedData['name'], ///Generated from securityexpense
                'totalamount' => null,
                'status' => 'unpaid',
                'duedate' => $validatedData['duedate'] ?? null,
            ];
        } else {
            $doc = 'EXP-';
            $propertynumber = 'P' . str_pad($model->property_id, 2, '0', STR_PAD_LEFT);
            $unitnumber = $model->unit_id ?? 'N';
            $date = Carbon::now()->format('ymd');
            $referenceno = $doc . $propertynumber . $unitnumber . '-' . $date;
            $usermodelname = get_class($user);

            return [
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'unitcharge_id' => $model->unit_id ?? null,
                'model_type' => 'App\\Models\\Vendor',
                'model_id' => $user->id,
                'referenceno' => $referenceno,
                'name' => $model->charge_name, ///Generated from securityexpense
                'totalamount' => null,
                'status' => 'unpaid',
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
            $items = $model->getItems();

            // Create expense items from the model items
            foreach ($items as $item) {
                $expenseItem = new ExpenseItems([
                    'expense_id' => $expense->id,
                    'unitcharge_id' => $model->unitcharge_id ?? null,
                    'chartofaccount_id' => $item->chartofaccounts_id,
                    'description' => $item->charge_name,
                    'amount' => $item->amount,
                ]);
                $expenseItem->save();
            }
        }
    }




}
