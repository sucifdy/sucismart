@extends('layouts.app')

@section('content')
<div class="py-6 bg-white dark:bg-gray-900 min-h-screen text-gray-900 dark:text-white">
    <div class="max-w-4xl mx-auto space-y-6 px-4">
        <div class="text-center">
            <h1 class="text-3xl font-bold">📄 Riwayat Chatbot</h1>
            <p class="text-gray-500 dark:text-gray-300 text-sm mt-1">Menampilkan semua interaksi terbaru dengan chatbot.</p>
            <div class="mt-3 text-right">
                <form method="POST" action="{{ route('chat.clear') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline text-sm">🗑️ Hapus Semua</button>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left">Waktu</th>
                        <th class="px-4 py-2 text-left">Pengirim</th>
                        <th class="px-4 py-2 text-left">Pesan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach(App\Models\Chat::latest()->get() as $chat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 whitespace-nowrap text-[12px]">{{ $chat->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2 capitalize">{{ $chat->sender }}</td>
                            <td class="px-4 py-2 whitespace-pre-line">{{ strip_tags(str_replace(['**', '__', '##'], '', $chat->message)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-center">
            <a href="/chatbot" class="text-blue-600 hover:underline text-sm">⬅️ Kembali ke Chatbot</a>
        </div>
    </div>
</div>
@endsection
