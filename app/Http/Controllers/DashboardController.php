<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\SensorLog;
use App\Models\EnergiLog;

class DashboardController extends Controller
{
    public function lock()
    {
        $logs = DB::table('logs')
            ->leftJoin('users', function ($join) {
                $join->on('logs.uid', '=', 'users.uid')
                    ->orOn('logs.finger_id', '=', 'users.id');
            })
            ->select(
                'logs.id', 'logs.uid', 'logs.finger_id', 'logs.event', 'logs.source',
                'logs.photo', 'logs.created_at', 'users.name as user_name'
            )
            ->orderBy('logs.created_at', 'desc')
            ->get();

        $totalAksesBerhasil = DB::table('logs')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $totalAksesGagal = DB::table('logs')
            ->whereIn('event', ['RFID_DITOLAK', 'SIDIKJARI_DITOLAK'])
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $jumlahOrangHariIni = DB::table('logs')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->whereDate('created_at', now())
            ->count();

        $aktivitasTerakhir = DB::table('logs')
            ->orderBy('created_at', 'desc')
            ->first();

        $snapshotTerakhir = DB::table('logs')
            ->whereNotNull('photo')
            ->orderByDesc('created_at')
            ->first();

        if ($snapshotTerakhir) {
            $snapshotTerakhir->created_at = Carbon::parse($snapshotTerakhir->created_at);
        }

        $latestLingkungan = SensorLog::latest()->first();
        $suhu = $latestLingkungan->suhu ?? 0;
        $kelembaban = $latestLingkungan->kelembaban ?? 0;

        $konsumsiHariIni = EnergiLog::whereDate('created_at', now())->sum('energi');

        $rataRataEnergiQuery = EnergiLog::selectRaw('DATE(created_at) as tanggal, SUM(energi) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->get();

        $rataRataEnergi = $rataRataEnergiQuery->avg('total') ?? 0;
        $jumlahHariData = $rataRataEnergiQuery->count();

        $orangPerHari = DB::table('logs')
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->get();

        $rataRataOrang = $orangPerHari->avg('total') ?? 0;

        return view('dashboard-lock', compact(
            'logs',
            'totalAksesBerhasil',
            'totalAksesGagal',
            'jumlahOrangHariIni',
            'aktivitasTerakhir',
            'snapshotTerakhir',
            'suhu',
            'kelembaban',
            'konsumsiHariIni',
            'rataRataEnergi',
            'jumlahHariData',
            'rataRataOrang'
        ));
    }

    public function logAkses(Request $request)
    {
        $query = DB::table('logs')
            ->leftJoin('users as u1', 'logs.uid', '=', 'u1.uid')
            ->leftJoin('fingerprints', 'logs.finger_id', '=', 'fingerprints.finger_id')
            ->leftJoin('users as u2', 'fingerprints.user_id', '=', 'u2.id')
            ->select('logs.*', DB::raw('COALESCE(u1.name, u2.name) as user_name'));

        if ($request->tanggal) {
            $query->whereDate('logs.created_at', $request->tanggal);
        }

        if ($request->kategori === 'berhasil') {
            $query->whereIn('logs.event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA']);
        } elseif ($request->kategori === 'gagal') {
            $query->whereIn('logs.event', ['RFID_DITOLAK', 'SIDIKJARI_DITOLAK']);
        } elseif ($request->kategori === 'sensor') {
            $query->where('logs.event', 'like', '%sensor%');
        }

        $logs = $query->orderByDesc('logs.created_at')->paginate(20);

        $snapshotTerakhir = DB::table('logs')
            ->whereNotNull('photo')
            ->orderByDesc('created_at')
            ->first();

        $totalAksesBerhasil = DB::table('logs')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $totalAksesGagal = DB::table('logs')
            ->whereIn('event', ['RFID_DITOLAK', 'SIDIKJARI_DITOLAK'])
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $jumlahOrangHariIni = DB::table('logs')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->whereDate('created_at', now())
            ->count();

        $aktivitasTerakhir = DB::table('logs')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('log-akses', compact(
            'logs',
            'snapshotTerakhir',
            'totalAksesBerhasil',
            'totalAksesGagal',
            'jumlahOrangHariIni',
            'aktivitasTerakhir'
        ));
    }

