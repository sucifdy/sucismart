<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FingerprintController extends Controller
{
    // ✅ GET /api/fingerprints
    public function index()
    {
        $fingerprints = DB::table('fingerprints')
            ->join('users', 'fingerprints.user_id', '=', 'users.id')
            ->select('fingerprints.*', 'users.name as user_name')
            ->orderBy('fingerprints.id', 'desc')
            ->get();

        return response()->json($fingerprints);
    }

    // ✅ POST /api/fingerprints
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'finger_id' => 'required|integer|unique:fingerprints,finger_id',
        ]);

        $data = [
            'user_id' => $request->user_id,
            'finger_id' => $request->finger_id,
            'created_at' => now(),
            'updated_at' => now()
        ];

        $id = DB::table('fingerprints')->insertGetId($data);
        $data['id'] = $id;

        // Ambil nama user berdasarkan user_id
        $user = DB::table('users')->where('id', $request->user_id)->first();

        return response()->json([
            'message' => 'Fingerprint saved',
            'data' => $data,
            'user_name' => $user->name ?? null
        ]);
    }

    // ✅ DELETE /api/fingerprints/{id}
    public function destroy($id)
    {
        $deleted = DB::table('fingerprints')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Fingerprint not found'], 404);
        }

        return response()->json(['message' => 'Fingerprint deleted']);
    }
}
