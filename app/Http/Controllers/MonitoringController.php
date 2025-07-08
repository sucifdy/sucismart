<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jumlah_orang' => 'required|integer',
            'waktu' => 'required|date',
        ]);

        DB::table('detections')->insert([
            'jumlah_orang' => $request->jumlah_orang,
            'created_at' => $request->waktu,
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'OK']);
    }

    public function latest()
    {
        return DB::table('detections')->latest()->first();
    }
}
