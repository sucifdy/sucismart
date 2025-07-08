<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if ($username === 'admin' && $password === '123456') {
            return redirect('/dashboard'); // ganti sesuai halaman setelah login
        }

        return back()->with('error', 'Username atau password salah.');
    }
}
