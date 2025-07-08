@extends('layouts.app')

@section('content')
<div class="px-3 py-2 bg-white dark:bg-gray-900 min-h-screen text-gray-900 dark:text-white text-sm">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-semibold mb-2 text-center">🌿 Pemantauan Lingkungan</h1>

        <!-- STATUS SENSOR -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-6">
            @foreach ([['🌡️','Suhu','live-suhu','-- °C'],['💧','RH','live-rh','-- %'],['🔦','Cahaya','live-cahaya','--'],['🔥','Api','live-api','--'],['💨','Gas','live-gas','--']] as [$icon,$label,$id,$val])
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3 text-center">
                <div class="text-xl">{{ $icon }}</div>
                <div class="text-xs text-gray-400">{{ $label }}</div>
                <div id="{{ $id }}" class="text-lg font-bold mt-1">{{ $val }}</div>
            </div>
            @endforeach
        </div>

        <!-- FILTER GABUNGAN -->
<form method="GET" action="{{ route('environment.show') }}" class="flex flex-wrap sm:flex-nowrap justify-end mb-4 items-center gap-2">
    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="border px-2 py-1 rounded text-xs dark:bg-gray-700 dark:text-white" />

    <select name="range" class="border px-2 py-1 rounded text-xs dark:bg-gray-700 dark:text-white">
        <option value="day" {{ request('range') == 'day' ? 'selected' : '' }}>🗕️ Harian</option>
        <option value="week" {{ request('range') == 'week' ? 'selected' : '' }}>🗓️ Mingguan</option>
    </select>

    <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-xs rounded">Terapkan</button>
</form>

        <!-- GRAFIK -->
        @foreach ([['📈 Grafik Suhu (°C)', 'suhuChart'], ['🌫️ Grafik RH (%)', 'rhChart'], ['📊 Gas, Api, Cahaya', 'barChart']] as [$title, $id])
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-5">
            <h2 class="text-sm font-semibold mb-2">{{ $title }}</h2>
            <div class="h-48"><canvas id="{{ $id }}"></canvas></div>
        </div>
        @endforeach

        <!-- TABEL SENSOR -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-3">📋 Riwayat Sensor</h2>
            <div class="overflow-x-auto max-h-[320px] overflow-y-auto">
                <table class="min-w-full text-xs text-left text-gray-600 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 text-[11px] text-gray-500 dark:text-gray-300 uppercase">
                        <tr>
                            <th class="px-3 py-2">🕒 Waktu</th>
                            <th class="px-3 py-2">🌡️ Suhu</th>
                            <th class="px-3 py-2">💧 RH</th>
                            <th class="px-3 py-2">🔦 Cahaya</th>
                            <th class="px-3 py-2">🔥 Api</th>
                            <th class="px-3 py-2">💨 Gas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800">
                        @forelse ($latestLogs as $log)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-3 py-1">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-3 py-1">{{ $log->suhu }}</td>
                            <td class="px-3 py-1">{{ $log->kelembaban }}</td>
                            <td class="px-3 py-1">{{ $log->cahaya }}</td>
                            <td class="px-3 py-1">{{ $log->flame ? '🔥 Terdeteksi' : '✅ Aman' }}</td>
                            <td class="px-3 py-1">{{ $log->gas ? '☠️ Terdeteksi' : '✅ Aman' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-gray-400 py-3">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-3 text-xs text-gray-600 dark:text-gray-300">
                {{ $latestLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let suhuChart, rhChart, barChart;

function initCharts(labels, suhu, kelembaban, cahaya, flame, gas) {
    if (suhuChart) suhuChart.destroy();
    if (rhChart) rhChart.destroy();
    if (barChart) barChart.destroy();

    suhuChart = new Chart(document.getElementById('suhuChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Suhu (°C)',
                data: suhu,
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    rhChart = new Chart(document.getElementById('rhChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'RH (%)',
                data: kelembaban,
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    barChart = new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Gas',
                    data: gas,
                    backgroundColor: gas.map(v => v > 0 ? '#dc2626' : '#f87171')
                },
                {
                    label: 'Api',
                    data: flame,
                    backgroundColor: flame.map(v => v > 0 ? '#fb923c' : '#facc15')
                },
                {
                    label: 'Cahaya',
                    data: cahaya,
                    backgroundColor: cahaya.map(v => v > 800 ? '#1e3a8a' : '#fde047')
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
}

function fetchChartData(range = 'day') {
    const urlParams = new URLSearchParams(window.location.search);
    const tanggal = urlParams.get('tanggal');
    const params = new URLSearchParams();
    if (tanggal) params.append('tanggal', tanggal);
    params.append('range', range);

    fetch(`/api/environment/chart?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            initCharts(data.labels, data.suhu, data.kelembaban, data.cahaya, data.flame, data.gas);
        });
}

function updateRealtime() {
    fetch("/api/environment/latest")
        .then(res => res.json())
        .then(data => {
            document.getElementById("live-suhu").textContent = `${data?.suhu?.toFixed(1)} °C`;
            document.getElementById("live-rh").textContent = `${Math.round(data?.kelembaban ?? 0)} %`;
            document.getElementById("live-cahaya").textContent = data?.cahaya;
            document.getElementById("live-api").textContent = data?.flame_detected ? '🔥 Terdeteksi' : '✅ Aman';
            document.getElementById("live-gas").textContent = data?.gas_detected ? '☠️ Terdeteksi' : '✅ Aman';
        });
}

document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const range = params.get("range") || "day";
    const chartRange = document.getElementById("chart-range");
    if (chartRange) chartRange.value = range;

    fetchChartData(range);
    updateRealtime();

    // Sync chart range ke hidden input
    document.getElementById('chart-range').addEventListener('change', (e) => {
        const value = e.target.value;
        fetchChartData(value);
        document.getElementById("range-hidden").value = value;
    });

    setInterval(() => {
        updateRealtime();
        fetchChartData(document.getElementById('chart-range').value);
    }, 1000);
});
</script>
@endsection
