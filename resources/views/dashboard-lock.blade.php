@extends('layouts.app')

@section('content')
<style>
    html, body {
        overflow-x: hidden;
    }
</style>

<div id="dashboard" class="flex-1 px-2 sm:px-4 pt-0 pb-2 bg-white dark:bg-gray-900 transition-colors duration-500 text-sm min-h-[calc(100vh-64px)] overflow-y-auto">
  <div class="max-w-screen-2xl mx-auto">
        <div class="text-center mt-0.5 mb-2 animate-fadeIn">
      <div class="inline-flex flex-col items-center space-y-1.5">
        <h1 class="text-4xl sm:text-2xl font-extrabold text-blue-700 dark:text-blue-400 tracking-tight">
          Smarter Living Starts Here 🏡
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-300">
          Your smart space always within reach.
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
          ⏰ <span>Last updated: {{ now()->format('d M Y H:i') }} WIB</span>
        </p>
      </div>
    </div>

    {{-- KOTAK ATAS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5 animate-fadeIn">
        @foreach ([
            ['⚡', 'Konsumsi Energi', 'Hari ini: ' . number_format($konsumsiHariIni, 2) . ' Wh'],
            ['👥', 'Jumlah Akses Valid Hari Ini', '<span id="jumlah-orang">Hari ini: ' . $jumlahOrangHariIni . ' orang</span>'],
            ['🌡️', 'Suhu & Kelembapan', $suhu == 0 && $kelembaban == 0
                ? '<em class="text-blue-200">Sensor belum aktif</em>'
                : '<span id="suhu-box">Suhu: ' . round($suhu, 1) . '°C</span> | <span id="rh-box">RH: ' . round($kelembaban) . '%</span>'],
            ['🔒', 'Akses Terakhir', 'Terakhir akses: ' . ($aktivitasTerakhir->event ?? '-') . ' – ' . \Carbon\Carbon::parse($aktivitasTerakhir->created_at)->format('H:i')],
        ] as [$icon, $title, $desc])
        <div class="bg-blue-600 rounded-lg shadow text-white p-3 flex items-start space-x-3 min-w-0">
            <div class="text-lg">{{ $icon }}</div>
            <div class="flex-1">
                <div class="font-semibold leading-tight">{{ $title }}</div>
                <div class="text-xs text-blue-100 leading-snug">{!! $desc !!}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- STATISTIK RINGKAS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4 animate-fadeIn delay-100">
        <a href="{{ route('logakses') }}?kategori=berhasil" class="block">
            <div class="bg-violet-500 text-white rounded-lg p-3 shadow-md flex items-center space-x-3 min-w-0">
                <span class="text-xl">👥</span>
                <div>
                    <p class="text-sm">Rata-rata Akses Valid Harian</p>
                    <p id="rata-orang" class="text-base font-bold">{{ $rataRataOrang }} orang</p>
                </div>
            </div>
        </a>
        <a href="{{ route('energi') }}" class="block">
            <div class="bg-blue-500 text-white rounded-lg p-3 shadow-md flex items-center space-x-3 min-w-0">
                <span class="text-xl">⚡</span>
                <div>
                    <p class="text-sm">Rata-rata Mingguan Energi</p>
                    @if ($jumlahHariData == 0)
                        <p class="italic text-xs">Tidak ada data energi minggu ini</p>
                    @else
                        <p class="text-base font-bold">{{ number_format($rataRataEnergi, 2) }} Wh</p>
                    @endif
                </div>
            </div>
        </a>
        <a href="{{ route('logakses') }}?kategori=berhasil" class="block">
            <div class="bg-emerald-500 text-white rounded-lg p-3 shadow-md flex items-center space-x-3 min-w-0">
                <span class="text-xl">🔓</span>
                <div>
                    <p class="text-sm">Total Aktivitas Minggu Ini</p>
                    <p class="text-base font-bold">{{ $totalAksesBerhasil }} kali buka</p>
                </div>
            </div>
        </a>
        <a href="{{ route('logakses') }}?kategori=gagal" class="block">
            <div class="bg-red-500 text-white rounded-lg p-3 shadow-md flex items-center space-x-3 min-w-0">
                <span class="text-xl">⛔</span>
                <div>
                    <p class="text-sm">Akses Gagal Minggu Ini</p>
                    <p class="text-base font-bold">{{ $totalAksesGagal }} kali gagal</p>
                </div>
            </div>
        </a>
    </div>

    {{-- GRAFIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5 animate-fadeIn delay-200">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-100">📈 Jumlah Orang per Hari (Minggu Ini)</h2>
            <div class="h-48 w-full">
                <canvas id="peopleChart" class="w-full h-full"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-100">⚡ Konsumsi Energi Harian (Wh)</h2>
            <div class="h-48 w-full">
                <canvas id="energyChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let peopleChart = null;

function renderPeopleChart(labels, data) {
    const ctx = document.getElementById('peopleChart').getContext('2d');
    if (peopleChart) peopleChart.destroy();

    peopleChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Akses Valid',
                data: data,
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99,102,241,0.2)',
                pointBackgroundColor: '#6366F1',
                pointRadius: 5,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { color: '#ccc' } }
            },
            scales: {
                x: { title: { display: true, text: 'Tanggal', color: '#aaa', font: { weight: 'bold' } }, ticks: { color: '#ccc' }, grid: { color: 'rgba(200,200,200,0.1)' }},
                y: { title: { display: true, text: 'Jumlah Valid', color: '#aaa', font: { weight: 'bold' } }, beginAtZero: true, ticks: { color: '#ccc' }, grid: { color: 'rgba(200,200,200,0.1)' }}
            }
        }
    });
}

