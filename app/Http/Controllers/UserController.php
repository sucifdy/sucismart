<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // ✅ GET /api/users
    public function index()
    {
        $users = DB::table('users')->orderBy('id', 'desc')->get();
        return response()->json($users);
    }

    // ✅ POST /api/users
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'uid' => 'nullable|string',
        ]);

        $id = DB::table('users')->insertGetId([
            'name' => $request->name,
            'uid' => $request->uid,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'User created', 'id' => $id], 201);
    }

    // ✅ GET /api/users/{id}
    public function show($id)
    {
        $user = DB::table('users')->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    // ✅ PUT /api/users/{id}
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'uid' => 'nullable|string',
        ]);

        $updated = DB::table('users')->where('id', $id)->update([
            'name' => $request->name,
            'uid' => $request->uid,
            'updated_at' => now()
        ]);

        if (!$updated) {
            return response()->json(['message' => 'User not found or not updated'], 404);
        }

        return response()->json(['message' => 'User updated']);
    }

    // ✅ DELETE /api/users/{id}
    public function destroy($id)
    {
        $deleted = DB::table('users')->where('id', $id)->delete();
        if (!$deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['message' => 'User deleted']);
    }
}
