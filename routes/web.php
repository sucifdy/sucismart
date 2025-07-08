<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\EnergiController;

// === ✅ LANDING & LOGIN ===
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/login', [LandingController::class, 'login'])->name('login');

// === ✅ STATISTIK ===
Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');
Route::get('/statistik/export/pdf', [StatistikController::class, 'exportPdf'])->name('statistik.export.pdf');

// === ✅ DASHBOARD & FITUR UTAMA ===
Route::get('/dashboard', [DashboardController::class, 'lock'])->name('dashboard');
Route::get('/log-akses', [DashboardController::class, 'logAkses'])->name('log.akses');
Route::get('/logakses', [DashboardController::class, 'logAkses'])->name('logakses');
Route::get('/keamanan', [DashboardController::class, 'keamanan'])->name('keamanan');

// === ✅ SNAPSHOT & LOG API ===
Route::post('/trigger-snapshot', [DashboardController::class, 'triggerSnapshot'])->name('snapshot.trigger');
Route::get('/api/snapshot-terakhir', [DashboardController::class, 'getSnapshot'])->name('api.snapshot');
Route::get('/api/log-terakhir', [DashboardController::class, 'getLatestLogs'])->name('api.log');
Route::get('/api/ringkasan-aktivitas', [DashboardController::class, 'getRingkasanAktivitas'])->name('api.ringkasan');
Route::get('/api/notifikasi-keamanan', [DashboardController::class, 'getNotifikasiKeamanan'])->name('api.notifikasi');


// === ✅ OTOMATISASI & LINGKUNGAN ===
Route::get('/otomatisasi', [AutomationController::class, 'show'])->name('otomatisasi');
Route::get('/lingkungan', [EnvironmentController::class, 'show'])->name('lingkungan');

// === ✅ CHATBOT & NOTIFIKASI ===
Route::post('/chat-clear', [ChatbotController::class, 'clear'])->name('chat.clear');
Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot');
Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');

// === ✅ HALAMAN BLADE LANGSUNG (STATIC) ===
Route::view('/perangkat', 'perangkat')->name('perangkat');
Route::view('/chatbot', 'chatbot')->name('chatbot');
Route::view('/chat-history', 'chat-history')->name('chat.history');

// === ✅ API OTOMATISASI ===
Route::get('/api/automation/latest', [AutomationController::class, 'latest'])->name('api.automation.latest');

// === ✅ API SENSOR ===
Route::get('/api/environment/latest', [EnvironmentController::class, 'latest'])->name('api.environment.latest');
Route::get('/api/environment/chart', [EnvironmentController::class, 'chartData'])->name('api.environment.chart');
Route::get('/api/environment/warnings', [EnvironmentController::class, 'warnings'])->name('api.environment.warnings');

// === ✅ LOG & WARNING API ===
Route::post('/log', [AutomationController::class, 'storeLog']);
Route::post('/warning', [AutomationController::class, 'storeWarning']);
Route::get('/log/latest', [AutomationController::class, 'getLog'])->name('api.log.latest');
Route::get('/warning/latest', [AutomationController::class, 'getWarning'])->name('api.warning.latest');

// === ✅ ENERGI PAGE & API ===
Route::get('/energi', [EnergiController::class, 'index'])->name('energi.index');
Route::get('/api/energi/latest', [EnergiController::class, 'latest'])->name('api.energi.latest');
Route::get('/api/energi/chart', [EnergiController::class, 'chartData'])->name('api.energi.chart');
Route::get('/statistik/export/pdf', [StatistikController::class, 'exportPdf'])->name('statistik.export.pdf');
Route::get('/statistik/export/custom', [EnergiController::class, 'exportCustomPdf'])->name('statistik.export.pdf.custom');

Route::get('/energi', [EnergiController::class, 'index'])->name('energi');
Route::get('/dashboard-lock', [DashboardController::class, 'lock'])->name('dashboard.lock');
Route::get('/chart/jumlah-orang-per-hari', [DashboardController::class, 'getChartJumlahOrang'])->name('chart.jumlah.orang');
Route::get('/chart/energi-harian', [DashboardController::class, 'getChartEnergiHarian'])->name('chart.energi.harian');
Route::get('/lingkungan', [EnvironmentController::class, 'show'])->name('environment.show');
