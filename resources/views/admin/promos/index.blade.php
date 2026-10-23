@extends('layouts.admin')

@section('title', 'Kode Promo')
@section('subtitle', 'Kode promo yang bisa dipakai di Rental Summary')

@section('actions')
    <a href="{{ route('admin.promos.create') }}" class="btn-primary py-2 text-sm"><x-icon name="plus" class="h-4 w-4"/> Tambah Promo</a>
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-neutral">
                    <tr>
                        <th class="admin-th">Kode</th>
                        <th class="admin-th">Diskon</th>
                        <th class="admin-th">Berlaku sampai</th>
                        <th class="admin-th">Status</th>
                        <th class="admin-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($promos as $promo)
                        <tr>
                            <td class="admin-td">
                                <span class="font-semibold tracking-wide">{{ $promo->code }}</span>
                                <span class="block text-xs text-grey">{{ $promo->description }}</span>
                            </td>
                            <td class="admin-td">{{ $promo->discount_percent }}%{{ $promo->max_discount ? ' (maks. '.rupiah($promo->max_discount, false).')' : '' }}</td>
                            <td class="admin-td">{{ $promo->expires_at?->translatedFormat('d F Y') ?? 'Tanpa batas' }}</td>
                            <td class="admin-td">
                                @php $expired = $promo->isExpired(); @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $promo->is_active && ! $expired ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-grey' }}">
                                    {{ $expired ? 'Kedaluwarsa' : ($promo->is_active ? 'Aktif' : 'Nonaktif') }}
                                </span>
                            </td>
                            <td class="admin-td">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.promos.edit', $promo) }}" class="btn-outline">Edit</a>
                                    <form method="POST" action="{{ route('admin.promos.destroy', $promo) }}" onsubmit="return confirm('Hapus promo {{ $promo->code }}?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="admin-td py-10 text-center text-grey">Belum ada kode promo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
