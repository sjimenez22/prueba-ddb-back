<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'c_name' => 'Admin',
            'c_email' => 'admin@gmail.com',
            'c_password' => '$2y$12$rIBxBtxoBXcohcTr.vq5cuKLjvPWffCzwDTBXz1MIbVmf0Aj3j3NO',
            'fk_role' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
            'c_name' => 'User',
            'c_email' => 'user@gmail.com',
            'c_password' => '$2y$12$rIBxBtxoBXcohcTr.vq5cuKLjvPWffCzwDTBXz1MIbVmf0Aj3j3NO',
            'fk_role' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
