<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();

        $permissions = [
            'Access_Setting',
            'User_Management',
            'Add_role',
            'Edit_role',
            'Delete_role',
            'Add_user',
            'Edit_user',
            'Delete_user',

        ];

        foreach ($permissions as $p) {
            \Spatie\Permission\Models\Permission::create(['name' => $p]);
        }
    }
}
