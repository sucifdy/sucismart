@extends('layouts.app')

@section('title', 'Statistik')

@section('content')
<style>
    .chart-container {
        position: relative;
        height: 200px;
    }
</style>

<div class="p-6 text-gray-800 dark:text-white">
    <h1 class="text-2xl font-bold text-blue-900 dark:text-blue-300 mb-4">📊 Statistik Sistem</h1>

    {{-- Tombol Ekspor PDF --}}
    <a href="{{ route('statistik.export.pdf') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow mb-6 inline-block">
       📄 Ekspor PDF
    </a>

    {{-- Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <p class="text-gray-500 dark:text-gray-300">Akses Berhasil</p>
            <h2 class="text-3xl font-semibold text-green-600">{{ $aksesBerhasil }}</h2>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <p class="text-gray-500 dark:text-gray-300">Akses Gagal</p>
            <h2 class="text-3xl font-semibold text-red-600">{{ $aksesGagal }}</h2>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <p class="text-gray-500 dark:text-gray-300">Notifikasi Keamanan</p>
            <h2 class="text-3xl font-semibold text-yellow-500">{{ $notifikasi }}</h2>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <p class="text-gray-500 dark:text-gray-300">Total Snapshot</p>
            <h2 class="text-3xl font-semibold text-blue-500">{{ $snapshot }}</h2>
        </div>
    </div>

    {{-- Grafik Akses --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-2">Grafik Akses Seluruh Waktu</h2>
        <div class="chart-container">
            <canvas id="aksesChart"></canvas>
        </div>
    </div>

    {{-- Grafik Snapshot --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-2">Grafik Snapshot Wajah</h2>
        <div class="chart-container">
            <canvas id="snapshotChart"></canvas>
        </div>
    </div>

    {{-- Tabel Statistik Harian --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow mt-6 overflow-x-auto">
        <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-2">📋 Data Harian</h2>
        <table class="min-w-full text-sm text-center border border-gray-300 dark:border-gray-600">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="px-4 py-2 border">Tanggal</th>
                    <th class="px-4 py-2 border">Akses Berhasil</th>
                    <th class="px-4 py-2 border">Akses Gagal</th>
                    <th class="px-4 py-2 border">Snapshot</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < count($labels); $i++)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                        <td class="border px-4 py-2 font-semibold text-gray-800 dark:text-white">{{ $labels[$i] }}</td>
                        <td class="border px-4 py-2 text-green-600">{{ $dataBerhasil[$i] }}</td>
                        <td class="border px-4 py-2 text-red-600">{{ $dataGagal[$i] }}</td>
                        <td class="border px-4 py-2 text-blue-600">{{ $dataSnapshot[$i] }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = {!! json_encode($labels) !!};
    const isDark = document.documentElement.classList.contains('dark');
    const fontColor = isDark ? '#ffffff' : '#000000';

    new Chart(document.getElementById('aksesChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Berhasil',
                    data: {!! json_encode($dataBerhasil) !!},
                    backgroundColor: 'rgba(34,197,94,0.7)'
                },
                {
                    label: 'Gagal',
                    data: {!! json_encode($dataGagal) !!},
                    backgroundColor: 'rgba(239,68,68,0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { color: fontColor } },
                y: { beginAtZero: true, ticks: { color: fontColor } }
            },
            plugins: {
                legend: { labels: { color: fontColor } }
            }
        }
    });

    new Chart(document.getElementById('snapshotChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Snapshot',
                data: {!! json_encode($dataSnapshot) !!},
                backgroundColor: 'rgba(59,130,246,0.2)',
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { color: fontColor } },
                y: { beginAtZero: true, ticks: { color: fontColor } }
            },
            plugins: {
                legend: { labels: { color: fontColor } }
            }
        }
    });
</script>
@endsection
