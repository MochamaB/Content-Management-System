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
        $periods = $this->getperiods();
        // Define the columns for the unit report
        return [
            'property_id' => ['label' => 'Properties', 'values' => $properties, 'inputType' => 'select'],
            'from_date' => ['label' => 'From', 'values' => '', 'inputType' => 'date'],
            'to_date' => ['label' => 'To', 'values' => '', 'inputType' => 'date']
        ];
    }

    public function getperiods()
    {
        $periods = [
            'month_to_date' => 'Month to Date',
            'three_months_to_date' => 'Three Months to Date',
            'six_months_to_date' => 'Six Months to Date',
            'one_year' => 'One Year',
            'last_month' => 'Last Month',
        ];
        $selectedPeriod = 'three_months_to_date';  // Set a default period

        switch ($selectedPeriod) {
            case 'month_to_date':
                return [now()->startOfMonth()];
            case 'three_months_to_date':
                return [now()->subMonths(3)];
            case 'six_months_to_date':
                return [now()->subMonths(6)];
            case 'one_year':
                return [now()->subYear()];
            case 'last_month':
                return [now()->subMonth()->startOfMonth()];
                // Add more cases as needed for other submodules
            default:
                return [];
        }
    }

    ///////// All Leases///////

    //////// Tenant Summary




}
