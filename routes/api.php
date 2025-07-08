<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SmartLockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EnergiController;

// ==================== 🌡️ Sensor Manual + Realtime ====================
Route::post('/suhu', [SensorController::class, 'simpanSuhu']);
Route::post('/lingkungan', [SensorController::class, 'simpanLingkungan']);

// ==================== 🌿 Environment Monitoring (Realtime) ====================
Route::get('/environment/latest', [EnvironmentController::class, 'latest'])->name('api.environment.latest');
Route::get('/environment/chart', [EnvironmentController::class, 'chartData'])->name('api.environment.chart');
Route::get('/environment/warnings', [EnvironmentController::class, 'warnings'])->name('api.environment.warnings');
Route::post('/environment', [EnvironmentController::class, 'store']);

// ==================== 🔐 Smart Lock ====================
Route::post('/smartlock', [SmartLockController::class, 'store']);
Route::get('/smartlock', [SmartLockController::class, 'index']);
Route::post('/upload-photo', [SmartLockController::class, 'uploadPhoto']);

// ==================== 👤 User ====================
Route::apiResource('users', UserController::class);

// ==================== 🔑 Fingerprint ====================
Route::get('/fingerprints', [FingerprintController::class, 'index']);
Route::post('/fingerprints', [FingerprintController::class, 'store']);
Route::delete('/fingerprints/{id}', [FingerprintController::class, 'destroy']);

// ==================== 🔔 Notifikasi ====================
Route::post('/notifikasi', [NotifikasiController::class, 'store']);

// ==================== 🤖 Chatbot ====================
Route::post('/chatbot', [ChatbotController::class, 'chat']);

// ==================== 🧠 YOLO Detection ====================
Route::post('/detection', [MonitoringController::class, 'store']);
Route::get('/latest-detection', [MonitoringController::class, 'latest'])->name('api.latest-detection');

// ==================== ⚙️ Automation (suhu, cahaya, orang) ====================
Route::post('/automation/store', [AutomationController::class, 'store']);
Route::get('/automation/latest', [AutomationController::class, 'latest'])->name('api.automation.latest');

// ==================== 🔌 Device Control (Relay Manual) ====================
Route::post('/device/{device}/{action}', [DeviceController::class, 'control']);
Route::get('/device/status', [DeviceController::class, 'status'])->name('api.device.status');
Route::get('/reset-perintah', [DeviceController::class, 'reset'])->name('api.device.reset');

// ==================== 📝 Realtime Log & Warning ====================
Route::post('/log', [AutomationController::class, 'storeLog']);
Route::post('/warning', [AutomationController::class, 'storeWarning']);
Route::get('/log/latest', [AutomationController::class, 'getLog'])->name('api.log.latest');
Route::get('/warning/latest', [AutomationController::class, 'getWarning'])->name('api.warning.latest');

// ==================== ⚡ Energi Monitoring (ACS712) ====================
Route::post('/api/energi/store', [EnergiController::class, 'store']);
Route::get('/api/energi/latest', [EnergiController::class, 'latest'])->name('api.energi.latest');
Route::get('/api/energi/chart', [EnergiController::class, 'chartData'])->name('api.energi.chart');
Route::post('/energi/store', [EnergiController::class, 'store'])->name('api.energi.store');
Route::get('/energi/filter', [EnergiController::class, 'filterByTanggal']);
Route::get('/energy/latest', [EnergiController::class, 'getLatestEnergy']);
Route::get('/energy/hari-ini', [EnergiController::class, 'getTotalHariIni']);
Route::get('/api/energi/rata-rata-mingguan', [EnergiController::class, 'rataRataMingguan']);

Route::post('/device/relay-status', [DeviceController::class, 'updateRelayStatus']);
Route::get('/device/relay-status', [DeviceController::class, 'getRelayStatus']);
Route::get('/jumlah-orang-hari-ini', [\App\Http\Controllers\DashboardController::class, 'jumlahOrangHariIni']);
Route::get('/rata-rata-orang', [\App\Http\Controllers\DashboardController::class, 'rataRataOrangMingguan']);
Route::get('/api/energi/total-bulanan', [EnergiController::class, 'totalBulanan']);
Route::get('/api/energi/statistik', [EnergiController::class, 'statistik']);
Route::get('/energi/statistik', [EnergiController::class, 'statistik']);
