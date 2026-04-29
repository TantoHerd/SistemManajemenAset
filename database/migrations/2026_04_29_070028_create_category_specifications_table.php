<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('label');
            $table->string('key')->comment('slug untuk field name');
            $table->enum('type', ['text', 'number', 'textarea', 'date', 'boolean', 'select'])
                  ->default('text');
            $table->json('options')->nullable()
                  ->comment('Untuk tipe select: [{"value":"val1","label":"Label 1"}]');
            $table->boolean('is_required')->default(false);
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['category_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_specifications');
    }
};