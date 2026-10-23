@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
    <x-page-header title="Edit Profil" :back="route('profile')"/>

    <div class="container-page space-y-5 px-5 py-5 lg:grid lg:grid-cols-2 lg:items-start lg:gap-6 lg:space-y-0 lg:px-8 lg:py-6">
        <form method="POST" action="{{ route('profile.update') }}" class="card space-y-4 p-5">
            @csrf @method('PUT')
            <h2 class="font-semibold">Data Diri</h2>
            <div>
                <label class="label" for="name">Nama</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" class="input" required>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="input" required>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="phone">Nomor Telepon</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" class="input">
                @error('phone') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="address">Alamat Lengkap</label>
                <textarea id="address" name="address" rows="2" class="input">{{ old('address', $user->address) }}</textarea>
                @error('address') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="location_id">Lokasi</label>
                <select id="location_id" name="location_id" class="input">
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected(old('location_id', $user->location_id) === $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary w-full">Simpan</button>
        </form>

        <form method="POST" action="{{ route('profile.password') }}" class="card space-y-4 p-5">
            @csrf @method('PUT')
            <h2 class="font-semibold">Ubah Password</h2>
            @if ($user->password)
                <div>
                    <label class="label" for="current_password">Password Lama</label>
                    <input id="current_password" name="current_password" type="password" class="input" autocomplete="current-password">
                    @error('current_password') <p class="error">{{ $message }}</p> @enderror
                </div>
            @endif
            <div>
                <label class="label" for="password">Password Baru</label>
                <input id="password" name="password" type="password" class="input" autocomplete="new-password">
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="password_confirmation">Konfirmasi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password">
            </div>
            <button class="btn-primary w-full">Ubah Password</button>
        </form>
    </div>
@endsection