function updatePeopleChart() {
    fetch("/chart/jumlah-orang-per-hari")
        .then(res => res.json())
        .then(data => {
            if (data.labels && data.data) {
                renderPeopleChart(data.labels, data.data);
            }
        })
        .catch(err => console.warn("Gagal ambil data chart orang:", err));
}

function updateEnergyChart() {
    fetch("/chart/energi-harian")
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('energyChart').getContext('2d');

            if (!data || !data.data || data.data.length === 0) {
                ctx.font = "14px sans-serif";
                ctx.fillStyle = "#aaa";
                ctx.fillText("Belum ada data hari ini", 10, 50);
                return;
            }

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Konsumsi Energi (Wh)',
                        data: data.data,
                        backgroundColor: '#10B981',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { color: '#ccc' }},
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.raw} Wh`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Waktu', color: '#aaa', font: { weight: 'bold' } },
                            ticks: { color: '#ccc' },
                            grid: { display: false }
                        },
                        y: {
                            title: { display: true, text: 'Energi (Wh)', color: '#aaa', font: { weight: 'bold' } },
                            beginAtZero: true,
                            ticks: { color: '#ccc' },
                            grid: { color: 'rgba(200,200,200,0.1)' }
                        }
                    }
                }
            });
        })
        .catch(err => console.warn("Gagal ambil data energi:", err));
}

function updateSuhuRH() {
    fetch("/api/environment/latest")
        .then(res => res.json())
        .then(data => {
            if (data?.suhu !== undefined && data?.kelembaban !== undefined) {
                document.getElementById("suhu-box").textContent = `Suhu: ${parseFloat(data.suhu).toFixed(1)}°C`;
                document.getElementById("rh-box").textContent = `RH: ${Math.round(data.kelembaban)}%`;
            }
        })
        .catch(err => console.warn("Gagal update suhu:", err));
}

function updateJumlahOrang() {
    fetch("/api/chart/jumlah-orang-per-hari")
        .then(res => res.json())
        .then(data => {
            if (data?.jumlah !== undefined) {
                document.getElementById("jumlah-orang").textContent = `Hari ini: ${data.jumlah} orang`;
            }
        })
        .catch(err => console.warn("Gagal update jumlah orang:", err));
}

function updateRataRataOrang() {
    fetch("/api/rata-rata-orang")
        .then(res => res.json())
        .then(data => {
            if (data?.rata !== undefined) {
                document.getElementById("rata-orang").textContent = `${data.rata} orang`;
            }
        })
        .catch(err => console.warn("Gagal update rata-rata orang:", err));
}

updateSuhuRH(); setInterval(updateSuhuRH, 3000);
updateJumlahOrang(); setInterval(updateJumlahOrang, 3000);
updateRataRataOrang(); setInterval(updateRataRataOrang, 3000);
updatePeopleChart(); setInterval(updatePeopleChart, 10000);
updateEnergyChart();
</script>
@endsection
