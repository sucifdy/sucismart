@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-white px-0 py-0

transition duration-300">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600 bg-clip-text text-transparent">
                Notifikasi Smart Room
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                Lihat pembaruan terakhir terkait keamanan dan aktivitas ruangan Anda.
            </p>
            <div class="w-24 h-0.5 bg-gradient-to-r from-blue-400 to-purple-400 mx-auto mt-4"></div>
        </div>

        {{-- Isi Notifikasi --}}
        @forelse($notif as $n)
        <div class="mb-6 p-5 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-xl shadow-md hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-2">
                <div class="flex items-center gap-3">
                    <div class="text-xl">{{ $n->icon }}</div>
                    <div>
                        <h2 class="text-lg font-semibold">{{ $n->judul }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($n->created_at)->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>
                </div>
                @if($n->is_new)
                <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full font-semibold animate-pulse">Baru</span>
                @endif
            </div>
            @if($n->deskripsi)
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ $n->deskripsi }}
            </p>
            @endif
            <div class="mt-4">
                <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded">
                    {{ $n->kategori }}
                </span>
            </div>
        </div>
        @empty
        {{-- Kosong --}}
        <div class="flex flex-col items-center justify-center h-[60vh] text-gray-500 dark:text-gray-400 space-y-4">
            <div class="text-6xl animate-bounce-slow">📬</div>
            <h2 class="text-xl font-semibold text-center">Tidak ada notifikasi saat ini</h2>
            <p class="text-sm text-center">Sistem berjalan normal tanpa aktivitas penting.</p>
        </div>
        @endforelse

    </div>
</div>

{{-- Tambahan animasi --}}
<style>
    .animate-bounce-slow {
        animation: bounce 2.5s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
</style>
@endsection
