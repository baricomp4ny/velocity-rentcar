<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('location')->loadCount(['bookings', 'favorites']);

        return view('user.profile.show', compact('user'));
    }

    public function edit(Request $request)
    {
        return view('user.profile.edit', ['user' => $request->user(), 'locations' => Location::active()->ordered()->get()]);
    }

    public function update(ProfileRequest $request)
    {
        $request->user()->update($request->validated());

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            // Akun dari Google belum punya password, jadi password lama tidak wajib.
            'current_password' => [$user->password ? 'required' : 'nullable', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [], [
            'current_password' => 'Password lama',
            'password' => 'Password baru',
        ]);

        $user->update(['password' => $data['password']]);

        return redirect()->route('profile')->with('success', 'Password berhasil diubah.');
    }
}
