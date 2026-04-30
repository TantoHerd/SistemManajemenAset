<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('type', [
                'warranty_expiring',     // Garansi hampir habis
                'maintenance_due',       // Maintenance terjadwal
                'asset_overdue',         // Aset terlambat dikembalikan
                'maintenance_completed', // Maintenance selesai
                'asset_checked_out',     // Aset di-checkout
                'asset_checked_in',      // Aset di-checkin
                'system'                 // Notifikasi sistem
            ])->default('system');
            $table->string('icon')->default('bell');
            $table->string('color')->default('primary');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};