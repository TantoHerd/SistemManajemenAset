<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laptop',
                'code' => 'LAP',
                'description' => 'Laptop untuk karyawan',
                'useful_life_months' => 48,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Desktop PC',
                'code' => 'PC',
                'description' => 'Komputer desktop',
                'useful_life_months' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monitor',
                'code' => 'MON',
                'description' => 'Monitor komputer',
                'useful_life_months' => 48,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Printer',
                'code' => 'PRN',
                'description' => 'Printer dan scanner',
                'useful_life_months' => 36,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Network Switch',
                'code' => 'SW',
                'description' => 'Switch jaringan',
                'useful_life_months' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Server',
                'code' => 'SRV',
                'description' => 'Server dan rack',
                'useful_life_months' => 72,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Access Point',
                'code' => 'AP',
                'description' => 'Wireless access point',
                'useful_life_months' => 36,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smartphone',
                'code' => 'PHN',
                'description' => 'Smartphone dinas',
                'useful_life_months' => 24,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('categories')->insert($categories);
    }
}