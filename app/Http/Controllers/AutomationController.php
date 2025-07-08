<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Automation;
use App\Models\DeviceLog;
use App\Models\Warning;
use Illuminate\Support\Facades\Log;

class AutomationController extends Controller
{
    // ✅ Menampilkan halaman otomatisasi
    public function show()
    {
        return view('otomatisasi');
    }

    // ✅ Menyimpan data suhu, cahaya, dan jumlah orang ke tabel automation
    public function store(Request $request)
    {
        $validated = $request->validate([
            'suhu' => 'required|numeric',
            'cahaya' => 'required|numeric',
            'jumlah_orang' => 'required|integer',
        ]);

        $data = Automation::create($validated);

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => $data
        ], 201);
    }

    // ✅ Menyimpan log aktivitas ke tabel device_logs
    public function storeLog(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'required|string',
            'description' => 'required|string',
            'logged_at' => 'nullable|date'
        ]);

        $log = DeviceLog::create($validated);

        return response()->json([
            'message' => 'Log berhasil disimpan',
            'log' => $log
        ], 201);
    }

    // ✅ Menyimpan peringatan ke tabel warnings
    public function storeWarning(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'required|string',
            'message' => 'required|string'
        ]);

        $warning = Warning::create($validated);

        return response()->json([
            'message' => 'Warning berhasil disimpan',
            'warning' => $warning
        ], 201);
    }

    // ✅ Mengambil data terbaru untuk suhu, cahaya, dan jumlah orang
    public function latest()
    {
        $data = Automation::latest()->first();

        return response()->json([
            'suhu' => $data?->suhu ?? 0,
            'cahaya' => $data?->cahaya ?? 0,
            'jumlah_orang' => $data?->jumlah_orang ?? 0
        ]);
    }

    // ✅ Mengambil 10 log aktivitas terakhir
    public function getLog()
    {
        $logs = DeviceLog::orderByDesc('logged_at')->limit(10)->get();

        return response()->json([
            'logs' => $logs
        ]);
    }

    // ✅ Mengambil 10 peringatan terakhir
    public function getWarning()
    {
        $warnings = Warning::orderByDesc('created_at')->limit(10)->get();

        return response()->json([
            'warnings' => $warnings
        ]);
    }
}
