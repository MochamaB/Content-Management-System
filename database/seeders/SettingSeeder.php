<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [

            [
                'module'  => 'Property Settings',
                'name'  => 'Utilities',
                'model_type' => 'App\\Models\\Property',
                'key'  => 'Mass Update Utilities',
                'value'  => 'YES',
                'Description'  => 'All the Utilities and Charges attached to a property can be updated collectivelly',
            ],
            ///--------------- Lease Settings ------------///
            [
                'module'  => 'Lease Settings',
                'name'  => 'Due date for Invoices',
                'model_type' => 'App\\Models\\Lease',
                'key'  => 'duedate',
                'value'  => '15',
                'Description'  => 'Number of days for an invoice to be due after the invoice is generated',
            ],
            [
                'module'  => 'Lease Settings',
                'name'  => 'Late fees policy',
                'model_type' => 'App\\Models\\Lease',
                'key'  => 'latefees',
                'value'  => 'ON',
                'Description'  => 'Set or unset late fees on invoices once the due date is reached.',
            ],
            [
                'module'  => 'Communication Settings',
                'name'  => 'Invoice Notifications',
                'model_type' => 'App\\Models\\Lease',
                'key'  => 'Send Invoice Emails',
                'value'  => 'YES',
                'Description'  => 'Send emails to tenants for the Invoices generated.',
            ],
            [
                'module'  => 'Communication Settings',
                'name'  => 'Invoice Notifications',
                'model_type' => 'App\\Models\\Lease',
                'key'  => 'Send Invoice Texts',
                'value'  => 'YES',
                'Description'  => 'Send Texts to tenants for the Invoices generated.',
            ],
            [
                'module'  => 'Communication Settings',
                'name'  => 'Lease Notifications',
                'model_type' => 'App\\Models\\Lease',
                'key'  => 'Send Lease Notications',
                'value'  => 'YES',
                'Description'  => 'Send Notifications about creation or updating of Lease Terms.',
            ],
        ];

        foreach ($settings as $SettingsData) {
            // Check if a record with the same name, module, and submodule already exists
            $existingSetting = Setting::where('key', $SettingsData['key'])
                ->where('module', $SettingsData['module'])
                ->where('name', $SettingsData['name'])
                ->first();

            // Insert only if the record does not exist
            if (!$existingSetting) {
                Setting::create($SettingsData);
            }
        }

    }
}
