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
            CreateSuperUserSeeder::class,
            PropertyTypeSeeder::class,
            ChartOfAccountsSeeder::class,
          //  PaymentMethodSeeder::class,
            RolesTableSeeder::class,
            InvoiceTaskSeeder::class,
            AmenitySeeder::class,
        ]);
    }
}
