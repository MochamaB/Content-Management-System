<?php

namespace Database\Seeders;

use App\Models\SmsCredit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsCreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        SmsCredit::create([
            'credit_type' => '3',
            'property_id'  => NULL,
            'user_id'  => NULL,
            'tariff'  => 2,
            'available_credits'  => 10,
            'blocked_credits'  => 0,
            'used_credits'  => 0,
        ]);
    }
}
