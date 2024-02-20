<?php

namespace Database\Seeders;

use App\Models\Report;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reports = [
            ///--------------- Financial Reports ------------///
            [
                'name'  => 'income_statement',
                'title'  => 'Income Statement',
                'description'  => 'Income and expenses by property during a specified time frame.',
                'module'  => 'Financial',
                'submodule'  => 'incomestatement',

            ],
            [
                'name'  => 'general_ledger',
                'title'  => 'General Ledger',
                'description'  => 'Debit and credit transactions by property during a specified time frame..',
                'module'  => 'Financial',
                'submodule'  => 'generalledger',
            ],
            [
                'name'  => 'balance_sheet',
                'title'  => 'Balance Sheet',
                'description'  => 'Display the financial position, including assets, liabilities, and equity, at a specific date.',
                'module'  => 'Financial',
                'submodule'  => 'balancesheet',
            ],
            [
                'name'  => 'expense_report',
                'title'  => 'Expense Report',
                'description'  => 'Detail all property-related expenses, categorized by type (maintenance, utilities, property management fees, etc.).',
                'module'  => 'Financial',
                'submodule'  => 'expensereport',
            ],
            [
                'name'  => 'rent_roll',
                'title'  => 'Rent Roll',
                'description'  => 'Provide a snapshot of rental income, arrears, and lease details for all properties.',
                'module'  => 'Financial',
                'submodule'  => 'rentroll',
            ],
            [
                'name'  => 'tenant_deposits',
                'title'  => 'Tenant Deposits',
                'description'  => 'Provide a snapshot of all tenant deposits(security, Meter etc..).',
                'module'  => 'Financial',
                'submodule'  => 'tenantdeposits',
            ],

            ///--------------- Lease Reports ------------///
            [
                'name'  => 'Occupancy rate',
                'title'  => 'Occupancy Rate',
                'description'  => 'Shows all the occupied and vacant units against total available units',
                'module'  => 'Lease',
                'submodule'  => 'occupancyrate',
            ],
            [
                'name'  => 'all_leases',
                'title'  => 'All Leases',
                'description'  => 'Shows all the leases and their statuses',
                'module'  => 'Lease',
                'submodule'  => 'allleases',
            ],
            [
                'name'  => 'meter_readings',
                'title'  => 'Meter Readings Summary',
                'description'  => 'Displays all the charges with meter readings',
                'module'  => 'Lease',
                'submodule'  => 'meter_readings',
            ],
            [
                'name'  => 'tenant_summary',
                'title'  => 'Tenant Summary',
                'description'  => 'Display all the tenants and their units and balances',
                'module'  => 'Lease',
                'submodule'  => 'tenantsummary',
            ],
            [
                'name'  => 'property_summary',
                'title'  => 'Property Summary',
                'description'  => 'A detailed summary of all properties and its properties',
                'module'  => 'Property',
                'submodule'  => 'propertysummary',
            ],
            [
                'name'  => 'unit_summary',
                'title'  => 'Unit Summary',
                'description'  => 'A detailed summary of all unit listings and its properties',
                'module'  => 'Property',
                'submodule'  => 'unit_summary',
            ],
        ];

        foreach ($reports as $reportsData) {
            // Check if a record with the same name, module, and submodule already exists
            $existingReport = Report::where('name', $reportsData['name'])
                ->where('module', $reportsData['module'])
                ->where('submodule', $reportsData['submodule'])
                ->first();

            // Insert only if the record does not exist
            if (!$existingReport) {
                Report::create($reportsData);
            }
        }
    }
}
