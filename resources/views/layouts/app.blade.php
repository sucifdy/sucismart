<!DOCTYPE html>
<html lang="en" id="htmlRoot" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <title>Smart Lock</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {},
            }
        }
    </script>
</head>

<body class="h-screen text-gray-900 bg-white dark:bg-gray-900 transition-colors duration-500 overflow-hidden">
<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <div id="sidebar"
         class="w-48 h-screen bg-blue-900 text-white flex flex-col fixed top-0 left-0 z-40 shadow-lg transform transition-transform duration-300 -translate-x-0">
        @include('partials.sidebar')
    </div>

    {{-- MAIN CONTAINER --}}
    <div id="mainContainer" class="flex-1 flex flex-col h-screen ml-48 pt-16 overflow-hidden transition-all duration-300">

        {{-- NAVBAR --}}
        <header id="mainHeader"
                class="fixed top-0 left-48 right-0 h-16 bg-blue-900 px-6 flex justify-between items-center text-white shadow border-b border-blue-800 z-50 transition-all duration-300">
            <div class="flex items-center gap-4">
                <button id="toggleSidebar" class="text-white p-2 rounded hover:bg-blue-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ url('/chatbot') }}" class="w-10 h-10 rounded-full bg-white text-blue-900 flex items-center justify-center shadow hover:scale-105 transition">💬</a>
                <a href="{{ url('/notifikasi') }}" class="w-10 h-10 rounded-full bg-white text-yellow-600 flex items-center justify-center shadow hover:scale-105 transition">🔔</a>
                <a href="{{ url('/statistik') }}" class="w-10 h-10 rounded-full bg-white text-green-700 flex items-center justify-center shadow hover:scale-105 transition">📈</a>
                <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-full bg-white text-blue-800 flex items-center justify-center shadow hover:scale-105 transition">🌙</button>
            </div>
        </header>

        {{-- KONTEN UTAMA --}}
        <main class="flex-1 overflow-y-auto bg-white dark:bg-gray-900 transition-colors duration-500">
            <div class="w-full px-4 sm:px-6 pt-6">
                @yield('content')
            </div>
        </main>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    // Dark mode
    if (localStorage.theme === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }

    function toggleDarkMode() {
        const html = document.documentElement;
        html.classList.toggle('dark');
        localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
    }

    // Sidebar toggle
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("toggleSidebar");
        const mainContainer = document.getElementById("mainContainer");
        const mainHeader = document.getElementById("mainHeader");

        if (window.innerWidth < 768) {
            sidebar.classList.add("-translate-x-full");
            mainContainer.classList.remove("ml-48");
            mainHeader.classList.replace("left-48", "left-0");
        } else {
            mainContainer.classList.add("ml-48");
        }

        toggleBtn.addEventListener("click", () => {
            const sidebarClosed = sidebar.classList.toggle("-translate-x-full");

            if (sidebarClosed) {
                mainContainer.classList.remove("ml-48");
                mainHeader.classList.replace("left-48", "left-0");
            } else {
                mainContainer.classList.add("ml-48");
                mainHeader.classList.replace("left-0", "left-48");
            }
        });
    });
</script>

@yield('scripts')
</body>
</html>
