<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $propertyTypes = [
            [
                'property_category' => 'Residential',
                'property_type' => 'Apartment',
            ],
            [
                'property_category' => 'Residential',
                'property_type' => 'Town House',
            ],
            [
                'property_category' => 'Residential',
                'property_type' => 'Office',
            ],
            [
                'property_category' => 'Commercial',
                'property_type' => 'Offices',
            ],
            [
                'property_category' => 'Commercial',
                'property_type' => 'Shopping Center',
            ],
        ];
        
        foreach ($propertyTypes as $propertyTypeData) {
            $existingSetting = PropertyType::where('property_category', $propertyTypeData['property_category'])
            ->where('property_type', $propertyTypeData['property_type'])
            ->first();
            if (!$existingSetting) {
                PropertyType::create($propertyTypeData);
            }
          
        }
        
    }
}
