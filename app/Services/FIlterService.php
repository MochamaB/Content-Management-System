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
        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties],
            'unit' => ['label' => 'Units', 'values' => $units],
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
    ///////// All Leases///////

    //////// Tenant Summary




}
