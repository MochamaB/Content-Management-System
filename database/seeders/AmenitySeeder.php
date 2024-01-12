<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $amenities = [
            [
                'amenity_name' => 'Swimming Pool',
            ],
            [
                'amenity_name' => 'Gym',
            ],
            [
                'amenity_name' => 'Lift',
                
            ],
            [
                'amenity_name' => 'Security',
            ],
            [
                'amenity_name' => 'Parking',
            ],
        ];
        
        foreach ($amenities as $amenitiesTypeData) {
            Amenity::create($amenitiesTypeData);
        }
    }
}
