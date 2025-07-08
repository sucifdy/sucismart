<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorLog;

class SensorController extends Controller
{
    public function simpanLingkungan(Request $request)
    {
        SensorLog::create([
            'suhu' => $request->suhu,
            'cahaya' => $request->cahaya,
            'flame' => $request->flame,
            'gas' => $request->gas
        ]);

        return response()->json(['message' => 'Sensor berhasil disimpan']);
    }
}
