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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();        // UID RFID (boleh kosong)
            $table->integer('finger_id')->nullable(); // ID fingerprint (boleh kosong)
            $table->string('event');                  // Contoh: RFID_DITOLAK, SIDIKJARI_DITERIMA, GETARAN
            $table->string('source')->nullable();     // Contoh: ARDUINO, SENSOR, GETARAN
            $table->string('photo')->nullable();      // Path ke foto (kalau ada)
            $table->timestamps();                     // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
