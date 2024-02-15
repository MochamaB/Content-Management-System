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

    public function getIncomeStatementFilters()
    {
        $properties = Property::pluck('property_name', 'id')->toArray();
        $units = Unit::pluck('unit_number', 'id')->toArray();
       
        $threemonths = now()->subMonths(3);
        $periods = [
            'now()->startOfMonth()' => 'Month to Date',
            'now()->subMonths(3)' => 'Three Months to Date',
            '.now()->subMonths(6).' => 'Six Months to Date',
            '.now()->subYear().' => 'One Year',
            '.now()->subMonth()->startOfMonth().' => 'Last Month',
        ];
        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select'],
            'unit_id' => ['label' => 'Units', 'values' => $units, 'inputType' => 'select'],
            'created_at' => ['label' => 'Period', 'values' =>$periods , 'inputType' => 'selectdefault']
        ];
    }

    public function periods()
    {
         $periods = [
        'month_to_date' => 'Month to Date',
        'three_months_to_date' => 'Three Months to Date',
        'six_months_to_date' => 'Six Months to Date',
        'one_year' => 'One Year',
        'last_month' => 'Last Month',
    ];
    $periods = [
        'Month to Date' => now()->startOfMonth(),
        'Three Months to Date' => now()->subMonths(3),
        'Six Months to Date' => now()->subMonths(6),
        'One Year' => now()->subYear(),
        'Last Month' => now()->subMonth()->startOfMonth()
    ];
    }

    ///////// All Leases///////

    //////// Tenant Summary




}
