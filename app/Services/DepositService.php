<?php

// app/Services/DepositService.php

namespace App\Services;


use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Actions\RecordTransactionAction;
use App\Models\Deposit;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Models\DepositItems;
use App\Models\User;

class DepositService
{
    private $calculateTotalAmountAction;
    private $recordTransactionAction;


    public function __construct(
        CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        RecordTransactionAction $recordTransactionAction
    ) {
        $this->recordTransactionAction = $recordTransactionAction;
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
    }

    public function generateDeposit(Model $model = null, User $user = null, $validatedData = null, $formreferenceno = null)
    {


        $DepositData = $this->getDepositHeaderData($model, $user, $validatedData, $formreferenceno);

        //1. Create Deposit Header Data
       
        $deposit = $this->createDeposit($DepositData);

        //2. Create Deposit items
            $this->createDepositItems($deposit, $model, $validatedData);

        //3. Update Total Amount in Payment Header
            $this->calculateTotalAmountAction->total($deposit);

        //4. Create Transactions for ledger


            $this->recordTransactionAction->transaction($deposit, $model);

        //  $this->recordTransactionAction->voucherCharges($Deposit, $model);

        return $deposit;
    }




    //////2. GET DATA FOR VOUCHER HEADER DATA
    private function getDepositHeaderData($model, $user, $validatedData, $formreferenceno)
    {
        if (!is_null($validatedData)) {
            return [
                'property_id' => $validatedData['property_id'],
                'unit_id' => $validatedData['unit_id'],
                'unitcharge_id' => null,
                'model_type' => $validatedData['model_type'],
                'model_id' => $validatedData['model_id'],
                'referenceno' => $formreferenceno,
                'name' => $validatedData['name'], ///Generated from securitydeposit
                'totalamount' => null,
                'status' => 'unpaid',
                'duedate' => $validatedData['duedate'] ?? null,
            ];
        } else {
            $doc = 'DEP-';
            $propertynumber = 'P' . str_pad($model->property_id, 2, '0', STR_PAD_LEFT);
            $unitnumber = $model->unit_id ?? 'N';
            $date = Carbon::now()->format('ymd');
            $referenceno = $doc . $propertynumber . $unitnumber . '-' . $date;
            $usermodelname = get_class($user);

            return [
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'unitcharge_id' => $model->unit_id ?? null,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
                'referenceno' => $referenceno,
                'name' => $model->charge_name, ///Generated from securitydeposit
                'totalamount' => null,
                'status' => 'unpaid',
                'duedate' => null,
            ];
        }
    }

    private function createDeposit($data)
    {
        return Deposit::create($data);
    }

    private function createDepositItems($deposit, $model, $validatedData)
    {

        if (!is_null($validatedData)) {
                // Create deposit items from the form input names
                foreach ($validatedData['chartofaccount_id'] as $index => $item) {
                    $depositItem = new DepositItems([
                        'deposit_id' => $deposit->id,
                        'unitcharge_id' => null,
                        'chartofaccount_id' => $item ?? null,
                        'description' => $validatedData['description'][$index] ?? null,
                        'amount' => $validatedData['amount'][$index] ?? null,
                    ]);
                    $depositItem->save();
                }
            
        } else {
            // Get items from the model (e.g., invoices)
            $items = $model->getItems();

            // Create deposit items from the model items
            foreach ($items as $item) {
                $depositItem = new DepositItems([
                    'deposit_id' => $deposit->id,
                    'unitcharge_id' => $model->unitcharge_id ?? null,
                    'chartofaccount_id' => $item->chartofaccounts_id,
                    'description' => $item->charge_name,
                    'amount' => $item->amount,
                ]);
                $depositItem->save();
            }
        }
    }




    ////// PAyment voucher from the createmethod
    public function generateDepositForm($validatedData, $request, $referenceno)
    {
        $deposit = new Deposit();
        $deposit->fill($validatedData);
        $deposit->referenceno = $referenceno;
        $deposit->save();

        //4. Create Transactions for ledger
        $this->recordTransactionAction->transaction($deposit);
    }
}
