<?php

// app/Services/Reports/PropertyReportService.php

namespace App\Services\Reports;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Unit;

class FinancialReportService
{
    public function getColumns($submodule)
    {
        // Determine the columns based on the submodule
        switch ($submodule) {
            case 'incomestatement':
                return $this->getIncomeStatementColumns();
                // Add more cases as needed for other submodules
                default:
                return ['column1', 'column2', 'column3'];
                break;
        }
    }
    //// Default methods /////////
    public function getFilters($submodule)
    {
        // Determine the columns based on the submodule
        switch ($submodule) {
            case 'incomestatement':
           //     return $this->getAllLeasesFilters();
                break;

            case 'tenantsummary':
              //  return $this->getTenantSummaryFilters();
                break;
                // Add more cases as needed for other submodules
                default:
                return $this->getDefaultFilters();
                break;
        }
    }

    public function getData($submodule)
    {
        // Query the data based on the submodule
        switch ($submodule) {
            case 'incomestatement':
                return $this->getIncomeStatementData();
                // Add more cases as needed for other submodules
        }
    }

    protected function getDefaultFilters()
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

    protected function getIncomeStatementColumns()
    {
        // Define the columns for the unit report
        return ['column1', 'column2', 'column3'];
    }

    protected function getIncomeStatementData()
    {
        // Query the data for the unit report
        $properties = Property::all();

        // Transform the data as needed for the report
        $data = $properties->map(function ($property) {
            return [
                'column1' => $property->id,
                'column2' => $property->property_name,
                'column3' => $property->property_location,
            ];
        });

        return $data;
    }
}
