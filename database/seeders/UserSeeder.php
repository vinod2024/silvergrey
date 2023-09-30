<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super_admin_role = Role::create(['name'=>'super_admin']);
        $super_admin = User::create([
            'name' => 'Super Admin',
            'email' => 'super_admin@gmail.com',
            'password' => \Hash::make('12345678')        
        ]);

        $super_admin->assignRole($super_admin_role);        

        // Admin Permission
        $admin_role = Role::create(['name'=>'admin']);        


    }
}
