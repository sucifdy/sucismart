<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('suhu_logs', function (Blueprint $table) {
            $table->id();
            $table->float('suhu');         // Kolom suhu (°C)
            $table->float('kelembaban');   // Kolom kelembaban (% RH)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suhu_logs');
    }
};
