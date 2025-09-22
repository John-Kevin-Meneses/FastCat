<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('schedules')->insert([
            [
                'route_id' => 1, // belongs to Route A
                'departure_time' => "11:00:00",
                'arrival_time' => "13:00:00",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 2, // belongs to Route B
                'departure_time' => "14:00:00",
                'arrival_time' => "16:00:00",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
