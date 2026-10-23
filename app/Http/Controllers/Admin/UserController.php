<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role') === 'admin' ? 'admin' : 'user';

        $users = User::where('role', $role)
            ->with('location')
            ->withCount('bookings')
            ->withSum(['bookings as total_spent' => fn ($q) => $q->revenue()], DB::raw(Booking::TOTAL_SQL))
            ->when($request->query('q'), fn ($q, $search) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'role'));
    }

    public function create(Request $request)
    {
        return view('admin.users.form', [
            'user' => new User(['role' => $request->query('role') === 'admin' ? 'admin' : 'user']),
            'locations' => Location::ordered()->get(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->userData());

        return redirect()->route('admin.users.index', ['role' => $user->role])->with('success', "Akun {$user->name} berhasil dibuat.");
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['user' => $user, 'locations' => Location::ordered()->get()]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->userData();

        if ($user->is($request->user()) && $data['role'] !== 'admin') {
            return back()->withInput()->with('error', 'Kamu tidak bisa mencabut peran admin dari akunmu sendiri.');
        }

        $user->update($data);

        return redirect()->route('admin.users.index', ['role' => $user->role])->with('success', "Akun {$user->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        if ($user->bookings()->exists()) {
            return back()->with('error', "{$user->name} punya riwayat pesanan sehingga tidak bisa dihapus.");
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
