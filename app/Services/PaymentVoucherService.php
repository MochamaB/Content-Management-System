<?php

// app/Services/PaymentVoucherService.php

namespace App\Services;


use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Actions\RecordTransactionAction;
use App\Models\Paymentvoucher;
use App\Models\PaymentVoucherItems;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Models\User;

class PaymentVoucherService
{
    private $calculateTotalAmountAction;
    private $recordTransactionAction;
   

    public function __construct(CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        RecordTransactionAction $recordTransactionAction)
    {
        $this->recordTransactionAction = $recordTransactionAction;
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
       
    }

    public function generatePaymentVoucher(Model $model, User $user)
    {
                $modelname = get_class($model);
        
                $paymentVoucherData = $this->getPaymentVoucherHeaderData($model,$modelname,$user);

                //1. Create Payment Voucher Header Data
                $paymentVoucher = $this->createPaymentVoucher($paymentVoucherData);

                //2. Create PaymentVoucher items
                 $this->createPaymentVoucherItems($paymentVoucher, $model);

                //3. Update Total Amount in PaymentVoucher Header
                $this->calculateTotalAmountAction->paymentVoucher($paymentVoucher);
//

                //4. Create Transactions for ledger
                ///check if the chartaccount is either a asset,Liability, income or expense
                $accounttype = $model->chartofaccounts->account_type;
                if($accounttype === "Income"){
                    $this->recordTransactionAction->voucherChargesIncome($paymentVoucher, $model);
                }else{
                    $this->recordTransactionAction->voucherCharges($paymentVoucher, $model);
                }
              //  $this->recordTransactionAction->voucherCharges($paymentVoucher, $model);
        
                return $paymentVoucher;
           
    }

 
 

    //////2. GET DATA FOR VOUCHER HEADER DATA
    private function getPaymentVoucherHeaderData($model,string $modelname,$user)
    {
        $today = Carbon::now();
        $date = $today->format('ym');
        $unitnumber = Unit::where('id', $model->unit_id)->first();
        $usermodelname = get_class($user);

        return [
            'property_id' => $model->property_id,
            'unit_id' => $model->unit_id,
            'payable_type' => $modelname,
            'payable_id' => $model->id,
            'model_type' => $usermodelname,
            'model_id' => $user->id,
            'referenceno' => 'PV '. $date . $unitnumber->unit_number,
            'name' => $model->charge_name, ///Generated from securitydeposit
            'totalamount' => null,
            'status' => 'unpaid',
            'duedate' => null,
        ];
    }

    private function createPaymentVoucher($data)
    {
        return Paymentvoucher::create($data);
    }

    

    private function createPaymentVoucherItems($paymentVoucher, $model)
    {
        // Create invoice items
        PaymentVoucherItems::create([
            'paymentvoucher_id' => $paymentVoucher->id,
            'unitcharge_id' => $model->id,
            'chartofaccount_id' => $model->chartofaccounts_id,
            'charge_name' => $model->charge_name,
            'description' => $model->description,
            'amount' => $model->rate,
        ]);  
        
    }

    ////// PAyment voucher from the createmethod
    public function generatePaymentVoucherForm($validatedData ,$request,$referenceno)
    {
        $paymentvoucher = new Paymentvoucher();
        $paymentvoucher->fill($validatedData);
        $paymentvoucher->referenceno = $referenceno;
        $paymentvoucher->save();

        $paymentVoucherItems = [];
        if (!empty($paymentvoucher->id)) {
           foreach ($request->input('chartofaccount_id') as $index => $item) { 
               $paymentVoucherItem = [
                   'paymentvoucher_id' => $paymentvoucher->id,
                   'unitcharge_id' => null,
                   'chartofaccount_id' => $request->input("chartofaccount_id.{$index}"),
                   'charge_name' => $request->input("charge_name.{$index}"),
                   'description' => null,
                   'payment_method' => null,
                   'amount' => $request->input("amount.{$index}"),
                   'created_at' => now(),
                   'updated_at' => now(),
                   // ... Other fields ...
               ];
               $paymentVoucherItems[] = $paymentVoucherItem;
           }
       }
       PaymentVoucherItems::insert($paymentVoucherItems);

        //3. Update Total Amount in PaymentVoucher Header
        $this->calculateTotalAmountAction->paymentVoucher($paymentvoucher);
      //4. Create Transactions for ledger
        $this->recordTransactionAction->voucherCharges($paymentvoucher);
    }
}
