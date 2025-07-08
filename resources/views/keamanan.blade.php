@extends('layouts.app')

@section('content')
<div class="px-6 py-10 bg-white dark:bg-gray-900 min-h-screen transition-colors duration-500 text-gray-900 dark:text-white">
    <div class="max-w-7xl mx-auto space-y-10">

        {{-- LOG AKSES DAN SNAPSHOT --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Snapshot Wajah --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow space-y-3">
                <h3 class="font-semibold text-blue-800 dark:text-blue-300 text-lg">📸 Snapshot Akses</h3>
                <img id="snapshot-image" src="/images/last_snapshot.jpg" alt="Snapshot" class="rounded-lg w-full h-40 object-cover bg-gray-300 dark:bg-gray-700" />
                <p id="snapshot-time" class="text-xs text-gray-500 dark:text-gray-400">Terakhir: -</p>
                <button class="text-sm font-bold text-blue-700 dark:text-blue-300 hover:underline">Lihat Semua</button>
            </div>

            {{-- Notifikasi Keamanan --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow space-y-3">
                <h3 class="font-semibold text-blue-800 dark:text-blue-300 text-lg">🔐 Notifikasi Keamanan</h3>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <li class="py-2 flex justify-between">
                        <span>Getaran mencurigakan pukul 23:41</span>
                        <span class="text-red-500 font-bold">🚨</span>
                    </li>
                    <li class="py-2 flex justify-between">
                        <span>Akses gagal oleh fingerprint ID: 07</span>
                        <span class="text-yellow-500 font-bold">❌</span>
                    </li>
                    <li class="py-2 flex justify-between">
                        <span>Akses berhasil oleh UID: 8391DA2C</span>
                        <span class="text-green-500 font-bold">✅</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Log Aktivitas --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow space-y-3 max-h-80 overflow-y-auto">
            <h3 class="font-semibold text-blue-800 dark:text-blue-300 text-lg">🗂️ Log Akses & Snapshot</h3>
            <ul class="text-sm space-y-2">
                <li>⏱️ 06:45 - Kamera aktif</li>
                <li>👤 07:00 - UID 8391DA2C Akses diterima</li>
                <li>🔒 07:01 - Pintu Terkunci</li>
                <li>📸 07:02 - Snapshot wajah diambil</li>
            </ul>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function fetchSnapshot() {
        fetch("{{ route('api.snapshot') }}")
            .then(res => res.json())
            .then(data => {
                if (data?.photo) {
                    document.getElementById('snapshot-image').src = `/storage/photos/${data.photo}?${Date.now()}`
                    document.getElementById('snapshot-time').textContent = `Terakhir: ${new Date(data.created_at).toLocaleTimeString('id-ID')} WIB`
                }
            })
    }
    setInterval(fetchSnapshot, 5000)
</script>
@endsection
