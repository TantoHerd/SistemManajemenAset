<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mecards', function (Blueprint $table) {
            // Cek dan tambah kolom jika belum ada
            if (!Schema::hasColumn('mecards', 'website')) {
                $table->string('website')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('mecards', 'address')) {
                $table->text('address')->nullable()->after('website');
            }
            
            if (!Schema::hasColumn('mecards', 'note')) {
                $table->text('note')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('mecards', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('note');
            }
            
            // Kolom JSON untuk data dinamis
            if (!Schema::hasColumn('mecards', 'phones')) {
                $table->json('phones')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('mecards', 'emails')) {
                $table->json('emails')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('mecards', 'addresses')) {
                $table->json('addresses')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('mecards', 'socials')) {
                $table->json('socials')->nullable()->after('website');
            }
            
            if (!Schema::hasColumn('mecards', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('note');
            }
        });
    }

    public function down()
    {
        Schema::table('mecards', function (Blueprint $table) {
            $columns = ['phones', 'emails', 'addresses', 'socials', 'custom_fields', 
                        'website', 'address', 'note', 'logo_path'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('mecards', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};