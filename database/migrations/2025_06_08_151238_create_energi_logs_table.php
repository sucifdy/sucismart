<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energi_logs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perangkat');
            $table->float('arus');
            $table->float('daya');
            $table->float('energi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energi_logs');
    }
};
