<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class CreateSuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::updateOrCreate([
            'firstname' => 'Property', 
            'lastname' => 'SuperAdmin', 
            'email' => 'propertysuperadmin@gmail.com',
            'email_verified_at'=> '2023-03-12 15:36:35',
            'password' => env('SUPERADMIN_PASSWORD'),
            'password_set' => true,
            'phonenumber' => '0786250600',
            'idnumber' => '0123456789',
            'status' => 'Active',
        ]);

         // Assign SuperAdmin role to the user
         $superAdminRole = Role::where('name', 'SuperAdmin')->first();
         $user->assignRole($superAdminRole);
    
    }
}
