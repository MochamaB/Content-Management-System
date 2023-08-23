<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateSuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'firstname' => 'Property', 
            'lastname' => 'SuperAdmin', 
            'email' => 'propertysuperadmin@gmail.com',
            'email_verified_at'=> '2023-03-12 15:36:35',
            'password' => Hash::make('admin!23') ,
            'phonenumber' => '0123456789',
            'idnumber' => '0123456789',
            'status' => 'Active',
            'profilepicture' => 'avatar.jpg',
        ]);
    
    }
}
