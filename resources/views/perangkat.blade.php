@extends('layouts.app')

@section('content')
<div class="px-6 py-10 bg-white dark:bg-gray-900 min-h-screen text-gray-900 dark:text-white transition-colors duration-500">
    <div class="max-w-7xl mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">⚙️ Kontrol Perangkat Rumah</h1>
            <p class="text-gray-500 dark:text-gray-300 mt-1">Atur dan pantau perangkat secara dinamis dan stylish.</p>
        </div>

        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach ([
                ['💡', 'Lampu', 'lampu'],
                ['🌀', 'Kipas', 'kipas'],
                ['🍚', 'Magicom', 'magicom'],
                ['💧', 'Dispenser', 'dispenser'],
                ['📺', 'Televisi', 'tv'],
                ['🍞', 'Toaster', 'toaster'],
            ] as [$icon, $label, $id])
            <div class="rounded-3xl bg-gradient-to-br from-blue-800 to-blue-700 shadow-lg p-6 hover:shadow-xl hover:scale-[1.01] transition-transform flex flex-col justify-between">
                <div class="flex items-center gap-4 mb-6">
                    <div class="text-5xl">{{ $icon }}</div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ $label }}</h2>
                        <p class="text-sm text-blue-100">Status: <span class="italic">Terkoneksi</span></p>
                    </div>
                </div>
                <div class="flex gap-2 justify-between items-center mt-auto">
                    <button class="bg-lime-500 hover:bg-lime-600 text-white font-bold text-sm px-4 py-2 rounded-full shadow transition">ON</button>
                    <button class="bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm px-4 py-2 rounded-full shadow transition">OFF</button>
                    <button class="bg-white hover:bg-gray-200 text-blue-800 text-sm font-semibold px-4 py-2 rounded-full shadow flex items-center gap-2 transition">
                        ⏰ Jadwal
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-16 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-gray-700 rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-blue-800 dark:text-blue-300 flex items-center gap-2">📢 Notifikasi Peringatan</h2>
                <ul class="divide-y divide-blue-100 dark:divide-gray-700">
                    <li class="py-3 flex items-start gap-3">
                        <span class="text-xl">🚨</span>
                        <span class="text-gray-700 dark:text-gray-200">Lampu ruang tamu menyala lebih dari 2 jam</span>
                    </li>
                    <li class="py-3 flex items-start gap-3">
                        <span class="text-xl">🔌</span>
                        <span class="text-gray-700 dark:text-gray-200">Magicom dinyalakan jam 07:00</span>
                    </li>
                    <li class="py-3 flex items-start gap-3">
                        <span class="text-xl">📴</span>
                        <span class="text-gray-700 dark:text-gray-200">Kipas dimatikan otomatis jam 21:00</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-gray-700 rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-blue-800 dark:text-blue-300 flex items-center gap-2">🗂️ Log Aktivitas & Riwayat</h2>
                <ul class="divide-y divide-blue-100 dark:divide-gray-700">
                    <li class="py-3 flex items-start gap-3">
                        <span class="text-xl">🕒</span>
                        <span class="text-gray-700 dark:text-gray-200">06:45 - Lampu ON</span>
                    </li>
                    <li class="py-3 flex items-start gap-3">
                        <span class="text-xl">🕒</span>
                        <span class="text-gray-700 dark:text-gray-200">07:00 - Magicom ON</span>
                    </li>
                    <li class="py-3 flex items-start gap-3">
                        <span class="text-xl">🕒</span>
                        <span class="text-gray-700 dark:text-gray-200">09:15 - Kipas OFF</span>
                    </li>
                    <li class="py-3 flex items-start gap-3">
                        <span class="text-xl">🕒</span>
                        <span class="text-gray-700 dark:text-gray-200">10:22 - Lampu OFF</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
