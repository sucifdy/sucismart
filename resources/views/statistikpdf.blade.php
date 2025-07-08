<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Sistem</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 30px;
            color: #111;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            color: #1E3A8A;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .summary-table {
            width: 100%;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        .summary-table td {
            width: 25%;
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
            font-size: 14px;
        }

        .summary-label {
            font-weight: bold;
            color: #444;
        }

        .summary-value {
            font-size: 16px;
            color: #000;
            margin-top: 5px;
        }

        .data-section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1E3A8A;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #1E3A8A;
            color: #fff;
            padding: 8px;
            font-size: 12px;
        }

        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

    <h1>Laporan Statistik Sistem</h1>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-label">Akses Berhasil</div>
                <div class="summary-value">{{ $aksesBerhasil }}</div>
            </td>
            <td>
                <div class="summary-label">Akses Gagal</div>
                <div class="summary-value">{{ $aksesGagal }}</div>
            </td>
            <td>
                <div class="summary-label">Notifikasi</div>
                <div class="summary-value">{{ $notifikasi }}</div>
            </td>
            <td>
                <div class="summary-label">Total Snapshot</div>
                <div class="summary-value">{{ $snapshot }}</div>
            </td>
        </tr>
    </table>

    <div class="data-section-title">📅 Data Statistik Harian</div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Akses Berhasil</th>
                <th>Akses Gagal</th>
                <th>Snapshot</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < count($labels); $i++)
                <tr>
                    <td>{{ $labels[$i] }}</td>
                    <td>{{ $dataBerhasil[$i] }}</td>
                    <td>{{ $dataGagal[$i] }}</td>
                    <td>{{ $dataSnapshot[$i] }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
    </div>

</body>
</html>
