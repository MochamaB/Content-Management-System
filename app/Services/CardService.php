<?php

// app/Services/Reports/PropertyReportService.php

namespace App\Services;

use App\Models\Chartofaccount;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Unitcharge;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CardService
{

   

    public function invoiceCard($invoices)
    {
       

        $invoiceCount = $invoices->count();
      //  $invoicePayments = $invoices->withCount('payments')->get();
      //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'invoicecount' => ['title' => 'Total Invoices', 'firstvalue' => $invoiceCount, 'links' => 'View More'],
            'invoicecount2' => ['title' => 'Total Invoices', 'firstvalue' => $invoiceCount, 'links' => 'View More'],
        ];
        return $cards;
    }
    public function unitCard($units)
    {
       

        $unitCount = $units->count();
      //  $invoicePayments = $invoices->withCount('payments')->get();
      //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'unitCount' => ['title' => 'Total Units','icon' =>'', 'value' => $unitCount, 'amount'=>'','pecentage'=>'', 'links' => ''],
            'unitsleased' => ['title' => 'Units Leased','icon' =>'', 'value' => $unitCount, 'amount'=>'','pecentage'=>'', 'links' => ''],
            'No of Tenants' => ['title' => 'No of Tenants','icon' =>'', 'value' => $unitCount, 'amount'=>'','pecentage'=>'', 'links' => ''],
            'Vacant Units' => ['title' => 'Vacant Units','icon' =>'', 'value' => $unitCount, 'amount'=>'','pecentage'=>'', 'links' => ''],
        ];
        return $cards;
    }
   

}
