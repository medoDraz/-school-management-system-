<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=\App\User::create([
            'name'=>'super',
//            'last_name' =>'admin',
            'email' =>'admin@admin.com',
            'password' =>bcrypt('12345678'),
            'status' =>1,
        ]);
//        $user->attachRole('super_admin');
        $role = \Spatie\Permission\Models\Role::first();
        $user->assignRole($role->id);
    }
}
