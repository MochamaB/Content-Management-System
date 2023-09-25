<?php

namespace Database\Seeders;

use App\Models\Chartofaccounts;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chartOfAccounts = [
            [
                'account_number' => '110',
                'account_type' => 'Asset',
                'account_name' => 'Bank Account',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '111',
                'account_type' => 'Asset',
                'account_name' => 'Accounts Receivable',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '112',
                'account_type' => 'Asset',
                'account_name' => 'Undeposited Funds',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '210',
                'account_type' => 'Liability',
                'account_name' => 'Accounts Payable',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '211',
                'account_type' => 'Liability',
                'account_name' => 'Last Months Rent',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '212',
                'account_type' => 'Liability',
                'account_name' => 'Prepayments',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '213',
                'account_type' => 'Liability',
                'account_name' => 'Security Deposit Liability',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '310',
                'account_type' => 'Income',
                'account_name' => 'Rent Income',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '311',
                'account_type' => 'Income',
                'account_name' => 'Utility Income',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '312',
                'account_type' => 'Income',
                'account_name' => 'Repairs Income',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '313',
                'account_type' => 'Income',
                'account_name' => 'Application Fee',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '314',
                'account_type' => 'Income',
                'account_name' => 'Late Fees Income ',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '410',
                'account_type' => 'Expenses',
                'account_name' => 'General Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '411',
                'account_type' => 'Expenses',
                'account_name' => 'Bathroom Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '412',
                'account_type' => 'Expenses',
                'account_name' => 'Kitchen Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '413',
                'account_type' => 'Expenses',
                'account_name' => 'Plumbing Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '414',
                'account_type' => 'Expenses',
                'account_name' => 'Floor Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '415',
                'account_type' => 'Expenses',
                'account_name' => 'Supplies',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '416',
                'account_type' => 'Expenses',
                'account_name' => 'Residential',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '417',
                'account_type' => 'Expenses',
                'account_name' => 'Software ',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '418',
                'account_type' => 'Expenses',
                'account_name' => 'Trash Collection',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '419',
                'account_type' => 'Expenses',
                'account_name' => 'Management Fees',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '420',
                'account_type' => 'Expenses',
                'account_name' => 'Agent Fees: Tenant Placement',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '421',
                'account_type' => 'Expenses',
                'account_name' => 'Taxes ',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '422',
                'account_type' => 'Expenses',
                'account_name' => 'Utility Fees',
                'account_level' => 'Parent Account',
            ],
        ];

        foreach ($chartOfAccounts as $chartOfAccountData) {
            Chartofaccounts::create($chartOfAccountData);
        }
    }
}
