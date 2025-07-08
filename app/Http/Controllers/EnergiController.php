<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\EnergiLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class EnergiController extends Controller {

    public function index()
    {
        $today = now()->toDateString();

        $logsHariIni = EnergiLog::whereDate('created_at', $today)->get();
        $perPerangkat = $logsHariIni->groupBy('nama_perangkat')->map(function ($items) {
            return $items->sum('energi');
        });

        $totalHariIni = $logsHariIni->sum('energi');

        $mingguan = EnergiLog::selectRaw('DATE(created_at) as tanggal, SUM(energi) as total')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('tanggal')
            ->get();

        $rataRataMingguan = round($mingguan->avg('total'), 2);
        $jumlahHariData = $mingguan->count();

        $perangkatTertinggi = $perPerangkat->sortDesc()->keys()->first() ?? '-';
        $totalHariIni = EnergiLog::whereDate('created_at', $today)->sum('energi');

$totalBulanan = EnergiLog::whereYear('created_at', now()->year)
                         ->whereMonth('created_at', now()->month)
                         ->sum('energi');

$totalBulanan = round($totalBulanan / 1000, 2); // dalam kWh

        return view('energi', compact(
            'totalHariIni',
            'rataRataMingguan',
            'jumlahHariData',
            'perangkatTertinggi',
            'totalBulanan',
            'perPerangkat'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kipas.arus' => 'required|numeric',
            'kipas.daya' => 'required|numeric',
            'kipas.energi' => 'required|numeric',
            'lampu.arus' => 'required|numeric',
            'lampu.daya' => 'required|numeric',
            'lampu.energi' => 'required|numeric',
            'tv.arus' => 'required|numeric',
            'tv.daya' => 'required|numeric',
            'tv.energi' => 'required|numeric',
            'hairdryer.arus' => 'required|numeric',
            'hairdryer.daya' => 'required|numeric',
            'hairdryer.energi' => 'required|numeric',
        ]);

        $finalData = [];

        foreach ($validated as $nama => $data) {
            $last = EnergiLog::where('nama_perangkat', $nama)->latest()->first();
            $delta = $data['energi'] - ($last->energi ?? 0);

            if ($delta < 0 || $delta > 5000) $delta = 0;

            EnergiLog::create([
                'nama_perangkat' => $nama,
                'arus' => $data['arus'],
                'daya' => $data['daya'],
                'energi' => $delta,
            ]);

            $finalData[$nama] = [
                'arus' => $data['arus'],
                'daya' => $data['daya'],
                'energi' => $delta
            ];
        }

        Cache::put('energi_realtime', $finalData, now()->addSeconds(20));

        Log::info('[ENERGI] Delta disimpan', $finalData);
        return response()->json(['message' => 'Data delta energi disimpan']);
    }

    public function latest()
    {
        $data = Cache::get('energi_realtime');

        if (!$data) {
            Log::warning('[ENERGI] Cache kosong, fallback ke database');

            $logs = EnergiLog::latest()->take(20)->get()->groupBy('nama_perangkat');
            $data = [
                'kipas'     => $this->formatEnergiData($logs, 'kipas'),
                'hairdryer' => $this->formatEnergiData($logs, 'hairdryer'),
                'tv'        => $this->formatEnergiData($logs, 'tv'),
                'lampu'     => $this->formatEnergiData($logs, 'lampu'),
            ];
        }

        return response()->json($data);
    }

    private function formatEnergiData($logs, $device)
    {
        return [
            'energi' => $logs[$device]->sum('energi') ?? 0,
            'daya'   => $logs[$device]->avg('daya') ?? 0,
            'arus'   => $logs[$device]->avg('arus') ?? 0,
        ];
    }

    public function energiHariIni()
    {
        $today = now()->toDateString();
        $logs = EnergiLog::whereDate('created_at', $today)->get();
        $total = $logs->sum('energi');
        return response()->json(['total_hari_ini' => round($total, 2)]);
    }

    public function rataRataMingguan()
    {
        $logs = EnergiLog::selectRaw('DATE(created_at) as tanggal, SUM(energi) as total')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('tanggal')
            ->get();

        $avg = $logs->avg('total') ?? 0;
        return response()->json(['rata_rata' => round($avg, 2)]);
    }

    public function chartData()
    {
        $logs = EnergiLog::selectRaw('DATE(created_at) as tanggal, SUM(energi) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get()
            ->reverse();

        return response()->json([
            'labels' => $logs->pluck('tanggal')->map(fn($tgl) => date('D', strtotime($tgl))),
            'data'   => $logs->pluck('total')
        ]);
    }

    public function filterByTanggal(Request $request)
    {
        $tanggal = $request->input('tanggal');

        if (!$tanggal) {
            return response()->json(['message' => 'Tanggal tidak boleh kosong'], 400);
        }

        $start = Carbon::parse($tanggal)->startOfDay();
        $end = Carbon::parse($tanggal)->endOfDay();

        $logs = EnergiLog::whereBetween('created_at', [$start, $end])->get();

        if ($logs->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data untuk tanggal ini'], 404);
        }

        $data = $logs->groupBy('nama_perangkat')->map(function ($items) {
            return [
                'energi' => $items->sum('energi'),
                'daya'   => $items->avg('daya'),
                'arus'   => $items->avg('arus'),
            ];
        });

        return response()->json([
            'kipas'     => $data['kipas'] ?? ['energi' => 0, 'daya' => 0, 'arus' => 0],
            'hairdryer' => $data['hairdryer'] ?? ['energi' => 0, 'daya' => 0, 'arus' => 0],
            'tv'        => $data['tv'] ?? ['energi' => 0, 'daya' => 0, 'arus' => 0],
            'lampu'     => $data['lampu'] ?? ['energi' => 0, 'daya' => 0, 'arus' => 0],
        ]);
    }

    public function statistik()
{
    $today = now()->toDateString();

    // Total energi hari ini
    $totalHariIni = EnergiLog::whereDate('created_at', $today)->sum('energi');

    // Data minggu ini untuk pie chart dan rata-rata
    $logsMingguan = EnergiLog::where('created_at', '>=', now()->subDays(6))->get();

    $perPerangkat = $logsMingguan->groupBy('nama_perangkat')->map(function ($items) {
        return $items->sum('energi');
    });

    $mingguan = EnergiLog::selectRaw('DATE(created_at) as tanggal, SUM(energi) as total')
        ->where('created_at', '>=', now()->subDays(6))
        ->groupBy('tanggal')
        ->get();

    $rataRataMingguan = round($mingguan->avg('total'), 2);
    $perangkatTertinggi = $perPerangkat->sortDesc()->keys()->first() ?? '-';

    // ✅ Perbaikan total bulanan
    $totalBulanan = EnergiLog::whereYear('created_at', now()->year)
                             ->whereMonth('created_at', now()->month)
                             ->sum('energi');
    $totalBulanan = round($totalBulanan / 1000, 2); // kWh

    return response()->json([
        'totalHariIni' => round($totalHariIni, 2),
        'rataRataMingguan' => $rataRataMingguan,
        'perangkatTertinggi' => ucfirst($perangkatTertinggi),
        'totalBulanan' => $totalBulanan,
        'pieData' => [
            'kipas'     => $perPerangkat['kipas'] ?? 0,
            'hairdryer' => $perPerangkat['hairdryer'] ?? 0,
            'tv'        => $perPerangkat['tv'] ?? 0,
            'lampu'     => $perPerangkat['lampu'] ?? 0,
        ]
    ]);
}


    public function exportPdf()
    {
        $logs = EnergiLog::latest()->take(50)->get();
        $pdf = Pdf::loadView('energilog', compact('logs'))->setPaper('A4', 'portrait');
        return $pdf->download('laporan_energi.pdf');
    }

    public function exportCustomPdf(Request $request)
    {
        ignore_user_abort(true);
        set_time_limit(300);
        ini_set('memory_limit', '1024M');

        $request->validate([
            'tipe' => 'required|in:harian,mingguan,bulanan',
            'tanggal' => 'required|date',
        ]);

        $tipe = $request->tipe;
        $tanggal = Carbon::parse($request->tanggal);
        $query = EnergiLog::query();

        if ($tipe === 'harian') {
            $query->whereBetween('created_at', [$tanggal->startOfDay(), $tanggal->endOfDay()]);
        } elseif ($tipe === 'mingguan') {
            $query->whereBetween('created_at', [$tanggal->startOfWeek(), $tanggal->endOfWeek()]);
        } elseif ($tipe === 'bulanan') {
            $query->whereYear('created_at', $tanggal->year)
                  ->whereMonth('created_at', $tanggal->month);
        }

        $logs = $query->orderBy('created_at', 'asc')->get();

        if ($logs->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk rentang tersebut.');
        }

        try {
            $pdf = Pdf::loadView('energilog', compact('logs'))->setPaper('A4', 'portrait');
            return $pdf->download("laporan_energi_{$tipe}_" . now()->format('Ymd_His') . ".pdf");
        } catch (\Exception $e) {
            Log::error('Gagal render PDF: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat PDF.');
        }
    }

}
