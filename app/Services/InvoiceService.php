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
use App\Jobs\SendInvoiceEmailJob;
use App\Models\Transaction;
use App\Notifications\InvoiceGeneratedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class InvoiceService
{
    private $calculateTotalAmountAction;
    private $updateDueDateAction;
    private $updateNextDateAction;
    private $recordTransactionAction;


    public function __construct(
        CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        UpdateNextDateAction $updateNextDateAction,
        UpdateDueDateAction $updateDueDateAction,
        RecordTransactionAction $recordTransactionAction
    ) {
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
        $this->updateNextDateAction = $updateNextDateAction;
        $this->updateDueDateAction = $updateDueDateAction;
        $this->recordTransactionAction = $recordTransactionAction;
    }
    public function getUnitCharges()
    {
        return Unitcharge::where('recurring_charge', 'Yes')
            ->where('parent_id', null)
              //   ->whereMonth('nextdate', now()->month)
            ->whereHas('unit.lease', function ($query) {
                $query->where('status', 'Active');
            })
         //   ->whereDoesntHave('invoices', function ($query) {
         //       $query->whereMonth('created_at', now()->month);
         //   })
            ->get();
    }
    public function chargesForInvoiceGeneration()
    {

        $unitcharges = $this->getUnitCharges();

        foreach ($unitcharges as $unitcharge) {
            //1. Create invoice items from invoice service app/Services/InvoiceService
            $this->generateInvoice($unitcharge);
        }
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
        //    if ($this->isTimeToGenerateInvoice($unitcharge)) {
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
        //// Child Charges
        $childcharges = Unitcharge::where('parent_id', $unitcharge->id)->get();
        if ($childcharges) {
            foreach ($childcharges as $childcharge) {
                $this->updateNextDateAction->invoicenextdate($childcharge);
            }
        }

        //5. Update Due Date In the newly generated invoice.
        $this->updateDueDateAction->handle($invoice);

        //6. Create Transactions for ledger
        $this->recordTransactionAction->transaction($invoice, $unitcharge);

        //7. Dispatch a job to send Email/Notification to the Tenant containing the invoice.

       $this->invoiceEmail($invoice);
     //   SendInvoiceEmailJob::dispatch($invoice);


        return $invoice;
    }

    ///////2. GET OPENING BALANCE OF THE INVOICE
    public function calculateOpeningBalance(Invoice $invoice)
    {
        // Get the date 6 months ago from today
        $sixMonthsAgo = now()->subMonths(6);

        // Calculate the sum of invoice amounts
        $invoiceAmount = Transaction::where('created_at', '<', $sixMonthsAgo)
            ->where('unit_id', $invoice->unit_id)
            ->where('charge_name', $invoice->type)
            ->where('transactionable_type', 'App\Models\Invoice')
            ->sum('amount');

        // Calculate the sum of payment amounts
        $paymentAmount = Transaction::where('created_at', '<', $sixMonthsAgo)
            ->where('unit_id', $invoice->unit_id)
            ->where('charge_name', $invoice->type)
            ->where('transactionable_type', 'App\Models\Payment')
            ->sum('amount');

        // Calculate the opening balance
        $openingBalance = $invoiceAmount - $paymentAmount;

        return $openingBalance;
    }


    ///3. CHECK IF INVOICE EXISTS
    private function invoiceExists(Unitcharge $unitcharge)
    {

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        return Invoice::where('unitcharge_id', $unitcharge->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->first();
    }

    //////4. GET DATA FOR INVOICE HEADER DATA
    private function getInvoiceHeaderData($unitcharge)
    {
        //$today = Carbon::now();
        $userId = Lease::where('unit_id', $unitcharge->unit_id)->first();
        $user = User::class;
        $doc = 'INV-';
        $propertynumber = 'P' . str_pad($unitcharge->property_id, 2, '0', STR_PAD_LEFT);
        $unitnumber = $unitcharge->unit->unit_number ?? 'N';
        $date = Carbon::parse($unitcharge->nextdate)->format('ymd');
        $referenceno = $doc . $propertynumber . $unitnumber . '-' . $date;

        return [
            'property_id' => $unitcharge->property_id,
            'unit_id' => $unitcharge->unit_id,
            'unitcharge_id' => $unitcharge->id,
            'model_type' => $user, ///This has plymorphism because an invoice can also be sent to a vendor.
            'model_id' => $userId->user_id,
            'referenceno' => $referenceno,
            'name' => $unitcharge->charge_name,
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
                ->where('unitcharge_id', $unitcharge->id)
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
            'description' => $unitcharge->charge_name,
            'amount' => $amount,
        ]);

        // Create invoice items for child charges
        $childcharges = Unitcharge::where('parent_id', $unitcharge->id)->get();
        if ($childcharges->count() > 0) {


            foreach ($childcharges as $childcharge) {

                if ($childcharge->charge_type === 'units') {
                    $nextdateFormatted = Carbon::parse($childcharge->nextdate)->format('Y-m-d');
                    $updatedFormatted = Carbon::parse($childcharge->updated_at ?? Carbon::now())->format('Y-m-d');

                    $amount = 0.00;
                    $meterReadings = MeterReading::where('unit_id', $childcharge->unit_id)
                        ->where('unitcharge_id', $childcharge->id)
                        ->where('startdate', '>=', $updatedFormatted) // Check readings after updated_at
                        ->where('startdate', '<=', $nextdateFormatted) // Check readings before or equal to nextdate
                        ->get();

                    foreach ($meterReadings as $reading) {
                        // Calculate the amount based on meter readings and assign it to $amount
                        $amount = $reading->amount;
                    }
                } else {
                    // If charge_type is not 'units', use the unitcharge rate as the amount
                    $amount = $childcharge->rate;
                }
                InvoiceItems::create([
                    'invoice_id' => $invoice->id,
                    'unitcharge_id' => $childcharge->id,
                    'chartofaccount_id' => $childcharge->chartofaccounts_id,
                    'description' => $childcharge->charge_name,
                    'amount' => $amount,
                ]);
            }
        }
    }

    public function invoiceEmail($invoice)
    {
        
        
        $user = $invoice->model;
        $unitchargeId = $invoice->invoiceItems->pluck('unitcharge_id')->first();
        $sixMonths = now()->subMonths(6);
        $transactions = Transaction::where('created_at', '>=', $sixMonths)
            ->where('unit_id', $invoice->unit_id)
            ->where('unitcharge_id', $unitchargeId)
            ->get();
        $groupedInvoiceItems = $transactions->groupBy('unitcharge_id');
        $openingBalance = $this->calculateOpeningBalance($invoice);

        $viewContent = View::make('email.statement', [
            'user' => $user,
            'invoice' => $invoice,
            'transactions' => $transactions,
            'groupedInvoiceItems' => $groupedInvoiceItems,
            'openingBalance' => $openingBalance
        ])->render();

        try {
            $user->notify(new InvoiceGeneratedNotification($invoice, $user, $viewContent));
        } catch (\Exception $e) {
            // Log the error or perform any necessary actions
            Log::error('Failed to send payment notification: ' . $e->getMessage());
        }

       
    }

    
}
