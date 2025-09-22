<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('routes')->insert([
            [
                'name' => 'Route A',
                'origin' => 'Batangas',
                'destination' => 'Calapan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Route B',
                'origin' => 'Bulalacao',
                'destination' => 'Caticlan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
