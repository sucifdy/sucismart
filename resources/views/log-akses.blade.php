@extends('layouts.app')

@section('content')
<div class="px-6 py-8 bg-white dark:bg-gray-900 min-h-screen text-gray-900 dark:text-white transition-colors duration-500">
    <div class="w-full space-y-6">

        {{-- Judul --}}
        <h1 class="text-xl font-semibold text-blue-800 dark:text-blue-300">🗂️ Histori Log Akses</h1>

        {{-- FILTER --}}
        <div class="flex flex-wrap gap-2 items-center text-sm">
            <input type="date" id="filterDate" class="bg-white text-blue-900 rounded px-3 py-1.5 shadow focus:outline-none focus:ring focus:ring-blue-200" />
            <select id="filterKategori" class="bg-white text-blue-900 rounded px-3 py-1.5 shadow focus:outline-none focus:ring focus:ring-blue-200">
                <option value="">Semua Kategori</option>
                <option value="berhasil">Akses Berhasil</option>
                <option value="gagal">Akses Gagal</option>
                <option value="sensor">Sensor Terdeteksi</option>
            </select>
            <button onclick="filterLog()" class="bg-lime-400 hover:bg-lime-500 text-blue-900 font-semibold px-3 py-1.5 rounded shadow text-sm">
                Filter 🔍
            </button>
        </div>

        {{-- TABEL LOG --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-4 overflow-x-auto">
            <table class="min-w-full text-xs text-left text-gray-700 dark:text-gray-200">
                <thead class="bg-gray-100 dark:bg-gray-700 font-medium">
                    <tr>
                        <th class="px-3 py-2">Waktu</th>
                        <th class="px-3 py-2">UID</th>
                        <th class="px-3 py-2">Fingerprint</th>
                        <th class="px-3 py-2">Event</th>
                        <th class="px-3 py-2">User</th>
                        <th class="px-3 py-2">Foto</th>
                    </tr>
                </thead>
                <tbody id="log-body">
                    @foreach ($logs as $log)
                    <tr class="border-b dark:border-gray-600">
                        <td class="px-3 py-1.5">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</td>
                        <td class="px-3 py-1.5">{{ $log->uid ?? '-' }}</td>
                        <td class="px-3 py-1.5">{{ $log->finger_id ?? '-' }}</td>
                        <td class="px-3 py-1.5">
                            <span class="px-2 py-0.5 rounded-lg font-medium
                                {{ Str::contains($log->event, 'DITERIMA') ? 'bg-green-200 text-green-700' : (Str::contains($log->event, 'DITOLAK') ? 'bg-red-200 text-red-700' : 'bg-yellow-200 text-yellow-800') }}">
                                {{ $log->event }}
                            </span>
                        </td>
                        <td class="px-3 py-1.5">{{ $log->user_name ?? '-' }}</td>
                        <td class="px-3 py-1.5">
                            @if ($log->photo)
                                <img src="{{ asset('storage/photos/' . $log->photo) }}"
                                     onclick="openModal('{{ asset('storage/photos/' . $log->photo) }}')"
                                     class="h-10 w-10 object-cover rounded shadow cursor-pointer hover:ring-2 hover:ring-blue-400 transition"
                                     alt="snapshot">
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-2">{{ $logs->links() }}</div>
        </div>

        {{-- SNAPSHOT & RINGKASAN --}}
        <div class="grid md:grid-cols-2 gap-5">
            {{-- Snapshot --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4" id="snapshot">
                <h3 class="text-base font-semibold text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-2">
                    📸 Snapshot Akses
                    <a href="{{ route('log.akses') }}#snapshot"
                       class="text-xs text-blue-600 dark:text-blue-400 hover:underline ml-auto">
                       Lihat Riwayat
                    </a>
                </h3>

                @if ($snapshotTerakhir && $snapshotTerakhir->photo)
                    <div class="relative rounded overflow-hidden border border-gray-300 dark:border-gray-600 shadow-sm">
                        <img id="snapshot-image"
                             src="{{ asset('storage/photos/' . $snapshotTerakhir->photo) }}"
                             onclick="openModal('{{ asset('storage/photos/' . $snapshotTerakhir->photo) }}')"
                             class="cursor-pointer w-full h-48 object-cover bg-gray-200 dark:bg-gray-700 hover:opacity-90 transition"
                             alt="Snapshot Akses">
                        <div id="snapshot-time"
                             class="absolute bottom-0 left-0 bg-black bg-opacity-60 text-white text-xs px-3 py-1">
                            Terakhir: {{ \Carbon\Carbon::parse($snapshotTerakhir->created_at)->format('d/m/Y H:i:s') }} WIB
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Snapshot ini diambil otomatis saat akses berhasil atau ditolak.
                    </p>
                @else
                    <p class="text-sm text-gray-400 italic">Belum ada snapshot yang tersedia.</p>
                @endif
            </div>

            {{-- Ringkasan --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <h3 class="text-base font-semibold text-blue-800 dark:text-blue-300 mb-3">📊 Ringkasan Akses</h3>
                <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-200">
                    <li>✅ <strong>{{ $totalAksesBerhasil }}</strong> Akses berhasil (7 hari terakhir)</li>
                    <li>❌ <strong>{{ $totalAksesGagal }}</strong> Akses ditolak (7 hari terakhir)</li>
                    <li>🧍 <strong>{{ $jumlahOrangHariIni }}</strong> Akses tercatat hari ini</li>
                    <li>🕒 Terakhir aktif:
                        <strong>
                            {{ $aktivitasTerakhir ? \Carbon\Carbon::parse($aktivitasTerakhir->created_at)->format('d/m/Y H:i:s') : '-' }}
                        </strong>
                    </li>
                </ul>
            </div>
        </div>

        {{-- MODAL GAMBAR --}}
        <div id="photoModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden items-center justify-center">
            <div class="relative bg-white rounded-lg shadow-lg overflow-hidden">
                <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">×</button>
                <img id="modalImage" src="" alt="Preview" class="w-auto max-w-4xl max-h-[90vh] rounded shadow-lg object-contain">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('photoModal').classList.remove('hidden');
        document.getElementById('photoModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('photoModal').classList.add('hidden');
        document.getElementById('photoModal').classList.remove('flex');
        document.getElementById('modalImage').src = '';
    }

    document.getElementById('photoModal').addEventListener('click', function (e) {
        if (e.target.id === 'photoModal') closeModal();
    });

    function fetchLogs() {
        const currentUrl = new URL(window.location.href);
        const page = currentUrl.searchParams.get("page");
        if (page && page !== "1") return;

        fetch("{{ route('api.log') }}")
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('log-body');
                tbody.innerHTML = '';
                data.forEach(log => {
                    const createdAt = new Date(log.created_at);
                    const eventClass = log.event.includes('DITERIMA') ? 'bg-green-200 text-green-700'
                                      : log.event.includes('DITOLAK') ? 'bg-red-200 text-red-700'
                                      : 'bg-yellow-200 text-yellow-800';

                    const row = `
                        <tr class="border-b dark:border-gray-600">
                            <td class="px-3 py-1.5">${createdAt.toLocaleString()}</td>
                            <td class="px-3 py-1.5">${log.uid ?? '-'}</td>
                            <td class="px-3 py-1.5">${log.finger_id ?? '-'}</td>
                            <td class="px-3 py-1.5"><span class="px-2 py-0.5 rounded-lg font-medium ${eventClass}">${log.event}</span></td>
                            <td class="px-3 py-1.5">${log.user_name ?? '-'}</td>
                            <td class="px-3 py-1.5">
                                ${log.photo ? `<img src='/storage/photos/${log.photo}' onclick="openModal('/storage/photos/${log.photo}')" class='h-10 w-10 object-cover rounded shadow cursor-pointer hover:ring-2 hover:ring-blue-400 transition' alt='snapshot'>` : '<span class="text-gray-400 italic">-</span>'}
                            </td>
                        </tr>`;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            });
    }

    function fetchSnapshot() {
        fetch("{{ route('api.snapshot') }}")
            .then(res => res.json())
            .then(data => {
                if (data?.photo) {
                    const photoUrl = `/storage/photos/${data.photo}?${Date.now()}`;
                    document.getElementById('snapshot-image').src = photoUrl;
                    document.getElementById('snapshot-image').setAttribute('onclick', `openModal('${photoUrl}')`);
                    document.getElementById('snapshot-time').textContent = `Terakhir: ${new Date(data.created_at).toLocaleTimeString('id-ID')} WIB`;
                }
            });
    }

    setInterval(fetchLogs, 5000);
    setInterval(fetchSnapshot, 5000);
</script>
@endsection
