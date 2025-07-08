@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-500 px-4 py-4">
    <div class="w-full flex flex-col gap-6 h-full">

        {{-- HEADER --}}
        <div class="text-center">
            <h1 class="text-3xl font-bold text-blue-700 dark:text-blue-400 flex justify-center items-center gap-2 drop-shadow">
                <img src="https://api.dicebear.com/6.x/bottts/svg" alt="bot" class="w-6 h-6"> Smart Room Chatbot
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 italic">
                Mendukung <span class="text-green-600 dark:text-green-400 font-semibold">multi-bahasa</span> & percakapan real-time.
            </p>
        </div>

        {{-- TOOLS --}}
        <div class="flex justify-between items-center text-sm">
            <a href="/chat-history" class="text-blue-600 hover:underline">📄 Lihat Riwayat</a>
            <form method="POST" action="{{ route('chat.clear') }}">
                @csrf
                <button type="submit" class="text-red-600 hover:underline">🗑️ Hapus Semua</button>
            </form>
        </div>

        {{-- CHAT WINDOW --}}
        <div class="flex-1 flex flex-col rounded-xl shadow bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div id="chat-window" class="flex-1 overflow-y-auto px-4 pt-4 pb-2 space-y-3 bg-white dark:bg-gray-800">
                @foreach(App\Models\Chat::latest()->take(50)->get()->reverse() as $chat)
                <div class="flex {{ $chat->sender === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="flex items-end gap-2 max-w-[85%]">
                        @if($chat->sender === 'bot')
                            <img src="https://api.dicebear.com/6.x/bottts/svg" class="w-5 h-5 rounded-full" />
                        @endif
                        <div class="px-4 py-2 rounded-2xl text-[13px] leading-tight break-words whitespace-pre-line shadow
                            {{ $chat->sender === 'user' ? 'bg-green-100 dark:bg-green-700 text-right rounded-br-none' : 'bg-blue-500 text-white dark:bg-blue-600 text-left rounded-bl-none' }}">
                            {{ strip_tags(str_replace(['**','__','##'], '', $chat->message)) }}
                            <div class="text-[10px] mt-1 {{ $chat->sender === 'user' ? 'text-right text-gray-400' : 'text-white/70 text-right' }}">
                                {{ $chat->created_at->format('H:i') }}
                            </div>
                        </div>
                        @if($chat->sender === 'user')
                            <img src="https://api.dicebear.com/6.x/personas/svg" class="w-5 h-5 rounded-full" />
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- INPUT BAR --}}
            <form class="flex gap-2 p-3 border-t border-gray-200 dark:border-gray-700" id="chat-form">
                <input type="text" id="chat-input" placeholder="Tulis pertanyaan..." class="flex-1 px-4 py-2 rounded-full border text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 dark:border-gray-600 transition" />
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-full text-sm shadow">Kirim</button>
                <button type="button" id="voice" class="bg-gray-600 hover:bg-red-600 text-white px-4 py-2 rounded-full text-sm shadow">🎤</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const chatWindow = document.getElementById('chat-window');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const mic = document.getElementById('voice');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const userMessage = input.value.trim();
        if (!userMessage) return;

        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        chatWindow.innerHTML += `
            <div class="flex justify-end">
                <div class="flex items-end gap-2 max-w-[85%]">
                    <div class="px-4 py-2 rounded-2xl text-[13px] leading-tight bg-green-100 dark:bg-green-700 text-right rounded-br-none shadow">
                        ${userMessage}
                        <div class="text-[10px] text-gray-400 mt-1 text-right">${now}</div>
                    </div>
                    <img src="https://api.dicebear.com/6.x/personas/svg" class="w-5 h-5 rounded-full" />
                </div>
            </div>
            <div class="flex justify-start" id="loading">
                <div class="bg-gray-300 dark:bg-gray-600 italic px-4 py-2 rounded-2xl shadow animate-pulse text-[13px]">....</div>
            </div>
        `;
        input.value = '';
        chatWindow.scrollTop = chatWindow.scrollHeight;

        try {
            const res = await fetch('/api/chatbot', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userMessage })
            });
            const data = await res.json();
            document.getElementById('loading')?.remove();

            const reply = data.reply.replace(/\*\*/g, '').replace(/__/g, '').replace(/##/g, '').trim();
            chatWindow.innerHTML += `
                <div class="flex justify-start">
                    <div class="flex items-end gap-2 max-w-[85%]">
                        <img src="https://api.dicebear.com/6.x/bottts/svg" class="w-5 h-5 rounded-full" />
                        <div class="px-4 py-2 rounded-2xl text-[13px] leading-tight bg-blue-500 text-white dark:bg-blue-600 rounded-bl-none shadow">
                            ${reply}
                            <div class="text-[10px] text-white/70 mt-1 text-right">${now}</div>
                        </div>
                    </div>
                </div>
            `;
            chatWindow.scrollTop = chatWindow.scrollHeight;
        } catch (err) {
            document.getElementById('loading')?.remove();
            chatWindow.innerHTML += `
                <div class="flex justify-start">
                    <div class="bg-red-200 dark:bg-red-700 px-4 py-2 rounded-2xl shadow text-sm max-w-[85%]">⚠️ Gagal konek ke server.</div>
                </div>
            `;
        }
    });

    // Mic support
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (SpeechRecognition) {
        const recognition = new SpeechRecognition();
        recognition.lang = 'id-ID';

        mic.addEventListener('click', () => {
            mic.innerText = '🎤 Mendengarkan...';
            mic.classList.add('bg-red-600');
            recognition.start();
        });

        recognition.onresult = (e) => {
            input.value = e.results[0][0].transcript;
            mic.innerText = '🎤';
            mic.classList.remove('bg-red-600');
        };

        recognition.onend = () => {
            mic.innerText = '🎤';
            mic.classList.remove('bg-red-600');
        };
    }
</script>
@endsection
