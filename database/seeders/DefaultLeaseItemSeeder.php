<?php

namespace Database\Seeders;

use App\Models\DefaultLeaseItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultLeaseItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultLeaseItems = [
            [
                'category' => 'Kitchen Area',
                'item_description' => 'Kitchen Sink',
            ],
            [
                'category' => 'Kitchen Area',
                'item_description' => 'Countertops',
            ],
            [
                'category' => 'Kitchen Area',
                'item_description' => 'Cabinets',
            ],
            [
                'category' => 'Bedrooms',
                'item_description' => 'Wardrobe',
            ],
            [
                'category' => 'Bedrooms',
                'item_description' => 'Mirror',
            ],
            [
                'category' => 'Bathrooms',
                'item_description' => 'Shower',
            ],
            [
                'category' => 'Bathrooms',
                'item_description' => 'Toilet',
            ],
            [
                'category' => 'Bathrooms',
                'item_description' => 'Sink',
            ],
            [
                'category' => 'Bathrooms',
                'item_description' => 'Mirror',
            ],
            [
                'category' => 'Access Control',
                'item_description' => 'Main Door Key',
            ],
            [
                'category' => 'Access Control',
                'item_description' => 'Main Gate Key',
            ],
        ];
        
        foreach ($defaultLeaseItems as $defaultLeaseItemData) {
            $existingSetting = DefaultLeaseItem::where('category', $defaultLeaseItemData['category'])
            ->where('item_description', $defaultLeaseItemData['item_description'])
            ->first();
            if (!$existingSetting) {
                DefaultLeaseItem::create($defaultLeaseItemData);
            }
          
        }
    }
}
