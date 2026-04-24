<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Level 1: Gedung
        $gedungA = DB::table('locations')->insertGetId([
            'name' => 'Gedung A',
            'code' => 'A',
            'parent_id' => null,
            'building' => 'Gedung A',
            'floor' => null,
            'room' => null,
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $gedungB = DB::table('locations')->insertGetId([
            'name' => 'Gedung B',
            'code' => 'B',
            'parent_id' => null,
            'building' => 'Gedung B',
            'floor' => null,
            'room' => null,
            'address' => 'Jl. Gatot Subroto No. 45, Jakarta',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Level 2: Lantai di Gedung A
        $lantai1 = DB::table('locations')->insertGetId([
            'name' => 'Lantai 1',
            'code' => 'A-1',
            'parent_id' => $gedungA,
            'building' => 'Gedung A',
            'floor' => '1',
            'room' => null,
            'address' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $lantai2 = DB::table('locations')->insertGetId([
            'name' => 'Lantai 2',
            'code' => 'A-2',
            'parent_id' => $gedungA,
            'building' => 'Gedung A',
            'floor' => '2',
            'room' => null,
            'address' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $lantai3 = DB::table('locations')->insertGetId([
            'name' => 'Lantai 3',
            'code' => 'A-3',
            'parent_id' => $gedungA,
            'building' => 'Gedung A',
            'floor' => '3',
            'room' => null,
            'address' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Level 3: Ruangan di Lantai 1
        DB::table('locations')->insert([
            [
                'name' => 'IT Department',
                'code' => 'A-1-IT',
                'parent_id' => $lantai1,
                'building' => 'Gedung A',
                'floor' => '1',
                'room' => 'IT Department',
                'address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance Department',
                'code' => 'A-1-FIN',
                'parent_id' => $lantai1,
                'building' => 'Gedung A',
                'floor' => '1',
                'room' => 'Finance Department',
                'address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HR Department',
                'code' => 'A-1-HR',
                'parent_id' => $lantai1,
                'building' => 'Gedung A',
                'floor' => '1',
                'room' => 'HR Department',
                'address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
        // Level 3: Ruangan di Lantai 2
        DB::table('locations')->insert([
            [
                'name' => 'Marketing Department',
                'code' => 'A-2-MKT',
                'parent_id' => $lantai2,
                'building' => 'Gedung A',
                'floor' => '2',
                'room' => 'Marketing Department',
                'address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales Department',
                'code' => 'A-2-SLS',
                'parent_id' => $lantai2,
                'building' => 'Gedung A',
                'floor' => '2',
                'room' => 'Sales Department',
                'address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
        // Server room di Lantai 3
        DB::table('locations')->insert([
            'name' => 'Server Room',
            'code' => 'A-3-SRV',
            'parent_id' => $lantai3,
            'building' => 'Gedung A',
            'floor' => '3',
            'room' => 'Server Room',
            'address' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Level 2: Lantai di Gedung B
        $lantai1B = DB::table('locations')->insertGetId([
            'name' => 'Lantai 1',
            'code' => 'B-1',
            'parent_id' => $gedungB,
            'building' => 'Gedung B',
            'floor' => '1',
            'room' => null,
            'address' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('locations')->insert([
            'name' => 'Warehouse',
            'code' => 'B-1-WH',
            'parent_id' => $lantai1B,
            'building' => 'Gedung B',
            'floor' => '1',
            'room' => 'Warehouse',
            'address' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}