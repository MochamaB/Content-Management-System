<?php

// app/Services/Reports/PropertyReportService.php

namespace App\Services\Reports;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Request;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\FilterService;
use App\Services\TableViewDataService;

class LeaseReportService
{
    private $filterService;
    private $tableViewDataService;


    public function __construct(FilterService $filterService, TableViewDataService $tableViewDataService)
    {

        $this->filterService = $filterService;
        $this->tableViewDataService = $tableViewDataService;
    }

    public function getColumns($submodule)
    {
        // Determine the columns based on the submodule
        switch ($submodule) {
            case 'allleases':
                return $this->getAllLeasesColumns();
                break;

            case 'tenantsummary':
                return $this->getTenantSummaryColumns();
                break;
                // Add more cases as needed for other submodules
                default:
                return ['column1', 'column2', 'column3'];
                break;
        }
    }
    public function getFilters($submodule)
    {
        // Determine the columns based on the submodule
        switch ($submodule) {
            case 'allleases':
                return $this->filterService->getAllLeasesFilters();
                break;

           
                // Add more cases as needed for other submodules

            default:
                return $this->filterService->getDefaultFilters();
                break;
        }
    }

    public function getData($submodule, $filters)
    {
        // Query the data based on the submodule
        switch ($submodule) {
            case 'allleases':
                return $this->getAllLeasesData($filters);
                break;
            case 'tenantsummary':
                return $this->getTenantSummaryData($filters);
                break;
                // Add more cases as needed for other submodules
        }
    }
   

    ///////// All Leases///////
    protected function getAllLeasesColumns()
    {
        // Define the columns for the unit report
        return ['column1', 'column2', 'column3'];
    }


    protected function getAllLeasesData($filters)
    {
        // Query the data for the unit report
        $query = lease::with('property', 'unit', 'user');
        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                if ($column == 'from_date' || $column == 'to_date') {
                    // Use whereBetween on the created-at column with the date range
                    $query->whereBetween('created_at', [$value, $value]);
                } else {

                $query->where($column, $value);
                }
            }
        }
        // Get the data
        $leases = $query->get();
        // Transform the data as needed for the report
        $data = $leases->map(function ($lease) {
            return [
                'column1' => $lease->property->property_name,
                'column2' => $lease->unit->unit_number,
                'column3' => $lease->status,
            ];
        });

        return $data;
    }

    //////// Tenant Summary
    protected function getTenantSummaryColumns()
    {
        // Define the columns for the unit report
        return ['column1', 'column2', 'column3'];
    }

  

    protected function getTenantSummaryData()
    {
        // Query the data for the unit report
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $query =  User::role('tenant');
        } else {
            $query = User::Tenants($user);
            //  $tablevalues = $user->filterUsers();
        }

        $allowedFilters = [
            'property_id' => 'property_id',
            'unit_id' => 'unit_id',
            // Add more filters as needed
        ];
        foreach ($allowedFilters as $filterKey => $column) {
            if (isset($filters[$filterKey])) {
                $query->where($column, $filters[$filterKey]);
            }
        }
        $tenants =$query->get();

        // Transform the data as needed for the report
        $data = $tenants->map(function ($tenant) {
            return [
                'column1' => $tenant->firstname,
                'column2' => $tenant->lastname,
                'column3' => $tenant->status,
            ];
        });

        return $data;
    }
}
