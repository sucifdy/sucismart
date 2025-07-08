<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    /**
     * ✅ Tampilkan halaman notifikasi Smart Room
     */
    public function index()
    {
        // Jika ingin reset notifikasi baru saat halaman dibuka, aktifkan baris di bawah ini:
        // DB::table('notifikasi')->where('is_new', true)->update(['is_new' => false]);

        $notif = DB::table('notifikasi')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('notifikasi', compact('notif'));
    }

    /**
     * ✅ Simpan notifikasi baru dari ESP32 atau sistem otomatisasi
     */
    public function store(Request $request)
    {
        // 🔒 Validasi agar input aman
        $request->validate([
            'icon' => 'required|string|max:10',
            'judul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:50',
        ]);

        // 💾 Simpan ke database
        DB::table('notifikasi')->insert([
            'icon' => $request->icon,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'is_new' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['status' => 'success']);
    }
}
