<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->userNotifications()->latest()->get();

        $request->user()->userNotifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('user.notifications', compact('notifications'));
    }
}
