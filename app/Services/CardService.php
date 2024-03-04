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

class FilterService
{

   

    public function invoiceCard($invoices)
    {
        $invoiceCount = $invoices->count();
        $invoicePayments = $invoices->withCount('payments')->get();
        $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        return [
            'totalinvoices' => [    
                'title' => 'Total Invoices', ///TOTAL CARD
                'firstvalue' => $invoiceCount,
                'secondvalue' => $paymentCount,
                'modelthreeCount' => $invoicesWithoutPaymentsCount,
                'totalCardInfo1' => $totalCardInfo1,
                'totalCardInfo2' => $totalCardInfo2,
                'totalCardInfo3' => $totalCardInfo3,
            ],
            'unit_id' => ['label' => 'Units', 'values' => $units, 'inputType' => 'select'],
            'creditaccount_id' => ['label' => 'Accounts', 'values' => $accounts, 'inputType' => 'selectgroup'],
            'from_date' => ['label' => 'From', 'values' => '', 'inputType' => 'date'],
            'to_date' => ['label' => 'To', 'values' => '', 'inputType' => 'date']
        ];
    }

    public function getIncomeStatementFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();

        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select'],
            'from_date' => ['label' => 'From', 'values' => '', 'inputType' => 'date'],
            'to_date' => ['label' => 'To', 'values' => '', 'inputType' => 'date']
        ];
    }

    public function getAllLeasesFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $units = Unit::pluck('unit_number', 'id')->toArray();
        $status = Lease::pluck('status', 'status')->toArray();
        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties],
            'unit_id' => ['label' => 'Units', 'values' => $units],
            'status' => ['label' => 'Status', 'values' => $status]
        ];
    }

    public function getInvoiceFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $units = Unit::pluck('unit_number', 'id')->toArray();
        $unitcharge = Unitcharge::pluck('charge_name', 'id')->unique()->toArray();
        $status = [
            'paid' => 'Paid',
            'unpaid' => 'Not Paid',
            'overpaid' => 'Over Paid',
            'partially_paid' => 'Partially Paid'
        ];

        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select', 'filtertype' => 'main'],
            'unit_id' => ['label' => 'Units', 'values' => $units, 'inputType' => 'select', 'filtertype' => 'main'],
            'status' => ['label' => 'Status', 'values' => $status, 'inputType' => 'select', 'filtertype' => 'main'],
            'unitcharge_id' => ['label' => 'Charge', 'values' => $unitcharge, 'inputType' => 'select', 'filtertype' => 'advanced'],
            'from_date' => ['label' => 'From', 'values' => '', 'inputType' => 'date', 'filtertype' => 'advanced'],
            'to_date' => ['label' => 'To', 'values' => '', 'inputType' => 'date', 'filtertype' => 'advanced']
        ];
    }

    public function getMeterReadingsFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $units = Unit::pluck('unit_number', 'id')->toArray();
        $unitcharge = Unitcharge::where('charge_type', 'units')->get()->groupBy('charge_name');
     //  $unitcharge = $charge->map(function($item) {
       //     return [
      //          'id' => $item[0]->id,
       //         'charge_name' => $item[0]->charge_name
      //      ];
      //  })->toArray();
       
      //  dd($unitcharge);
        // Convert the grouped collection back to an array
       // $unitcharge = $groupedUnitcharge->toArray();

        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select', 'filtertype' => 'main'],
            'unit_id' => ['label' => 'Units', 'values' => $units, 'inputType' => 'select', 'filtertype' => 'main'],
            'unitcharge_id' => ['label' => 'Charge', 'values' => $unitcharge, 'inputType' => 'selectarray', 'filtertype' => 'advanced'],
            'from_date' => ['label' => 'From', 'values' => '', 'inputType' => 'date', 'filtertype' => 'advanced'],
            'to_date' => ['label' => 'To', 'values' => '', 'inputType' => 'date', 'filtertype' => 'advanced']
        ];
    }
}
