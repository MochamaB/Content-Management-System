<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxes = [

            [  
                'property_type_id' => 1,
                'name' => 'Monthly Rental Income',
                'taxable_type' => 'App\\Models\\Payment',
                'rate' => 7.5,
                'status' => 'active',
                'description' => 'Monthly Rental Income tax for residential properties ',
                'related_model_type' => 'App\\Models\\Invoice',
                'related_model_condition' => ['name' => 'rent'],
                'additional_condition' => null
            ],
           
        ];

        foreach ($taxes as $taxData) {
            // Check if a record with the same name, module, and submodule already exists
            $existingTax = Tax::where('name', $taxData['name'])
                ->first();

            // Insert only if the record does not exist
            if (!$existingTax) {
                Tax::create($taxData);
            }
        }
    }
}
