<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function create()
    {
        return view('admin.announcements.create', ['customerCount' => User::where('role', 'user')->count()]);
    }

    /**
     * Kirim pengumuman (mis. promo baru) ke menu Notification semua pelanggan.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255', 'starts_with:/,http://,https://'],
        ], [], ['title' => 'Judul', 'body' => 'Isi pengumuman', 'url' => 'Tautan']);

        $count = 0;
        $now = now();

        User::where('role', 'user')->select('id')->chunkById(500, function ($users) use ($data, $now, &$count) {
            UserNotification::insert($users->map(fn (User $user) => [
                'id' => (string) Str::orderedUuid(),
                'user_id' => $user->id,
                'title' => $data['title'],
                'body' => $data['body'],
                'url' => $data['url'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all());

            $count += $users->count();
        });

        return back()->with('success', "Pengumuman terkirim ke {$count} pelanggan.");
    }
}
