<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('username')->nullable();
            $table->string('action'); // create, read, update, delete, login, logout, export
            $table->string('module'); // asset, maintenance, loan, user, cctv, mecard, stock_opname, backup
            $table->string('record_id')->nullable(); // ID record yang diakses
            $table->string('record_name')->nullable(); // Nama record untuk identifikasi
            $table->text('old_data')->nullable(); // Data sebelum perubahan (JSON)
            $table->text('new_data')->nullable(); // Data setelah perubahan (JSON)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Index untuk performa query
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'module']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};