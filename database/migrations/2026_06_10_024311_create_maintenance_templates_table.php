<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category_id')->nullable(); // Bisa spesifik per kategori
            $table->enum('frequency', ['monthly', 'quarterly', 'semi_annual', 'annual']);
            $table->text('description');
            $table->text('checklist')->nullable(); // JSON checklist
            $table->integer('estimated_hours')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_templates');
    }
};