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
            ReportSeeder::class,
            SettingSeeder::class,
            TransactionTypeSeeder::class,
            TaskSeeder::class,
            AmenitySeeder::class,
            TaxesSeeder::class,
            SmsCreditSeeder::class,
        ]);
    }
}
