<?php

namespace Database\Seeders;

use App\Models\Chartofaccount;
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
                'account_number' => '11001',
                'account_type' => 'Asset',
                'account_name' => 'Bank Account',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '22000',
                'account_type' => 'Asset',
                'account_name' => 'Accounts Receivable',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '22001',
                'account_type' => 'Asset',
                'account_name' => 'Undeposited Funds',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '31000',
                'account_type' => 'Liability',
                'account_name' => 'Accounts Payable',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '32000',
                'account_type' => 'Liability',
                'account_name' => 'Last Months Rent',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '33000',
                'account_type' => 'Liability',
                'account_name' => 'Prepayments',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '34000',
                'account_type' => 'Liability',
                'account_name' => 'Security Deposit Liability',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '41000',
                'account_type' => 'Income',
                'account_name' => 'Rent Income',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '42000',
                'account_type' => 'Income',
                'account_name' => 'Utility Income',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '43000',
                'account_type' => 'Income',
                'account_name' => 'Repairs Income',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '44000',
                'account_type' => 'Income',
                'account_name' => 'Application Fee',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '45000',
                'account_type' => 'Income',
                'account_name' => 'Late Fees Income ',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '91000',
                'account_type' => 'Expenses',
                'account_name' => 'General Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '91001',
                'account_type' => 'Expenses',
                'account_name' => 'Bathroom Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '91002',
                'account_type' => 'Expenses',
                'account_name' => 'Kitchen Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '91003',
                'account_type' => 'Expenses',
                'account_name' => 'Plumbing Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '91004',
                'account_type' => 'Expenses',
                'account_name' => 'Floor Repairs',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '92000',
                'account_type' => 'Expenses',
                'account_name' => 'Supplies',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '93000',
                'account_type' => 'Expenses',
                'account_name' => 'Residential',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '94000',
                'account_type' => 'Expenses',
                'account_name' => 'Software ',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '95000',
                'account_type' => 'Expenses',
                'account_name' => 'Trash Collection',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '96000',
                'account_type' => 'Expenses',
                'account_name' => 'Management Fees',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '97000',
                'account_type' => 'Expenses',
                'account_name' => 'Agent Fees: Tenant Placement',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '98000',
                'account_type' => 'Expenses',
                'account_name' => 'Taxes ',
                'account_level' => 'Parent Account',
            ],
            [
                'account_number' => '99000',
                'account_type' => 'Expenses',
                'account_name' => 'Utility Fees',
                'account_level' => 'Parent Account',
            ],
        ];

        foreach ($chartOfAccounts as $chartOfAccountData) {
            Chartofaccount::create($chartOfAccountData);
        }
    }
}
