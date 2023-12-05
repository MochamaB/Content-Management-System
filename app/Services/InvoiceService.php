<?php

// app/Services/InvoiceService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\User;
use App\Models\Lease;
use App\Models\MeterReading;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Actions\UpdateDueDateAction;
use App\Actions\UpdateNextDateAction;
use App\Actions\RecordTransactionAction;
use App\Notifications\InvoiceGeneratedNotification;



class InvoiceService
{
    private $calculateTotalAmountAction;
    private $updateDueDateAction;
    private $updateNextDateAction;
    private $recordTransactionAction;
   

    public function __construct(CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        UpdateNextDateAction $updateNextDateAction,
        UpdateDueDateAction $updateDueDateAction,
        RecordTransactionAction $recordTransactionAction)
    {
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
        $this->updateNextDateAction = $updateNextDateAction;
        $this->updateDueDateAction = $updateDueDateAction;
        $this->recordTransactionAction = $recordTransactionAction;
       
    }


    public function generateInvoice(Unitcharge $unitcharge)
    {
        $invoiceExists = $this->invoiceExists($unitcharge);
        // Check if an invoice already exists for the given month, unit, and charge name
        if ($invoiceExists) {
            // Invoice already exists, skip and continue to the next Unitcharge record
            return $invoiceExists;
        }
        
        ///Queries unitcharge-> next date to check if the charge needs to be invoiced that month.
           if ($this->isTimeToGenerateInvoice($unitcharge)) {
           // dd($unitcharge);

                $invoiceData = $this->getInvoiceHeaderData($unitcharge);

                //1. Create Invoice Header Data
                $invoice = $this->createInvoice($invoiceData);

                //2. Create invoice items
                $this->createInvoiceItems($invoice, $unitcharge);

                //3. Update Total Amount in Invoice Header
                $this->calculateTotalAmountAction->handle($invoice);

                //4. Update Next Date in the Unitcharge
                $this->updateNextDateAction->invoicenextdate($unitcharge);

                //5. Update Due Date In the newly generated invoice.
                $this->updateDueDateAction->handle($invoice);

                //6. Create Transactions for ledger
                $this->recordTransactionAction->invoiceCharges($invoice);

                //7. Send Email/Notification to the Tenant containing the invoice.
                $user = $invoice->model;
                $user->notify(new InvoiceGeneratedNotification($invoice,$user));

        
                return $invoice;
           }
    }

    ///////2. CHECK IF ITS TIME TO GENERATE INVOICE
    private function isTimeToGenerateInvoice($unitcharge)
    {
        $nextDate = Carbon::parse($unitcharge->nextdate);
        return Carbon::now()->isSameMonth($nextDate);
    }

    ///3. CHECK IF INVOICE EXISTS
    private function invoiceExists(Unitcharge $unitcharge)
    {
        $today = Carbon::now();
        $invoicenodate = $today->format('ym');
        $unitnumber = Unit::where('id', $unitcharge->unit_id)->value('unit_number');
        $referenceno = $invoicenodate . $unitnumber;
        
        return Invoice::where('referenceno', $referenceno)
        ->where('invoice_type', $unitcharge->charge_name)
        ->first();
    }

    //////4. GET DATA FOR INVOICE HEADER DATA
    private function getInvoiceHeaderData($unitcharge)
    {
        $today = Carbon::now();
        $invoicenodate = $today->format('ym');
        $unitnumber = Unit::where('id', $unitcharge->unit_id)->first();
        $userId = Lease::where('unit_id', $unitcharge->unit_id)->first();
        $user = User::class;

        return [
            'property_id' => $unitcharge->property_id,
            'unit_id' => $unitcharge->unit_id,
            'model_type'=>$user, ///This has plymorphism because an invoice can also be sent to a vendor.
            'model_id' => $userId->user_id,
            'referenceno' => $invoicenodate .$unitnumber->unit_number,
            'invoice_type' => $unitcharge->charge_name,
            'totalamount' => null,
            'status' => 'unpaid',
            'duedate' => null,
        ];
    }

    private function createInvoice($data)
    {
        return Invoice::create($data);
    }

    

    private function createInvoiceItems($invoice, $unitcharge)
    {
        if ($unitcharge->charge_type === 'units') {
            $nextdateFormatted = Carbon::parse($unitcharge->nextdate)->format('Y-m-d');
            $updatedFormatted = Carbon::parse($unitcharge->updated_at ?? Carbon::now())->format('Y-m-d');
          
            $amount = 0.00; 
            $meterReadings = MeterReading::where('unit_id', $unitcharge->unit_id)
            ->where('unitcharge_id',$unitcharge->id)
            ->where('startdate', '>=', $updatedFormatted) // Check readings after updated_at
            ->where('startdate', '<=', $nextdateFormatted) // Check readings before or equal to nextdate
            ->get();
              
            foreach ($meterReadings as $reading) {
                // Calculate the amount based on meter readings and assign it to $amount
                $amount = $reading->amount;
                    }
                } else {
                    // If charge_type is not 'units', use the unitcharge rate as the amount
                    $amount = $unitcharge->rate;
                }
        // Create invoice items
        InvoiceItems::create([
            'invoice_id' => $invoice->id,
            'unitcharge_id' => $unitcharge->id,
            'chartofaccount_id' => $unitcharge->chartofaccounts_id,
            'charge_name' => $unitcharge->charge_name,
            'description' => '',
            'amount' => $amount,
        ]);

       // Create invoice items for child charges
            $childcharges = Unitcharge::where('parent_id', $unitcharge->id)->get();
            if ($childcharges->count() > 0) {
            
            foreach ($childcharges as $childcharge) {
                InvoiceItems::create([
                    'invoice_id' => $invoice->id,
                    'unitcharge_id' => $childcharge->id,
                    'chartofaccount_id' => $childcharge->chartofaccounts_id,
                    'charge_name' => $childcharge->charge_name,
                    'description' => '',
                    'amount' => $childcharge->rate,
                ]);
            }
        }
        
    }
}
