<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $this->markRead($request);

        return view('user.chat', [
            'messages' => $user->messages()->oldest()->get()->map->toChatArray(),
            'prefill' => $request->query('text'),
        ]);
    }

    /**
     * Dipanggil berkala (polling) agar balasan CS muncul tanpa reload.
     */
    public function messages(Request $request)
    {
        $messages = $request->user()->messages()->since($request->query('since'))->get();
        $this->markRead($request);

        return response()->json($messages->map->toChatArray());
    }

    public function store(Request $request)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:1000']]);

        $message = $request->user()->messages()->create(['sender' => 'user', 'body' => $data['body']]);

        return response()->json($message->toChatArray());
    }

    private function markRead(Request $request): void
    {
        $request->user()->messages()->unreadFrom('admin')->update(['read_at' => now()]);
    }
}
