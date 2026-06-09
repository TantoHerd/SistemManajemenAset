<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('stock_opname_sessions')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('expected_location')->nullable();
            $table->enum('actual_status', ['found', 'missing', 'damaged', 'moved'])->default('found');
            $table->string('actual_location')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users');
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_opname_items');
    }
};