    public function triggerSnapshot(Request $request)
    {
        try {
            Http::timeout(3)->post('http://192.168.1.7/snapshot');
            return back()->with('status', '📸 Snapshot dikirim ke ESP32-CAM!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal menghubungi ESP32-CAM');
        }
    }

    public function getLatestLogs()
    {
        $logs = DB::table('logs')
            ->leftJoin('users as u1', 'logs.uid', '=', 'u1.uid')
            ->leftJoin('fingerprints', 'logs.finger_id', '=', 'fingerprints.finger_id')
            ->leftJoin('users as u2', 'fingerprints.user_id', '=', 'u2.id')
            ->select('logs.*', DB::raw('COALESCE(u1.name, u2.name) as user_name'))
            ->orderByDesc('logs.created_at')
            ->limit(10)
            ->get();

        return response()->json($logs);
    }

    public function getSnapshot()
    {
        $snapshot = DB::table('logs')
            ->whereNotNull('photo')
            ->orderByDesc('created_at')
            ->first();

        return response()->json($snapshot);
    }

    public function keamanan()
    {
        $snapshotTerakhir = DB::table('logs')
            ->whereNotNull('photo')
            ->orderByDesc('created_at')
            ->first();

        return view('keamanan', compact('snapshotTerakhir'));
    }

    public function rataRataOrangMingguan()
    {
        $jumlahPerHari = DB::table('logs')
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->pluck('total');

        $rata = round($jumlahPerHari->sum() / 7);

        return response()->json(['rata' => $rata]);
    }

    public function jumlahOrangHariIni()
    {
        $jumlah = DB::table('logs')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->whereDate('created_at', now())
            ->count();

        return response()->json(['jumlah' => $jumlah]);
    }

  public function getChartJumlahOrang()
{
    $data = DB::table('logs')
        ->select(DB::raw("DATE(created_at) as tanggal"), DB::raw("COUNT(*) as total"))
        ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
        ->where('created_at', '>=', now()->subDays(6)) // 6 hari ke belakang + hari ini
        ->groupBy(DB::raw("DATE(created_at)"))
        ->orderBy(DB::raw("DATE(created_at)"))
        ->get();

    $labels = [];
    $jumlah = [];

    for ($i = 6; $i >= 0; $i--) {
        $tanggal = now()->subDays($i)->format('Y-m-d');
        $label = now()->subDays($i)->translatedFormat('d M'); // contoh: 01 Jul

        $found = $data->firstWhere('tanggal', $tanggal);
        $labels[] = $label;
        $jumlah[] = $found ? $found->total : 0;
    }

    return response()->json([
        'labels' => $labels,
        'data' => $jumlah
    ]);
}

public function getChartEnergiHarian()
{
    $data = DB::table('energi_logs')
        ->select(DB::raw('HOUR(created_at) as jam'), DB::raw('SUM(energi) as total'))
        ->whereDate('created_at', now())
        ->groupBy(DB::raw('HOUR(created_at)'))
        ->get();

    $kategori = [
        'Pagi' => 0,
        'Siang' => 0,
        'Sore' => 0,
        'Malam' => 0,
    ];

    foreach ($data as $row) {
        $jam = (int) $row->jam;
        if ($jam >= 0 && $jam <= 10) {
            $kategori['Pagi'] += $row->total;
        } elseif ($jam >= 11 && $jam <= 14) {
            $kategori['Siang'] += $row->total;
        } elseif ($jam >= 15 && $jam <= 17) {
            $kategori['Sore'] += $row->total;
        } else {
            $kategori['Malam'] += $row->total;
        }
    }

    return response()->json([
        'labels' => array_keys($kategori),
        'data' => array_values($kategori)
    ]);
}


}
