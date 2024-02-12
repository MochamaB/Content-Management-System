<?php

// app/Services/Reports/PropertyReportService.php

namespace App\Services;

use App\Models\Chartofaccount;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FilterService
{

    public function getDefaultFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $units = Unit::pluck('unit_number', 'id')->toArray();
        $status = Lease::pluck('status', 'status')->toArray();
        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties],
            'unit' => ['label' => 'Units', 'values' => $units],
            'status' => ['label' => 'Status', 'values' => $status]
        ];
    }

    public function getGeneralLedgerFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $units = Unit::pluck('unit_number', 'id')->toArray();
        $account = Chartofaccount::all();
        $accounts = $account->groupBy('account_type');
        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select'],
            'unit_id' => ['label' => 'Units', 'values' => $units, 'inputType' => 'select'],
            'transactionable_type' => ['label' => 'Accounts', 'values' => $accounts, 'inputType' => 'select']
        ];
    }

    ///////// All Leases///////

    //////// Tenant Summary




}
