@extends('layouts.admin')

@section('title', $user->exists ? 'Edit Akun '.$user->name : 'Tambah '.\App\Models\User::ROLES[$user->role])

@section('actions')
    <a href="{{ route('admin.users.index', ['role' => $user->role]) }}" class="btn-outline py-2">Kembali</a>
@endsection

@section('content')
    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="card max-w-2xl space-y-5 p-6">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="name">Nama</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" class="admin-input" required>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="admin-input" required>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="phone">Nomor Telepon</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" class="admin-input">
                @error('phone') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="location_id">Lokasi</label>
                <select id="location_id" name="location_id" class="admin-input">
                    <option value="">-</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected(old('location_id', $user->location_id) === $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('location_id') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="label" for="address">Alamat</label>
            <textarea id="address" name="address" rows="2" class="admin-input">{{ old('address', $user->address) }}</textarea>
            @error('address') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Peran</label>
            <div class="flex gap-3">
                @foreach (\App\Models\User::ROLES as $value => $label)
                    <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary-50">
                        <input type="radio" name="role" value="{{ $value }}" class="text-primary focus:ring-primary" @checked(old('role', $user->role) === $value)>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('role') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 border-t border-gray-100 pt-5 sm:grid-cols-2">
            <div>
                <label class="label" for="password">{{ $user->exists ? 'Password Baru' : 'Password' }}</label>
                <input id="password" name="password" type="password" class="admin-input" autocomplete="new-password" @required(! $user->exists)>
                @if ($user->exists) <p class="mt-1 text-xs text-grey">Kosongkan jika tidak diubah.</p> @endif
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="admin-input" autocomplete="new-password">
            </div>
        </div>

        <button class="btn-primary w-full">{{ $user->exists ? 'Simpan Perubahan' : 'Buat Akun' }}</button>
    </form>
@endsection
