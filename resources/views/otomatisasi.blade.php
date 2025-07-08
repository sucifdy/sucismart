@extends('layouts.app')

@section('content')
<div class="px-4 py-6 text-gray-900 dark:text-white">
    <div class="space-y-10">
        <div class="text-center">
            <h1 class="text-2xl font-bold tracking-tight text-blue-900 dark:text-blue-300">⚙ Otomatisasi & Kontrol Perangkat</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pantau suhu, cahaya, dan kehadiran secara real-time, serta kontrol perangkat secara manual.</p>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-800 shadow border border-blue-300 dark:border-blue-700 overflow-hidden">
            <div class="p-3 bg-blue-700 dark:bg-blue-800">
                <h2 class="text-base font-semibold text-white">🧍 Deteksi Kehadiran (YOLO)</h2>
            </div>
            <div class="h-60 bg-black">
                <img src="http://192.168.137.172:5000/video" class="w-full h-full object-contain" alt="Live YOLO Feed" />
            </div>
            <div class="p-3 text-sm text-gray-700 dark:text-white bg-blue-50 dark:bg-blue-900">
                Orang terdeteksi: <span id="jumlah_orang" class="font-bold text-blue-600 dark:text-blue-300">-</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                <h2 class="text-base font-medium text-blue-900 dark:text-blue-200 mb-1">🌡 Suhu Ruangan</h2>
                <p class="text-3xl font-bold text-blue-700 dark:text-blue-400">
                    <span id="suhu">--</span>°C
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                <h2 class="text-base font-medium text-yellow-700 dark:text-yellow-300 mb-1">💡 Intensitas Cahaya</h2>
                <p class="text-3xl font-bold text-yellow-500">
                    <span id="cahaya">--</span>
                </p>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-xl font-semibold mb-4 text-blue-800 dark:text-blue-300 text-center">🔌 Kontrol Manual Perangkat</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ([['🌀', 'Kipas', 'kipas'], ['💇‍♀', 'Hairdryer', 'hairdryer'], ['📺', 'Televisi', 'tv'], ['💡', 'Lampu', 'lampu']] as [$icon, $label, $id])
                <div class="bg-gradient-to-br from-blue-700 to-blue-600 dark:from-blue-800 dark:to-blue-700 rounded-2xl p-4 shadow flex flex-col items-center text-white">
                    <div class="text-3xl mb-1">{{ $icon }}</div>
                    <h3 class="text-sm font-medium">{{ $label }}</h3>
                    <p class="text-xs text-blue-100 mb-2">Status: <span class="italic">Terkoneksi</span></p>
                    <label class="relative inline-flex items-center cursor-pointer mt-auto">
                        <input type="checkbox" id="relay-{{ $id }}" onchange="togglePerangkat(this, '{{ $id }}')" class="sr-only peer"
    {{ isset($relayStatus[$id]) && $relayStatus[$id] ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-400 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:bg-lime-500 transition-all"></div>
                        <div class="absolute left-0.5 top-0.5 bg-white border border-gray-300 rounded-full h-4 w-4 peer-checked:translate-x-full transition-transform"></div>
                    </label>
                    <span id="status-{{ $id }}" class="text-xs mt-1 text-white">Status: OFF</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 rounded-2xl shadow p-4">
            <h2 class="text-xl font-semibold mb-2 text-blue-800 dark:text-blue-300 flex items-center gap-2">🗂 Log Aktivitas & Riwayat</h2>
            <div id="log-list" class="max-h-60 overflow-y-auto pr-2">
                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-200"></ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function fetchAutomation() {
    fetch("{{ route('api.automation.latest') }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById("suhu").textContent = data?.suhu ?? "--";
            document.getElementById("cahaya").textContent = data?.cahaya ?? "--";
            document.getElementById("jumlah_orang").textContent = data?.jumlah_orang ?? "-";
        });
}

function fetchLogs() {
    fetch("{{ route('api.log.latest') }}")
        .then(res => res.json())
        .then(data => {
            const list = document.querySelector("#log-list ul");
            list.innerHTML = "";
            if (!data.logs || data.logs.length === 0) {
                list.innerHTML = `<li class="italic text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas</li>`;
                return;
            }
            data.logs.forEach(log => {
                list.innerHTML += `
                    <li class="flex items-start gap-3">
                        <span class="text-xl">${log.icon}</span>
                        <span title="${log.logged_at}">
                          ${new Date(log.logged_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} - ${log.description}
                        </span>
                    </li>`;
            });
        });
}

async function fetchRelayStatusFromDatabase() {
    const res = await fetch("/api/device/relay-status");
    const data = await res.json();
    const relay = data?.relay_status ?? {};

    Object.entries(relay).forEach(([key, val]) => {
        const toggle = document.getElementById(`relay-${key}`);
        const label = document.getElementById(`status-${key}`);
        if (toggle && toggle.checked !== !!val) {
            toggle.checked = !!val;
        }
        if (label) label.textContent = `Status: ${val ? 'ON' : 'OFF'}`;
    });
}

function togglePerangkat(elem, deviceId) {
    const action = elem.checked ? 'on' : 'off';

    fetch(`/api/device/${deviceId}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(() => {
        document.getElementById(`status-${deviceId}`).textContent = `Status: ${action.toUpperCase()}`;
        fetch("/log", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                icon: "🔌",
                description: `Perangkat ${deviceId} diaktifkan (${action.toUpperCase()}) secara manual oleh pengguna`,
                logged_at: new Date().toISOString()
            })
        });

        fetch("/api/device/relay-status", {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                relay_status: {
                    [deviceId]: (action === 'on')
                }
            })
        });
    })
    .catch(err => {
        console.error('❌ Gagal mengirim perintah perangkat:', err);
        elem.checked = !elem.checked;
    });
}

setInterval(() => {
    fetchAutomation();
    fetchLogs();
    fetchRelayStatusFromDatabase();
}, 3000);

document.addEventListener('DOMContentLoaded', () => {
    fetchAutomation();
    fetchLogs();
    fetchRelayStatusFromDatabase();
});
</script>
@endsection
