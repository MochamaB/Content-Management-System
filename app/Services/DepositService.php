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
use App\Models\User;

class DepositService
{
    private $calculateTotalAmountAction;
    private $recordTransactionAction;
   

    public function __construct(CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        RecordTransactionAction $recordTransactionAction)
    {
        $this->recordTransactionAction = $recordTransactionAction;
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
       
    }

    public function generateDeposit(Model $model, User $user =null)
    {
                $modelname = get_class($model);
        
                $DepositData = $this->getDepositHeaderData($model,$modelname,$user);

                //1. Create Payment Voucher Header Data
                $Deposit = $this->createDeposit($DepositData);

    

                //4. Create Transactions for ledger
                ///check if the chartaccount is either a asset,Liability, income or expense
                $accounttype = $model->chartofaccounts->account_type;
               
                $this->recordTransactionAction->deposit($Deposit, $model);
                
              //  $this->recordTransactionAction->voucherCharges($Deposit, $model);
        
                return $Deposit;
           
    }

 
 

    //////2. GET DATA FOR VOUCHER HEADER DATA
    private function getDepositHeaderData($model,string $modelname,$user)
    {
        $today = Carbon::now();
        $date = $today->format('ym');
        $unitnumber = Unit::where('id', $model->unit_id)->first();
        $usermodelname = get_class($user);

        return [
            'property_id' => $model->property_id,
            'unit_id' => $model->unit_id,
            'chartofaccount_id' =>'',
            'model_type' => $usermodelname,
            'model_id' => $user->id,
            'referenceno' => 'PV '. $date . $unitnumber->unit_number,
            'name' => $model->charge_name, ///Generated from securitydeposit
            'totalamount' => null,
            'status' => 'unpaid',
            'duedate' => null,
        ];
    }

    private function createDeposit($data)
    {
        return Deposit::create($data);
    }

    



    ////// PAyment voucher from the createmethod
    public function generateDepositForm($validatedData ,$request,$referenceno)
    {
        $deposit = new Deposit();
        $deposit->fill($validatedData);
        $deposit->referenceno = $referenceno;
        $deposit->save();

      //4. Create Transactions for ledger
        $this->recordTransactionAction->deposit($deposit);
    }
}
