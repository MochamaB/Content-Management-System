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

    public function generatePaymentVoucher(Model $model)
    {
                $modelname = class_basename($model);
        
                $paymentVoucherData = $this->getPaymentVoucherHeaderData($model,$modelname);

                //1. Create Payment Voucher Header Data
                $paymentVoucher = $this->createPaymentVoucher($paymentVoucherData);

                //2. Create PaymentVoucher items
                 $this->createPaymentVoucherItems($paymentVoucher, $model);

                //3. Update Total Amount in PaymentVoucher Header
                $this->calculateTotalAmountAction->paymentVoucher($paymentVoucher);
//

                //4. Create Transactions for ledger
                $this->recordTransactionAction->securitydeposit($paymentVoucher, $model);
        
                return $paymentVoucher;
           
    }

 
 

    //////2. GET DATA FOR INVOICE HEADER DATA
    private function getPaymentVoucherHeaderData($model,string $modelname)
    {
        $today = Carbon::now();
        $date = $today->format('ym');
        $unitnumber = Unit::where('id', $model->unit_id)->first();

        return [
            'property_id' => $model->property_id,
            'unit_id' => $model->unit_id,
            'model_type' => $modelname,
            'model_id' => $model->id,
            'referenceno' => 'PV '. $date . $unitnumber->unit_number,
            'voucher_type' => $model->charge_name, ///Generated from securitydeposit
            'totalamount' => null,
            'status' => 'Payable',
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
}
