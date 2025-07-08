<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // nama: kipas, lampu, dll
            $table->boolean('status')->default(false); // false = OFF, true = ON
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('devices');
    }
};
