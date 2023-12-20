<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentTypes = [
            [
                'name'  => 'Cash',
                'account_number'  => 'Cash Account',
                'account_name'  => 'Cash Account',
                'provider'  => 'None',
                
            ],
            [
                'name'  => 'MPESA',
                'account_number'  => 'Paybill Number',
                'account_name'  => 'Company Name',
                'provider'  => 'Safaricom',
            ],
            [
                'name'  => 'Cheque or Deposit',
                'account_number'  => '0100 0000 0000',
                'account_name'  => 'Company Name',
                'provider'  => 'Bank Name',
            ],
          
        ];
        
        foreach ($paymentTypes as $paymentTypesData) {
            PaymentType::create($paymentTypesData);
        }
        
    }
    }

