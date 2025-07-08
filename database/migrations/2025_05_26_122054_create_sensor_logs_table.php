<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('sensor_logs', function (Blueprint $table) {
    $table->id();
    $table->float('suhu')->nullable();
    $table->integer('cahaya')->nullable();
    $table->boolean('flame')->default(false);
    $table->boolean('gas')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_logs');
    }
};
