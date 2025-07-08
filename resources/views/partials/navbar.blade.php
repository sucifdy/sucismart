<header id="mainHeader"
        class="fixed top-0 left-64 right-0 h-16 bg-blue-900 text-white shadow border-b border-blue-800 z-50 transition-all duration-300">

    <div class="max-w-full h-full flex justify-between items-center px-6">
        {{-- KIRI: Tombol Toggle Sidebar --}}
        <div class="flex items-center gap-4">
            <button id="toggleSidebar"
                    class="text-white p-2 rounded hover:bg-blue-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- KANAN: Ikon Navigasi --}}
        <div class="flex items-center gap-4 whitespace-nowrap shrink-0">
            <a href="{{ url('/chatbot') }}"
               class="w-12 h-12 rounded-full bg-white text-blue-900 text-xl flex items-center justify-center shadow hover:scale-105 transition">
                💬
            </a>
            <a href="{{ url('/notifikasi') }}"
               class="w-12 h-12 rounded-full bg-white text-yellow-600 text-xl flex items-center justify-center shadow hover:scale-105 transition">
                🔔
            </a>
            <a href="{{ url('/statistik') }}"
               class="w-12 h-12 rounded-full bg-white text-green-700 text-xl flex items-center justify-center shadow hover:scale-105 transition">
                📈
            </a>
            <button onclick="toggleDarkMode()"
                    class="w-12 h-12 rounded-full bg-white text-blue-800 text-xl flex items-center justify-center shadow hover:scale-105 transition">
                🌙
            </button>
        </div>
    </div>
</header>
