<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToEnergiLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('energi_logs', function (Blueprint $table) {
            $table->timestamps(); // ➕ Tambah kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('energi_logs', function (Blueprint $table) {
            $table->dropTimestamps(); // ❌ Hapus kolom created_at dan updated_at jika rollback
        });
    }
}
