<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->unique();
            
            // Relasi
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Tanggal
            $table->date('loan_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            
            // Status
            $table->enum('status', [
                'pending',      // Menunggu approval
                'approved',     // Disetujui
                'rejected',     // Ditolak
                'active',       // Sedang dipinjam
                'returned',     // Sudah dikembalikan
                'overdue',      // Terlambat
                'cancelled'     // Dibatalkan
            ])->default('pending');
            
            // Detail
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->text('condition_before')->nullable();
            $table->text('condition_after')->nullable();
            
            // Denda (opsional)
            $table->decimal('fine_amount', 15, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('status');
            $table->index(['asset_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};