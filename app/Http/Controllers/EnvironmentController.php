<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorLog;
use App\Models\Warning;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EnvironmentController extends Controller
{

public function show(Request $request)
{
    $warnings = Warning::where(function ($query) {
        $query->where('message', 'like', '%suhu%')
              ->orWhere('message', 'like', '%cahaya%')
              ->orWhere('message', 'like', '%gas%')
              ->orWhere('message', 'like', '%api%');
    })
    ->latest()
    ->take(10)
    ->get();

    $range = $request->query('range', 'day'); // ✅ BENAR
    $tanggal = $request->query('tanggal');

    $query = SensorLog::orderByDesc('created_at');

 if ($tanggal && $range === 'week') {
    $tanggalAwal = Carbon::parse($tanggal)->subDays(6)->startOfDay(); // mulainya dari 6 hari sebelum
    $tanggalAkhir = Carbon::parse($tanggal)->endOfDay();              // sampai akhir hari itu
    $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
} elseif ($tanggal) {
    $query->whereDate('created_at', $tanggal);
} elseif ($range === 'week') {
    $query->where('created_at', '>=', now('Asia/Jakarta')->subDays(7));
}
    $latestLogs = $query->paginate(10)->withQueryString();

    return view('lingkungan', compact('warnings', 'latestLogs'));
}

    public function latest()
    {
        $latest = SensorLog::latest()->first();

        return response()->json([
            'suhu' => $latest->suhu ?? 0,
            'kelembaban' => $latest->kelembaban ?? 0, // ✅ Tambahkan ini
            'cahaya' => $latest->cahaya ?? 0,
            'flame_detected' => (bool) $latest->flame,
            'gas_detected' => (bool) $latest->gas
        ]);
    }

public function chartData(Request $request)
{
    $range = $request->query('range', 'day');
    $tanggal = $request->query('tanggal'); // Ambil dari URL
    $now = Carbon::now('Asia/Jakarta');

    $query = SensorLog::query();

    if ($tanggal && $range === 'week') {
    $tanggalAwal = Carbon::parse($tanggal)->subDays(6)->startOfDay(); // seminggu ke belakang dari tanggal yg dipilih
    $tanggalAkhir = Carbon::parse($tanggal)->endOfDay();
    $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
} elseif ($tanggal) {
    $query->whereDate('created_at', $tanggal);
} elseif ($range === 'week') {
    $query->where('created_at', '>=', $now->copy()->subDays(7));
} else {
    $query->whereDate('created_at', $now->toDateString());
}


    $data = $query->orderBy('created_at')->get();

    return response()->json([
        'labels' => $data->pluck('created_at')->map(fn($d) => $d->format($range === 'week' ? 'd M' : 'H:i')),
        'suhu' => $data->pluck('suhu'),
        'kelembaban' => $data->pluck('kelembaban'),
        'cahaya' => $data->pluck('cahaya'),
        'flame' => $data->pluck('flame'),
        'gas' => $data->pluck('gas')
    ]);
}

    public function warnings()
    {
        $warnings = Warning::where(function ($query) {
            $query->where('message', 'like', '%suhu%')
                  ->orWhere('message', 'like', '%cahaya%')
                  ->orWhere('message', 'like', '%gas%')
                  ->orWhere('message', 'like', '%api%');
        })
        ->latest()
        ->take(10)
        ->get(['icon', 'message', 'created_at']);

        return response()->json(
            $warnings->map(function ($item) {
                return [
                    'icon' => $item->icon,
                    'message' => $item->message,
                    'created_at' => $item->created_at ? $item->created_at->format('H:i') : '--:--'
                ];
            })
        );
    }

    public function store(Request $request)
    {
        // Log data masuk untuk debug
        Log::info('📥 [ENV] Diterima:', $request->all());

        $data = $request->validate([
            'suhu' => 'required|numeric',
            'kelembaban' => 'required|numeric', // ✅ Validasi kelembaban
            'cahaya' => 'required|numeric',
            'flame' => 'required|boolean',
            'gas' => 'required|boolean',
        ]);

        SensorLog::create([
            'suhu' => $data['suhu'],
            'kelembaban' => $data['kelembaban'], // ✅ Simpan ke DB
            'cahaya' => $data['cahaya'],
            'flame' => $data['flame'],
            'gas' => $data['gas'],
        ]);

        Log::info('✅ [ENV] Disimpan ke DB:', $data);

        return response()->json(['status' => 'OK']);
    }

    public function logs()
    {
        $logs = SensorLog::orderByDesc('created_at')->take(10)->get();

        return response()->json($logs);
    }
}
