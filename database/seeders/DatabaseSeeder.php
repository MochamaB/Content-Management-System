<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            CreateSuperUserSeeder::class,
            PropertyTypeSeeder::class,
            ChartOfAccountsSeeder::class,
          //  PaymentMethodSeeder::class,
          
            InvoiceTaskSeeder::class,
            AmenitySeeder::class,
        ]);
    }
}
