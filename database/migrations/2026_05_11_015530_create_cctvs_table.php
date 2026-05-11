<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cctvs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->integer('port')->default(80);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('stream_url')->nullable();
            $table->string('snapshot_url')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'inactive', 'error'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctvs');
    }
};