<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← Tambahkan ini

return new class extends Migration
{
    public function up()
    {
        Schema::create('reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->json('reminder_days')->nullable(); // [7, 3, 1]
            $table->boolean('email_notification')->default(true);
            $table->boolean('system_notification')->default(true);
            $table->time('send_time')->default('08:00:00');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('reminder_settings')->insert([
            'reminder_days' => json_encode([7, 3, 1]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('reminder_settings');
    }
};