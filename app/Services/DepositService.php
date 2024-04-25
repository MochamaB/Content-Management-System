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

        //2. Create Transactions for ledger
        ///check if the chartaccount is either a asset,Liability, income or expense
        $accounttype = $model->chartofaccounts->account_type;

        $this->recordTransactionAction->deposit($deposit, $model);

        //  $this->recordTransactionAction->voucherCharges($Deposit, $model);

        return $deposit;
    }




    //////2. GET DATA FOR VOUCHER HEADER DATA
    private function getDepositHeaderData($model, $user, $validatedData, $formreferenceno)
    {
        $doc = 'DEP-';
        $propertynumber = 'P' . str_pad($model->property_id, 2, '0', STR_PAD_LEFT);
        $unitnumber = $model->unit_id ?? 'N';
        $date = Carbon::now()->format('ymd');
        $referenceno = $doc . $propertynumber . $unitnumber . '-' . $date;
        $usermodelname = get_class($user);

        return [
            'property_id' => $model->property_id ?? $validatedData['property_id'],
            'unit_id' => $model->unit_id ?? $validatedData['unit_id'],
            'unitcharge_id' => $model->unit_id ?? null,
            'model_type' => $usermodelname ?? $validatedData['model_type'],
            'model_id' => $user->id ?? $validatedData['model_id'],
            'referenceno' => $referenceno ?? $formreferenceno,
            'charge_name' => $model->charge_name ?? $validatedData['charge_name'], ///Generated from securitydeposit
            'totalamount' => null,
            'status' => 'unpaid',
            'duedate' => $validatedData['duedate'] ?? null,
        ];
    }

    private function createDeposit($data)
    {
        return Deposit::create($data);
    }

    private function createDepositItems($deposit, $model, $validatedData)
    {

        if (!is_null($validatedData)) {
            // Check if the validated form data has input chartofaccount with square brackets
            if (array_key_exists('chartofaccount_id[]', $validatedData)) {
                // Create deposit items from the form input names
                foreach ($validatedData['chartofaccount_id[]'] as $index => $item) {
                    $depositItem = new DepositItems([
                        'deposit_id' => $deposit->id,
                        'unitcharge_id' => null,
                        'chartofaccount_id' => $validatedData['chartofaccount_id'][$index] ?? null,
                        'charge_name' => $validatedData['charge_name'][$index] ?? null,
                        'description' => '',
                        'amount' => $validatedData['chartofaccount_id'][$index] ?? null,
                    ]);
                    $depositItem->save();
                }
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
                    'charge_name' => $item->charge_name,
                    'description' => $item->description ?? '',
                    'amount' => $item->rate,

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
