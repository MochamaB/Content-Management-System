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
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Transaction;
use App\Notifications\InvoiceGeneratedNotification;
use App\Notifications\InvoiceGeneratedTextNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Services\SmsService;

class InvoiceService
{
    private $calculateTotalAmountAction;
    private $updateDueDateAction;
    private $updateNextDateAction;
    private $recordTransactionAction;
    protected $smsService;


    public function __construct(
        CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        UpdateNextDateAction $updateNextDateAction,
        UpdateDueDateAction $updateDueDateAction,
        RecordTransactionAction $recordTransactionAction,
        SmsService $smsService
    ) {
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
        $this->updateNextDateAction = $updateNextDateAction;
        $this->updateDueDateAction = $updateDueDateAction;
        $this->recordTransactionAction = $recordTransactionAction;
        $this->smsService = $smsService;
    }
    public function getUnitCharges()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
    
        return Unitcharge::where('recurring_charge', 'Yes')
            ->where('parent_id', null)
            ->whereHas('unit.lease', function ($query) {
                $query->where('status', Lease::STATUS_ACTIVE);
            })
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereMonth('nextdate', now()->month)
                    ->orWhereDoesntHave('invoices', function ($subQuery) use ($startOfMonth, $endOfMonth) {
                        $subQuery->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    });
            })
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

    public function generateInvoice(Unitcharge $unitcharge, $model = null)
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
        $this->createInvoiceItems($invoice, $unitcharge, $model);

        //3. Update Total Amount in Invoice Header
        $this->calculateTotalAmountAction->handle($invoice);

        //4. Update Next nad Updated Date in the Unitcharge
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
       

        return [
            'property_id' => $unitcharge->property_id,
            'unit_id' => $unitcharge->unit_id,
            'unitcharge_id' => $unitcharge->id,
            'model_type' => $user, ///This has plymorphism because an invoice can also be sent to a vendor.
            'model_id' => $userId->user_id,
            'name' => $unitcharge->charge_name,
            'totalamount' => null,
           //TEST:Invoice generation without 'status' => 'unpaid',
            'duedate' => null,
        ];
    }

    private function createInvoice($data)
    {
        return Invoice::create($data);
    }



    private function createInvoiceItems($invoice, $unitcharge, $model = null)
    {
        if ($unitcharge->charge_type === 'units') {
            $nextdateFormatted = Carbon::parse($unitcharge->nextdate)->format('Y-m-d');
            $updatedFormatted = Carbon::parse($unitcharge->updated_at ?? Carbon::now())->format('Y-m-d');

            $amount = 0.00;
            $meterReadings = MeterReading::where('unit_id', $unitcharge->unit_id)
                ->where('unitcharge_id', $unitcharge->id)
                ->where('startdate', '<=', $nextdateFormatted)
                ->where('enddate', '>=', $updatedFormatted)
                ->get();
            
            // TODO: find out why its generating wrong amounts
            foreach ($meterReadings as $reading) {
                // Calculate the amount based on meter readings and assign it to $amount
                // If theres more than one reading,Accumulate the amount from each reading
                $amount += $reading->amount;
            }
        } else {
            // If charge_type is not 'units', use the unitcharge rate as the amount
            $amount = $unitcharge->rate;
        }

        if ($model) {
            $items = $model->getItems;

            // Create expense items from the model items
            foreach ($items as $item) {
                InvoiceItems::create([
                    'invoice_id' => $invoice->id,
                    'unitcharge_id' => $unitcharge->id,
                    'chartofaccount_id' => $unitcharge->chartofaccounts_id,
                    'description' => $item->item,
                    'amount' => $item->amount,
                ]);
            }
            /// For models without children items but need invoice e.g Maintenance
        } else {
            // Create invoice items
            InvoiceItems::create([
                'invoice_id' => $invoice->id,
                'unitcharge_id' => $unitcharge->id,
                'chartofaccount_id' => $unitcharge->chartofaccounts_id,
                'description' => $unitcharge->charge_name,
                'amount' => $amount,
            ]);
        }

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
                        ->where('startdate', '<=', $nextdateFormatted)
                        ->where('enddate', '>=', $updatedFormatted)
                        ->get();

                    foreach ($meterReadings as $reading) {
                        // Calculate the amount based on meter readings and assign it to $amount
                        $amount += $reading->amount;
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

    public function invoiceEmail($invoice, $reminder = null)
    {


        $user = $invoice->model;
        $lease = Lease::where('unit_id',$invoice->unit_id)->first();
        $unitchargeId = $invoice->invoiceItems->pluck('unitcharge_id')->first();
        $sixMonths = now()->subMonths(6);
        $transactions = Transaction::where('created_at', '>=', $sixMonths)
            ->where('unit_id', $invoice->unit_id)
            ->where('unitcharge_id', $unitchargeId)
            ->get();
        $groupedInvoiceItems = $transactions->groupBy('unitcharge_id');
        $openingBalance = $this->calculateOpeningBalance($invoice);
          //// Data for the Payment Methods
        $PaymentMethod = PaymentMethod::where('property_id',$invoice->property_id)->get();
        $viewContent = View::make('email.statement', [
            'user' => $user,
            'invoice' => $invoice,
            'transactions' => $transactions,
            'groupedInvoiceItems' => $groupedInvoiceItems,
            'openingBalance' => $openingBalance,
            'PaymentMethod' => $PaymentMethod,
        ])->render();

        //CHECK IF EMAILS FOR THE LEASE ARE ENABLED
       
        $emailNotificationsEnabled = Setting::getSettingForModel(get_class($lease), $lease->id, 'invoiceemail');
         //CHECK IF EMAILS FOR THE LEASE ARE ENABLED
        $textNotificationsEnabled = Setting::getSettingForModel(get_class($lease), $lease->id, 'invoicetexts');
      //  dd($emailNotificationsEnabled);

        try {
            if ($emailNotificationsEnabled !== 'NO') {
            $user->notify(new InvoiceGeneratedNotification($invoice, $user, $viewContent, $reminder));
            }

            if ($textNotificationsEnabled !== 'NO') {

                $recipients = collect([$user]);
                $notificationClass = InvoiceGeneratedTextNotification::class;
                $notificationParams = ['invoice' => $invoice, 'user' => $user];
        
                foreach($recipients as $recipient){
                    $result = $this->smsService->queueSmsNotification($recipient,$notificationClass, $notificationParams);
                    }
                
        
             //   $user->notify(new InvoiceGeneratedTextNotification($invoice, $user, $viewContent, $reminder));
                }
        } catch (\Exception $e) {
            // Log the error or perform any necessary actions
            Log::error('Failed to send payment notification: ' . $e->getMessage());
        }
    }
}
