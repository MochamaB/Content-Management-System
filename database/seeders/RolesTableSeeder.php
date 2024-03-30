<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'SuperAdmin',
                'guard_name' => 'web',
                'description' => 'God User',
            ],
            [
                'name' => 'Administrator',
                'guard_name' => 'web',
                'description' => 'Has control of every module and properties and cannot be edited',
            ],
            [
                'name' => 'Agency Company',
                'guard_name' => 'web',
                'description' => 'Has access to all modules but can only manage assigned properties and units',
            ],
            [
                'name' => 'Property Owner',
                'guard_name' => 'web',
                'description' => 'Has access to most modules but can only manage assigned properties and units',
            ],
            [
                'name' => 'Rental Owner / Landlord',
                'guard_name' => 'web',
                'description' => 'Also a landlord.Limited access to modules and can only access assigned units',
            ],
            [
                'name' => 'Leasing Agent',
                'guard_name' => 'web',
                'description' => 'Has access to the leasing workflow',
            ],
            [
                'name' => 'Manager Staff',
                'guard_name' => 'web',
                'description' => 'Has access to maintenance workflows',
            ],
            [
                'name' => 'Staff',
                'guard_name' => 'web',
                'description' => 'Has access to tasks only',
            ],
            [
                'name' => 'Tenant',
                'guard_name' => 'web',
                'description' => 'Has access to their information',
            ],
        ];

        foreach ($roles as $rolesData) {
            Role::create($rolesData);
        }
    }
}
