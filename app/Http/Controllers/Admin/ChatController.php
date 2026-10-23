<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('admin.chats.index', ['threads' => $this->threads()]);
    }

    public function show(User $user)
    {
        $this->markRead($user);

        return view('admin.chats.show', [
            'threads' => $this->threads(),
            'customer' => $user,
            'messages' => $user->messages()->oldest()->get()->map->toChatArray(),
        ]);
    }

    public function messages(Request $request, User $user)
    {
        $messages = $user->messages()->since($request->query('since'))->get();
        $this->markRead($user);

        return response()->json($messages->map->toChatArray());
    }

    public function store(Request $request, User $user)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:1000']]);

        $message = $user->messages()->create(['sender' => 'admin', 'body' => $data['body']]);

        return response()->json($message->toChatArray());
    }

    private function markRead(User $user): void
    {
        $user->messages()->unreadFrom('user')->update(['read_at' => now()]);
    }

    /**
     * Daftar percakapan: pesan terakhir tiap pelanggan + jumlah yang belum dibaca.
     */
    private function threads()
    {
        $unread = Message::unreadFrom('user')
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return Message::with('user')
            ->latest()
            ->get()
            ->unique('user_id')
            ->map(fn (Message $last) => [
                'user' => $last->user,
                'last' => $last,
                'unread' => $unread[$last->user_id] ?? 0,
            ]);
    }
}
