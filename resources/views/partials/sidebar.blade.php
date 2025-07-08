<div id="sidebar"
     class="w-48 h-screen bg-blue-900 text-white flex flex-col fixed top-0 left-0 z-40 shadow-lg transition-transform duration-300">

    <div class="flex items-center justify-center px-4 py-4 border-b border-blue-800">
        <span class="text-lg font-bold tracking-wide">SMART ROOM</span>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-1 overflow-auto text-sm">
        <a href="{{ route('dashboard') }}"
           class="flex items-center space-x-2 px-4 py-2 rounded-md hover:bg-blue-700 {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
            <span>🏠</span><span>Dashboard</span>
        </a>
        <a href="{{ url('/lingkungan') }}"
           class="flex items-center space-x-2 px-4 py-2 rounded-md hover:bg-blue-700 {{ request()->is('lingkungan') ? 'bg-blue-700' : '' }}">
            <span>🌡️</span><span>Lingkungan</span>
        </a>
        <a href="{{ url('/energi') }}"
           class="flex items-center space-x-2 px-4 py-2 rounded-md hover:bg-blue-700 {{ request()->is('energi') ? 'bg-blue-700' : '' }}">
            <span>⚡</span><span>Energi</span>
        </a>
        <a href="{{ url('/otomatisasi') }}"
           class="flex items-center space-x-2 px-4 py-2 rounded-md hover:bg-blue-700 {{ request()->is('otomatisasi') ? 'bg-blue-700' : '' }}">
            <span>⏱️</span><span>Otomatisasi</span>
        </a>
        <a href="{{ url('/chatbot') }}"
           class="flex items-center space-x-2 px-4 py-2 rounded-md hover:bg-blue-700 {{ request()->is('chatbot') ? 'bg-blue-700' : '' }}">
            <span>🤖</span><span>Chatbot</span>
        </a>
        <a href="{{ url('/logakses') }}"
           class="flex items-center space-x-2 px-4 py-2 rounded-md hover:bg-blue-700 {{ request()->is('logakses') ? 'bg-blue-700' : '' }}">
            <span>📋</span><span>Log Akses</span>
        </a>
    </nav>
</div>
