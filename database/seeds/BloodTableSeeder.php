<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\TypeBlood;

class BloodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('type_bloods')->delete();

        $bgs = ['O-', 'O+', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];

        foreach($bgs as  $bg){
            TypeBlood::create(['Name' => $bg]);
        }
    }
}
