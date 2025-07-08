<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Konsumsi Energi (Interval 15 Menit)</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: sans-serif; font-size: 11px; }
        h2, p { text-align: center; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Konsumsi Energi (Interval 15 Menit)</h2>
    <p>Periode: {{ request('tanggal') ?? 'Terbaru' }}
        @if(request('tipe')) / Tipe: {{ ucfirst(request('tipe')) }} @endif
    </p>

    @php
        use Carbon\Carbon;

        $interval = 15;

        if ($logs->isEmpty()) {
            $slots = [];
        } else {
            $startOfDay = $logs->min('created_at')->copy()->startOfDay();
            $endOfDay = $logs->max('created_at')->copy()->endOfDay();

            $slots = [];
            for ($time = $startOfDay->copy(); $time < $endOfDay; $time->addMinutes($interval)) {
                $slotKey = $time->format('H:i');
                $slots[$slotKey] = [
                    'start' => $time->copy(),
                    'end' => $time->copy()->addMinutes($interval),
                    'data' => [
                        'kipas' => ['arus' => 0, 'daya' => 0, 'energi' => 0, 'count' => 0],
                        'lampu' => ['arus' => 0, 'daya' => 0, 'energi' => 0, 'count' => 0],
                        'tv' => ['arus' => 0, 'daya' => 0, 'energi' => 0, 'count' => 0],
                        'hairdryer' => ['arus' => 0, 'daya' => 0, 'energi' => 0, 'count' => 0],
                    ]
                ];
            }

            foreach ($logs as $log) {
                foreach ($slots as $key => &$slot) {
                    if ($log->created_at >= $slot['start'] && $log->created_at < $slot['end']) {
                        $device = $log->nama_perangkat;
                        if (isset($slot['data'][$device])) {
                            $slot['data'][$device]['arus'] += $log->arus;
                            $slot['data'][$device]['daya'] += $log->daya;
                            $slot['data'][$device]['energi'] += $log->energi;
                            $slot['data'][$device]['count']++;
                        }
                        break;
                    }
                }
            }
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Kipas (A / W / Wh)</th>
                <th>Lampu (A / W / Wh)</th>
                <th>TV (A / W / Wh)</th>
                <th>Hairdryer (A / W / Wh)</th>
            </tr>
        </thead>
        <tbody>
            @if (empty($slots))
                <tr>
                    <td colspan="5">Tidak ada data konsumsi energi untuk ditampilkan.</td>
                </tr>
            @else
                @foreach ($slots as $slot)
                    <tr>
                        <td>{{ $slot['start']->format('H:i') }} - {{ $slot['end']->format('H:i') }}</td>
                        @foreach (['kipas', 'lampu', 'tv', 'hairdryer'] as $device)
                            @php
                                $data = $slot['data'][$device];
                                $count = max($data['count'], 1);
                            @endphp
                            <td>
                                {{ number_format($data['arus'], 2) }} /
                                {{ number_format($data['daya'] / $count, 2) }} /
                                {{ number_format($data['energi'], 2) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>
