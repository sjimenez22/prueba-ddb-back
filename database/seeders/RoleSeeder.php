<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            'c_name' => 'Administrador',
            'c_tag' => 'admin',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('roles')->insert([
            'c_name' => 'Usuario',
            'c_tag' => 'user',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
