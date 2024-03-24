<?php

// app/Services/Reports/PropertyReportService.php

namespace App\Services;

use Illuminate\Http\Request;
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

    public function getDefaultFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $units = Unit::pluck('unit_number', 'id')->toArray();
        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select'],

        ];
    }

    public function getGeneralLedgerFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $units = Unit::pluck('unit_number', 'id')->toArray();
        $account = Chartofaccount::all();
        $accounts = $account->groupBy('account_type');
        $accounts = $accounts->map(function ($group) {
            return $group->pluck('account_name', 'id')->toArray();
        });
        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select'],
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
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select', 'filtertype' => 'main'],
            'unit_id' => ['label' => 'Units', 'values' => $units, 'inputType' => 'select', 'filtertype' => 'main'],
            'unitcharge_id' => ['label' => 'Charge', 'values' => $unitcharge, 'inputType' => 'selectarray', 'filtertype' => 'advanced'],
            'from_date' => ['label' => 'From', 'values' => '', 'inputType' => 'date', 'filtertype' => 'advanced'],
            'to_date' => ['label' => 'To', 'values' => '', 'inputType' => 'date', 'filtertype' => 'advanced']
        ];
    }

    public function getUnitFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $unittype = [
            'rent' => 'For Rent',
            'sale' => 'For Sale',
        ];
        $unitcharge = Unitcharge::where('charge_type', 'units')->get()->groupBy('charge_name');
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select', 'filtertype' => 'main'],
            'unit_type' => ['label' => 'Types', 'values' => $unittype, 'inputType' => 'select', 'filtertype' => 'main'],

        ];
    }

    public function getUnitChargeFilters(Request $request)
    {
        $propertyId = $request->property_id;
        $properties = Property::pluck('property_name', 'id')->toArray();
        if ($propertyId) {
            $units = Unit::where('property_id', $propertyId)->pluck('unit_number', 'id')->toArray();
        }else {
            // If property_id is not provided, fetch all units
            $units = Unit::pluck('unit_number', 'id')->toArray();
        }
        $chargecycle = [
            'Monthly' => 'Recurring',
            'once' => 'Charged Once',
        ];
        $type = [
            'fixed' => 'Fixed Rate',
            'units' => 'Per Unit',
        ];
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select', 'filtertype' => 'main'],
            'unit_id' => ['label' => 'Units', 'values' => $units, 'inputType' => 'select', 'filtertype' => 'main'],
            'charge_cycle' => ['label' => 'Charge Frequency', 'values' => $chargecycle, 'inputType' => 'select', 'filtertype' => 'advanced'],
            'charge_type' => ['label' => 'Charge Type', 'values' => $type, 'inputType' => 'select', 'filtertype' => 'advanced'],
        ];
    }
}
