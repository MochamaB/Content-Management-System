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
            'credit_type' => 'John doe',
            'property_id' => 'john@gmail.com',
            'mobile' => '911234567891',
           
        ]);
    }
}
