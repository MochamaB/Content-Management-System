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
                'model_type' => 'App\\Models\\Property',
                'info'  => 'All Settings for the Property and Company',
                'name'  => 'Mass Update Utilities',
                'key'  => 'massupdate',
                'value'  => 'YES',
                'Description'  => 'All the Utilities and Charges attached to a property can be updated collectivelly',
            ],
            [  
                'model_type' => 'App\\Models\\Property',
                'info'  => 'All Settings for the Property and Company',
                'name'  => 'Send Property Notification',
                'key'  => 'propertynotifications',
                'value'  => 'NO',
                'Description'  => 'Send Notifications to all users when property details are updated',
            ],
            ///--------------- Lease Settings ------------///
            [
                'model_type' => 'App\\Models\\Lease',
                'info'  => 'Lease Application Settings',
                'name'  => 'Due date for lease',
                'key'  => 'duedate',
                'value'  => '15',
                'Description'  => 'Number of days for an invoice to be due after the invoice is generated',
            ],
            [
                'model_type' => 'App\\Models\\Lease',
                'info'  => 'Lease Application Settings',
                'name'  => 'Late fees for invoices',
                'key'  => 'latefees',
                'value'  => 'ON',
                'Description'  => 'Set or unset late fees on invoices once the due date is reached.',
            ],
            [
                'model_type' => 'App\\Models\\Lease',
                'info'  => 'Lease Application Settings',
                'name'  => 'Late fees Method Structure',
                'key'  => 'latefeesstructure',
                'value'  => 'NONE',
                'Description'  => 'Set structure of charging the late fees.',
            ],
            [
                'model_type' => 'App\\Models\\Lease',
                'info'  => 'Lease Application Settings',
                'name'  => 'Late fees percentage or flatfee',
                'key'  => 'latefeesrate',
                'value'  => '0',
                'Description'  => 'Set rate or a percentage of the late fees.',
            ],
            [
                'model_type' => 'App\\Models\\Lease',
                'info'  => 'Lease Application Settings',
                'name'  => 'Send Invoice Emails',
                'key'  => 'invoiceemail',
                'value'  => 'YES',
                'Description'  => 'Send emails to tenants for the Invoices generated.',
            ],
            [
                'model_type' => 'App\\Models\\Lease',
                'info'  => 'Lease Application Settings',
                'name'  => 'Send Invoice Texts',
                'key'  => 'invoicetexts',
                'value'  => 'YES',
                'Description'  => 'Send Texts to tenants for the Invoices generated.',
            ],
            [
                'model_type' => 'App\\Models\\Lease',
                'info'  => 'Lease Application Settings',
                'name'  => 'Send Lease Notications',
                'key'  => 'leasenotifications',
                'value'  => 'YES',
                'Description'  => 'Send Notifications about creation or updating of Lease Terms.',
            ],
        ];

        foreach ($settings as $SettingsData) {
            // Check if a record with the same name, module, and submodule already exists
            $existingSetting = Setting::where('key', $SettingsData['key'])
                ->where('model_type', $SettingsData['model_type'])
                ->where('name', $SettingsData['name'])
                ->first();

            // Insert only if the record does not exist
            if (!$existingSetting) {
                Setting::create($SettingsData);
            }
        }

    }
}
