<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceLog;

class DeviceController extends Controller
{
    /**
     * ✅ POST /api/device/{device}/{action}
     * Menerima perintah ON/OFF manual dari web
     */
    public function control($device, $action)
    {
        Log::info("🔘 Masuk DeviceController::control() dengan: $device $action");

        $validDevices = ['lampu', 'kipas', 'tv', 'hairdryer'];
        $validActions = ['on', 'off'];

        if (!in_array($device, $validDevices) || !in_array($action, $validActions)) {
            Log::warning("❌ Perintah tidak valid: {$device} - {$action}");
            return response()->json(['error' => 'Invalid device or action'], 400);
        }

        $command = $device . '_' . $action;
        $path = storage_path('app/perintah.txt');

        try {
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }

            file_put_contents($path, $command);
            Log::info("✅ Perintah DITULIS ke: {$path} | Isi: {$command}");

            DeviceLog::create([
                'icon' => '🔌',
                'description' => "Perangkat {$device} di-{$action} secara manual oleh pengguna",
                'logged_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Gagal menulis perintah: " . $e->getMessage());
            return response()->json(['error' => 'Gagal menulis perintah'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Perintah berhasil dikirim',
            'command' => $command
        ]);
    }

    /**
     * ✅ POST /api/device/relay-status
     * Disimpan oleh ESP32 (status terbaru dari Arduino)
     */
    public function updateRelayStatus(Request $request)
    {
        $relay = $request->input('relay_status', []);

        foreach ($relay as $nama => $status) {
            DB::table('devices')->updateOrInsert(
                ['nama' => $nama],
                ['status' => $status, 'updated_at' => now()]
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * ✅ GET /api/device/status
     * Dibaca oleh Flask (mengambil & hapus perintah)
     */
    public function status()
    {
        $path = storage_path('app/perintah.txt');
        $command = null;
        $timestamp = null;

        if (file_exists($path)) {
            $command = trim(file_get_contents($path));
            $timestamp = filemtime($path);
            unlink($path);
        }

        $relay_status = [
            'lampu' => ['on' => str_contains($command, 'lampu_on'), 'on_time' => $timestamp ?? time()],
            'kipas' => ['on' => str_contains($command, 'kipas_on'), 'on_time' => $timestamp ?? time()],
            'tv' => ['on' => str_contains($command, 'tv_on'), 'on_time' => $timestamp ?? time()],
            'hairdryer' => ['on' => str_contains($command, 'hairdryer_on'), 'on_time' => $timestamp ?? time()],
        ];

        return response()->json([
            'command' => $command ?: null,
            'relay_status' => $relay_status
        ]);
    }

    /**
     * ✅ GET /reset-perintah
     * Untuk reset file command.txt manual
     */
    public function reset()
    {
        $path = storage_path('app/perintah.txt');

        if (file_exists($path)) {
            unlink($path);
            Log::info("🔁 File perintah.txt berhasil dihapus manual");
        }

        return response()->json([
            'success' => true,
            'message' => 'Perintah berhasil direset'
        ]);
    }

    /**
     * ✅ GET /api/device/relay-status
     * Diambil oleh Blade UI untuk sinkronisasi toggle
     */
    public function getRelayStatus()
    {
        $rows = DB::table('devices')->select('nama', 'status')->get();

        $relayStatus = [];
        foreach ($rows as $row) {
            $relayStatus[$row->nama] = (bool) $row->status;
        }

        return response()->json(['relay_status' => $relayStatus]);
    }
}
