<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            // Jenis maintenance otomatis
            $table->enum('auto_maintenance_frequency', ['none', 'monthly', 'quarterly', 'semi_annual', 'annual'])
                  ->default('none')
                  ->after('notes');
            
            // Tanggal maintenance terakhir
            $table->date('last_maintenance_date')->nullable()->after('auto_maintenance_frequency');
            
            // Tanggal maintenance berikutnya
            $table->date('next_maintenance_date')->nullable()->after('last_maintenance_date');
            
            // Apakah maintenance otomatis aktif
            $table->boolean('auto_maintenance_active')->default(false)->after('next_maintenance_date');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'auto_maintenance_frequency',
                'last_maintenance_date',
                'next_maintenance_date',
                'auto_maintenance_active'
            ]);
        });
    }
};