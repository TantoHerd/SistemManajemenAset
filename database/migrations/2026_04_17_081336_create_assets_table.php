<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            
            // Foreign keys
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('assigned_to')->nullable();
            
            // Status
            $table->enum('status', [
                'available',    // Tersedia
                'in_use',       // Sedang dipakai
                'maintenance',  // Perbaikan
                'damaged',      // Rusak
                'disposed'      // Dihapuskan
            ])->default('available');
            
            // Financial
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('residual_value', 15, 2)->default(0);
            $table->integer('useful_life_months');
            $table->decimal('current_value', 15, 2);
            
            // Barcode
            $table->string('barcode_image')->nullable();
            
            // Additional info
            $table->text('notes')->nullable();
            $table->date('warranty_expiry')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('restrict');
                  
            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('restrict');
                  
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            // Indexes untuk performa query
            $table->index('asset_code');
            $table->index('status');
            $table->index('serial_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};