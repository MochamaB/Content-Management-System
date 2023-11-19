<?php
// app/Actions/UpdateNextDateAction.php

namespace App\Actions;

use App\Models\Chartofaccount;
use App\Models\InvoiceItems;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Unitcharge;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class RecordTransactionAction
{
    use AsAction;

    public function securitydeposit(Unitcharge $unitcharge, string $modelname)
    {
        $description = Chartofaccount::where('id',$unitcharge->chartofaccounts_id)->first();
        $debit = 1; ///Bank Account
        
        
      
       Transaction::create([
                'property_id' => $unitcharge->property_id,
                'unit_id' => $unitcharge->unit_id,
                'unitcharge_id' => $unitcharge->id,
                'transactionable_id' => $unitcharge->id,
                'transactionable_type' => $modelname,///Model Name Unitcharge
                'description' => $description, ///Description of the charge
                'debitaccount_id' => 1,
                'creditaccount_id' => $unitcharge->chartofaccounts_id,
                'amount' => $unitcharge->rate,
            ]);
    }

    public function invoiceCharges(Invoice $invoice)
    {
        $invoiceitems = InvoiceItems::where('invoice_id',$invoice->id)->get();
       
        foreach($invoiceitems as $item)
        {
        $description = Chartofaccount::where('id',$item->chartofaccount_id)->first();
        Transaction::create([
            'property_id' => $invoice->property_id,
            'unit_id' => $invoice->unit_id,
            'unitcharge_id' => $item->unitcharge_id,
            'transactionable_id' => $item->unitcharge_id,
            'transactionable_type' => 'Unitcharges',///Model Name Unitcharge
            'description' => $description->account_name, ///Description of the charge
            'debitaccount_id' => 2,
            'creditaccount_id' => $item->chartofaccount_id,
            'amount' => $item->amount,
        ]);
    }


    }
}