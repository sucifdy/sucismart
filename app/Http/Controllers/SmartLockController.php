<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SmartLockController extends Controller
{
    /**
     * ✅ POST /api/smartlock
     * Menerima log dari Arduino atau ESP32
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'uid' => 'nullable|string',
            'finger_id' => 'nullable|integer',
            'event' => 'required|string',
            'source' => 'nullable|string',
            'photo' => 'nullable|string',
        ]);

        DB::table('logs')->insert([
            'uid' => $request->uid,
            'finger_id' => $request->finger_id,
            'event' => $request->event,
            'source' => $request->source,
            'photo' => $request->photo,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['status' => 'success'], 201);
    }

    /**
     * ✅ GET /api/smartlock
     * Mengambil semua log dan menampilkan nama user
     * dari pencocokan UID atau finger_id
     */
    public function index(): JsonResponse
    {
        $logs = DB::table('logs')
            ->leftJoin('users as u1', 'logs.uid', '=', 'u1.uid')
            ->leftJoin('fingerprints', 'logs.finger_id', '=', 'fingerprints.finger_id')
            ->leftJoin('users as u2', 'fingerprints.user_id', '=', 'u2.id')
            ->select(
                'logs.id',
                'logs.uid',
                'logs.finger_id',
                'logs.event',
                'logs.source',
                'logs.photo',
                'logs.created_at',
                DB::raw('COALESCE(u1.name, u2.name) as user_name')
            )
            ->orderBy('logs.created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * ✅ POST /api/upload-photo
     * Menerima file foto snapshot dari ESP32-CAM
     * dan mengupdate log terakhir yang belum ada foto
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $base64 = $request->input('photo');

        if (!$base64 || !str_starts_with($base64, 'data:image/jpeg;base64,')) {
            return response()->json(['status' => 'error', 'message' => 'Format base64 tidak valid'], 400);
        }

        // Bersihkan data base64
        $base64Data = base64_decode(substr($base64, strlen('data:image/jpeg;base64,')));

        // Simpan ke storage/photos
        $filename = 'snapshot_' . time() . '.jpg';
        Storage::disk('public')->put("photos/$filename", $base64Data);

        // Update log terakhir (dalam 10 detik terakhir) yang belum punya foto
        $updated = DB::table('logs')
            ->whereNull('photo')
            ->where('created_at', '>=', Carbon::now()->subSeconds(10))
            ->orderByDesc('created_at')
            ->limit(1)
            ->update(['photo' => $filename]);

        return response()->json([
            'status' => 'success',
            'filename' => $filename,
            'url' => asset("storage/photos/$filename"),
            'updated_log' => $updated
        ], 201);
    }
}
