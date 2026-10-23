@extends('layouts.admin')

@section('title', 'Pesanan')
@section('subtitle', 'Kelola semua pemesanan mobil')

@section('content')
    <form method="GET" class="mb-5 flex flex-wrap gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kode, nama penyewa, atau mobil" class="admin-input max-w-sm">
        <select name="status" class="admin-input w-auto" onchange="this.form.submit()">
            <option value="">Semua status</option>
            @foreach (\App\Models\Booking::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-primary py-2 text-sm">Cari</button>
    </form>

    <div class="card overflow-hidden">
        @include('admin.bookings._table')
    </div>

    <div class="mt-5">{{ $bookings->links() }}</div>
@endsection
