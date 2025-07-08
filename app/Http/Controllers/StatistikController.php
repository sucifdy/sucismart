<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StatistikController extends Controller
{
    public function index()
    {
        $aksesBerhasil = DB::table('logs')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->count();

        $aksesGagal = DB::table('logs')
            ->whereIn('event', ['RFID_DITOLAK', 'SIDIKJARI_DITOLAK'])
            ->count();

        $notifikasi = DB::table('notifikasi')->count();

        $snapshot = DB::table('logs')
            ->whereNotNull('photo')
            ->count();

        $tanggalUnik = DB::table('logs')
            ->select(DB::raw('DATE(created_at) as tanggal'))
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal');

        $labels = [];
        $dataBerhasil = [];
        $dataGagal = [];
        $dataSnapshot = [];

        foreach ($tanggalUnik as $tgl) {
            $labels[] = Carbon::parse($tgl)->format('d M Y');

            $dataBerhasil[] = DB::table('logs')
                ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
                ->whereDate('created_at', $tgl)
                ->count();

            $dataGagal[] = DB::table('logs')
                ->whereIn('event', ['RFID_DITOLAK', 'SIDIKJARI_DITOLAK'])
                ->whereDate('created_at', $tgl)
                ->count();

            $dataSnapshot[] = DB::table('logs')
                ->whereNotNull('photo')
                ->whereDate('created_at', $tgl)
                ->count();
        }

        return view('statistik', [
            'aksesBerhasil' => $aksesBerhasil,
            'aksesGagal' => $aksesGagal,
            'notifikasi' => $notifikasi,
            'snapshot' => $snapshot,
            'labels' => $labels,
            'dataBerhasil' => $dataBerhasil,
            'dataGagal' => $dataGagal,
            'dataSnapshot' => $dataSnapshot,
        ]);
    }

    public function exportPdf()
    {
        $aksesBerhasil = DB::table('logs')
            ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
            ->count();

        $aksesGagal = DB::table('logs')
            ->whereIn('event', ['RFID_DITOLAK', 'SIDIKJARI_DITOLAK'])
            ->count();

        $notifikasi = DB::table('notifikasi')->count();

        $snapshot = DB::table('logs')
            ->whereNotNull('photo')
            ->count();

        $tanggalUnik = DB::table('logs')
            ->select(DB::raw('DATE(created_at) as tanggal'))
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal');

        $labels = [];
        $dataBerhasil = [];
        $dataGagal = [];
        $dataSnapshot = [];

        foreach ($tanggalUnik as $tgl) {
            $labels[] = Carbon::parse($tgl)->format('d M Y');

            $dataBerhasil[] = DB::table('logs')
                ->whereIn('event', ['RFID_DITERIMA', 'SIDIKJARI_DITERIMA'])
                ->whereDate('created_at', $tgl)
                ->count();

            $dataGagal[] = DB::table('logs')
                ->whereIn('event', ['RFID_DITOLAK', 'SIDIKJARI_DITOLAK'])
                ->whereDate('created_at', $tgl)
                ->count();

            $dataSnapshot[] = DB::table('logs')
                ->whereNotNull('photo')
                ->whereDate('created_at', $tgl)
                ->count();
        }

        $pdf = Pdf::loadView('statistikpdf', [
            'aksesBerhasil' => $aksesBerhasil,
            'aksesGagal' => $aksesGagal,
            'notifikasi' => $notifikasi,
            'snapshot' => $snapshot,
            'labels' => $labels,
            'dataBerhasil' => $dataBerhasil,
            'dataGagal' => $dataGagal,
            'dataSnapshot' => $dataSnapshot,
        ]);

        return $pdf->download('statistik_smartlock.pdf');
    }
}
