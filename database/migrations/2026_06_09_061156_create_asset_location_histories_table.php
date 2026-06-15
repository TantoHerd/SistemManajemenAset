<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asset_location_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('old_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('new_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('old_location_name')->nullable();
            $table->string('new_location_name')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index(['asset_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_location_histories');
    }
};