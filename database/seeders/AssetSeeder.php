<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // Dapatkan ID kategori dan lokasi
        $laptopCategory = DB::table('categories')->where('code', 'LAP')->first();
        $itLocation = DB::table('locations')->where('code', 'A-1-IT')->first();
        $financeLocation = DB::table('locations')->where('code', 'A-1-FIN')->first();
        
        $assets = [
            [
                'asset_code' => 'LAP-IT-001',
                'name' => 'Dell XPS 15',
                'serial_number' => 'ABC123XYZ',
                'model' => 'XPS 15 9520',
                'brand' => 'Dell',
                'category_id' => $laptopCategory->id,
                'location_id' => $itLocation->id,
                'assigned_to' => null,
                'status' => 'available',
                'purchase_date' => '2024-01-15',
                'purchase_price' => 25000000,
                'residual_value' => 5000000,
                'useful_life_months' => 48,
                'current_value' => 25000000,
                'notes' => 'Laptop untuk tim IT',
                'warranty_expiry' => '2026-01-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'asset_code' => 'LAP-FIN-002',
                'name' => 'MacBook Pro 14',
                'serial_number' => 'XYZ789ABC',
                'model' => 'M3 Pro',
                'brand' => 'Apple',
                'category_id' => $laptopCategory->id,
                'location_id' => $financeLocation->id,
                'assigned_to' => null,
                'status' => 'available',
                'purchase_date' => '2024-02-20',
                'purchase_price' => 30000000,
                'residual_value' => 6000000,
                'useful_life_months' => 48,
                'current_value' => 30000000,
                'notes' => 'Laptop untuk tim finance',
                'warranty_expiry' => '2026-02-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'asset_code' => 'PC-IT-003',
                'name' => 'Dell OptiPlex 7010',
                'serial_number' => 'PC123IT',
                'model' => 'OptiPlex 7010',
                'brand' => 'Dell',
                'category_id' => DB::table('categories')->where('code', 'PC')->first()->id,
                'location_id' => $itLocation->id,
                'assigned_to' => null,
                'status' => 'available',
                'purchase_date' => '2023-10-10',
                'purchase_price' => 12000000,
                'residual_value' => 2000000,
                'useful_life_months' => 60,
                'current_value' => 12000000,
                'notes' => 'Desktop untuk developer',
                'warranty_expiry' => '2025-10-10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('assets')->insert($assets);
    }
}