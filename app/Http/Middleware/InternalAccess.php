<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InternalAccess
{
    public function handle(Request $request, Closure $next)
    {
        $allowedIps = ['127.0.0.1', '::1', '192.168.1.100']; // Tambah IP LAN kamu kalau perlu

        if (!in_array($request->ip(), $allowedIps)) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk jaringan internal.');
        }

        return $next($request);
    }
}
