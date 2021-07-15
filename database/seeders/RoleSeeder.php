<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[];
        for($i=0; $i < 20000; $i++){
            $data[] = [
                'name' => Str::random(20),
                'guard_name' => 'web'
            ];
        };
        DB::table('roles')->insert($data);
    }
}
