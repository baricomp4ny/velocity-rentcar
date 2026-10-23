@extends('layouts.admin')

@section('title', 'Pelanggan & Admin')
@section('subtitle', 'Kelola akun pelanggan dan akun admin')

@section('actions')
    <a href="{{ route('admin.users.create', ['role' => $role]) }}" class="btn-primary py-2 text-sm">
        <x-icon name="plus" class="h-4 w-4"/> Tambah {{ \App\Models\User::ROLES[$role] }}
    </a>
@endsection

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <div class="flex rounded-xl bg-white p-1 shadow-card">
            @foreach (\App\Models\User::ROLES as $value => $label)
                <a href="{{ route('admin.users.index', ['role' => $value]) }}"
                   class="rounded-lg px-4 py-1.5 text-sm font-medium {{ $role === $value ? 'bg-primary text-white' : 'text-grey hover:text-ink' }}">{{ $label }}</a>
            @endforeach
        </div>

        <form method="GET" class="flex flex-1 gap-3">
            <input type="hidden" name="role" value="{{ $role }}">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau telepon" class="admin-input max-w-sm">
            <button class="btn-primary py-2 text-sm">Cari</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-neutral">
                    <tr>
                        <th class="admin-th">Nama</th>
                        <th class="admin-th">Kontak</th>
                        <th class="admin-th">Lokasi</th>
                        @if ($role === 'user')
                            <th class="admin-th">Pesanan</th>
                            <th class="admin-th">Total Transaksi</th>
                        @endif
                        <th class="admin-th">Bergabung</th>
                        <th class="admin-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($users as $user)
                        <tr>
                            <td class="admin-td min-w-[200px]">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 flex-none items-center justify-center rounded-full bg-primary-50 text-xs font-semibold text-primary">{{ $user->initials() }}</span>
                                    <span class="font-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="admin-td">{{ $user->email }}<span class="block text-xs text-grey">{{ $user->phone ?? '-' }}</span></td>
                            <td class="admin-td">{{ $user->location?->name ?? '-' }}</td>
                            @if ($role === 'user')
                                <td class="admin-td">{{ $user->bookings_count }}</td>
                                <td class="admin-td whitespace-nowrap">{{ rupiah($user->total_spent ?? 0, false) }}</td>
                            @endif
                            <td class="admin-td whitespace-nowrap">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                            <td class="admin-td">
                                <div class="flex justify-end gap-2">
                                    @if ($role === 'user')
                                        <a href="{{ route('admin.chats.show', $user) }}" class="btn-outline">Chat</a>
                                    @endif
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-outline">Edit</a>
                                    @unless ($user->is(Auth::user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50">Hapus</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="admin-td py-10 text-center text-grey">Belum ada akun.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
@endsection
