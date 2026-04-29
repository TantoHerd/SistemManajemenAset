<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('spec_key', 100);
            $table->text('spec_value')->nullable();
            $table->timestamps();
            
            $table->unique(['asset_id', 'spec_key']);
            $table->index('spec_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_specifications');
    }
};