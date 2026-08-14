<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ChatController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $chats = $user->chat($user->role)->get();

        return response()->json($chats);
    }

    public function show(string $id)
    {
        $user = Auth::user();
        $chat = $user->where('id', '=', $id, 'and')->chat($user->role)->with('messages')->firstOrFail();

        return response()->json($chat);
    }

    public function store(Request $request, string $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $chat = $user->where('id', '=', $id, 'and')->chat($user->role)->firstOrFail();

        $message = $chat->messages()->create([
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }
}
