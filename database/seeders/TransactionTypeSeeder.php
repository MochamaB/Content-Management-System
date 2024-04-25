<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $TranactionTypes = [
            [
                'name' => 'Rent Income',
                'description' => 'Tenant invoice for rent',
                'model' => 'Invoice',
                'account_type' => 'Income',
                'debitaccount_id' => '2',
                'creditaccount_id' => '8',
            ],
            [
                'name' => 'Utility Income',
                'description' => 'Tenant invoice for utilities',
                'model' => 'Invoice',
                'account_type' => 'Income',
                'debitaccount_id' => '2',
                'creditaccount_id' => '9',
            ],
            [
                'name' => 'Security Deposit Liability',
                'description' => 'Tenant Security Deposit',
                'model' => 'Deposit',
                'account_type' => 'Liability',
                'debitaccount_id' => '1',
                'creditaccount_id' => '7',
            ],
            [
                'name' => 'Security Deposit Liability',
                'description' => 'Tenant deposit payment/refund',
                'model' => 'Payment',
                'account_type' => 'Liability',
                'debitaccount_id' => '7',
                'creditaccount_id' => '1',
            ],
            [
                'name' => 'General Repairs',
                'description' => 'Expense from vendor for repairs',
                'model' => 'Expense',
                'account_type' => 'Expense',
                'debitaccount_id' => '13',
                'creditaccount_id' => '4',
            ],
            [
                'name' => 'General Repairs',
                'description' => 'Vendor Payment for repairs',
                'model' => 'Payment',
                'account_type' => 'Expense',
                'debitaccount_id' => '4',
                'creditaccount_id' => '1',
            ],
            [
                'name' => 'Rent Income',
                'description' => 'Payment of  rent invoice',
                'model' => 'Payment',
                'account_type' => 'Income',
                'debitaccount_id' => '2',
                'creditaccount_id' => '1',
            ],
        ];

        foreach ($TranactionTypes as $transactionTypeData) {
            $existingTransactionType = TransactionType::where('name', $transactionTypeData['name'])
            ->where('model', $transactionTypeData['model'])
            ->first();
            if (!$existingTransactionType) {
                TransactionType::create($transactionTypeData);
            }
          
        }
    }
}
