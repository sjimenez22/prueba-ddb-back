<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status_tasks')->insert([
            'c_name' => 'En proceso',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('status_tasks')->insert([
            'c_name' => 'Pendiente',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('status_tasks')->insert([
            'c_name' => 'Completado',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
