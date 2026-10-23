@extends('layouts.admin')

@section('title', 'Mobil')
@section('subtitle', 'Kelola data kendaraan yang disewakan')

@section('actions')
    <a href="{{ route('admin.cars.create') }}" class="btn-primary py-2 text-sm"><x-icon name="plus" class="h-4 w-4"/> Tambah Mobil</a>
@endsection

@section('content')
    <form method="GET" class="mb-5 flex flex-wrap gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama mobil" class="admin-input max-w-xs">
        <select name="brand" class="admin-input w-auto" onchange="this.form.submit()">
            <option value="">Semua merek</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(request('brand') === $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        <button class="btn-primary py-2 text-sm">Cari</button>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-neutral">
                    <tr>
                        <th class="admin-th">Mobil</th>
                        <th class="admin-th">Spesifikasi</th>
                        <th class="admin-th">Harga/hari</th>
                        <th class="admin-th">Rating</th>
                        <th class="admin-th">Status</th>
                        <th class="admin-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($cars as $car)
                        <tr class="hover:bg-neutral/60">
                            <td class="admin-td min-w-[260px]">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $car->imageUrl() }}" alt="" class="h-12 w-16 rounded-lg object-cover">
                                    <div>
                                        <p class="font-medium">{{ $car->name }}</p>
                                        <p class="text-xs text-grey">{{ $car->brand->name }} · {{ $car->bookings_count }} pesanan</p>
                                    </div>
                                </div>
                            </td>
                            <td class="admin-td min-w-[170px]">
                                <span class="chip">{{ $car->category->name }}</span>
                                <span class="mt-1 block text-xs text-grey">{{ $car->transmission }} · {{ $car->fuel }} · {{ $car->seats }} kursi</span>
                            </td>
                            <td class="admin-td whitespace-nowrap font-medium">{{ rupiah($car->price_per_day, false) }}</td>
                            <td class="admin-td"><span class="flex items-center gap-1"><x-icon name="star" class="h-4 w-4 text-amber-400"/>{{ $car->ratingLabel() }}</span></td>
                            <td class="admin-td min-w-[150px]">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $car->is_available ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-grey' }}">{{ $car->is_available ? 'Tersedia' : 'Nonaktif' }}</span>
                                @if ($car->is_popular) <span class="ml-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-600">Populer</span> @endif
                            </td>
                            <td class="admin-td">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.cars.edit', $car) }}" class="btn-outline">Edit</a>
                                    <form method="POST" action="{{ route('admin.cars.destroy', $car) }}" onsubmit="return confirm('Hapus {{ $car->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="admin-td py-10 text-center text-grey">Belum ada mobil.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $cars->links() }}</div>
@endsection
