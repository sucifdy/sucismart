<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;

class ChatbotController extends Controller
{
    public function clear()
{
    \App\Models\Chat::truncate(); // hapus semua chat
    return redirect()->back()->with('status', 'Semua chat berhasil dihapus.');
}

    public function chat(Request $request)
    {
        $userMessage = $request->input('message');

        // Simpan pesan user
        Chat::create([
            'sender' => 'user',
            'message' => $userMessage
        ]);

        // Kirim ke Hugging Face Phi 3.5 Instruct
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '. env("HUGGINGFACE_API_TOKEN"), // Ganti dengan token kamu
            'Content-Type' => 'application/json',
        ])->post('https://api-inference.huggingface.co/models/microsoft/Phi-3.5-mini-instruct', [
            'inputs' => "<|user|>\n{$userMessage}\n<|assistant|>\n",
            'parameters' => [
                'max_new_tokens' => 1000,
                'temperature' => 0.7
            ],
        ]);

        $json = $response->json();
        $reply = '❌ Bot tidak merespons.';

        // Ambil jawaban bot dari respons
        if (isset($json[0]['generated_text'])) {
            $parts = explode('<|assistant|>', $json[0]['generated_text']);
            $reply = trim($parts[1] ?? $parts[0]);
        } elseif (isset($json['error'])) {
            $reply = '⚠️ ' . $json['error'];
        }

        // Simpan jawaban bot
        Chat::create([
            'sender' => 'bot',
            'message' => $reply
        ]);

        return response()->json(['reply' => $reply]);
    }
}
