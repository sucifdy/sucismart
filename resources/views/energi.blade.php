@extends('layouts.app')

@section('content')
<div class="px-4 py-6 bg-white dark:bg-gray-900 min-h-screen text-gray-900 dark:text-white transition-colors duration-500 overflow-auto">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header Section --}}
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-6 py-6 rounded-2xl shadow-lg">
            <div class="space-y-1">
                <h1 class="text-3xl font-black flex items-center gap-3 tracking-tight">
                    ⚡ <span>Konsumsi Energi</span>
                </h1>
                <p class="text-sm opacity-90">Pantau pemakaian energi secara efisien dan detail.</p>
            </div>
        </div>

        {{-- Stat Boxes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="p-4 rounded-xl bg-blue-700 text-white shadow-lg">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h3 class="text-sm font-semibold">Energi Hari Ini</h3>
                </div>
                <p id="energi-hari-ini" class="text-xl font-bold tracking-tight">{{ number_format($totalHariIni, 2) }} Wh</p>
            </div>

            <div class="p-4 rounded-xl bg-purple-600 text-white shadow-lg">
                <h3 class="text-sm font-semibold">Rata-rata Mingguan</h3>
                <p id="energi-mingguan" class="text-xl font-bold tracking-tight">
                    {{ $jumlahHariData == 0 ? 'Tidak ada data' : number_format($rataRataMingguan, 2) . ' Wh' }}
                </p>
            </div>

            <div class="p-4 rounded-xl bg-green-600 text-white shadow-lg">
                <h3 class="text-sm font-semibold">Penggunaan Tertinggi</h3>
                <p id="energi-tertinggi" class="text-xl font-bold tracking-tight">{{ ucfirst($perangkatTertinggi) }}</p>
            </div>

            <div class="p-4 rounded-xl bg-rose-500 text-white shadow-lg">
                <h3 class="text-sm font-semibold">Total Konsumsi Bulan Ini</h3>
                <p id="energi-bulanan" class="text-xl font-bold tracking-tight">{{ number_format($totalBulanan, 2) }} kWh</p>
            </div>
        </div>
  {{-- Filter dan Export --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6">
            <form id="filterForm" class="flex flex-wrap gap-2 items-center">
                <input type="date" id="tanggal" name="tanggal" class="px-3 py-2 rounded-md border text-sm dark:bg-gray-800 dark:text-white" />
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition font-medium">🔍 Filter</button>
            </form>

            <form action="{{ route('statistik.export.pdf.custom') }}" method="GET" class="flex flex-wrap gap-2 items-center">
                <select name="tipe" required class="px-3 py-2 rounded-md border text-sm dark:bg-gray-800 dark:text-white">
                    <option value="">Pilih Rentang</option>
                    <option value="harian">Harian</option>
                    <option value="mingguan">Mingguan</option>
                    <option value="bulanan">Bulanan</option>
                </select>
                <input type="date" name="tanggal" required class="px-3 py-2 rounded-md border text-sm dark:bg-gray-800 dark:text-white" />
                <button type="submit" class="bg-rose-600 text-white px-4 py-2 rounded-md text-sm hover:bg-rose-700 transition font-medium">🧾 Export PDF</button>
            </form>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">📈 Grafik Pemakaian Daya</h2>
                <canvas id="lineChart" class="w-full h-32"></canvas>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white mb-4 text-center">📊 Perangkat Konsumsi Tertinggi</h2>
                <div class="flex justify-center items-center h-64">
    <canvas id="pieChart" class="!w-[300px] !h-[300px]"></canvas>
</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let pieChart, lineChart;
function formatNumber(value) {
    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
    if (value >= 1000) return (value / 1000).toFixed(1) + 'K';
    return value.toFixed(0);
}
function fetchEnergiData() {
    fetch("/api/energi/statistik")
        .then(res => res.json())
        .then(data => {
            document.getElementById('energi-hari-ini').textContent = formatNumber(data.totalHariIni) + ' Wh';

            document.getElementById('energi-tertinggi').textContent = data.perangkatTertinggi;
          document.getElementById('energi-mingguan').textContent = formatNumber(data.rataRataMingguan) + ' Wh';
document.getElementById('energi-bulanan').textContent = formatNumber(data.totalBulanan) + ' kWh';

            const pieCtx = document.getElementById('pieChart').getContext('2d');
            if (pieChart) pieChart.destroy();
            pieChart = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Kipas', 'Hairdryer', 'TV', 'Lampu'],
                    datasets: [{
                        data: [
                            data.pieData.kipas,
                            data.pieData.hairdryer,
                            data.pieData.tv,
                            data.pieData.lampu
                        ],
                        backgroundColor: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '60%'
                }
            });
        });
}

function renderLineChart() {
    fetch("/api/energi/chart")
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('lineChart').getContext('2d');
            if (lineChart) lineChart.destroy();
            lineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Energi (Wh)',
                        data: data.data,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });
}

document.addEventListener('DOMContentLoaded', () => {
    fetchEnergiData();
    renderLineChart();

    setInterval(() => {
        fetchEnergiData();
        renderLineChart();
    }, 10000);

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const tanggal = document.getElementById('tanggal').value;
        if (!tanggal) return;

        fetch(`/api/energi/filter?tanggal=${tanggal}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('energi-hari-ini').textContent = (
                    data.kipas.energi +
                    data.hairdryer.energi +
                    data.tv.energi +
                    data.lampu.energi
                ).toFixed(2) + ' Wh';
            })
            .catch(err => alert('❌ Tidak ada data pada tanggal tersebut.'));
    });
});
</script>
@endsection
