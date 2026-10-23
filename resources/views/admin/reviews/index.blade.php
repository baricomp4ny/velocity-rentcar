@extends('layouts.admin')

@section('title', 'Ulasan')
@section('subtitle', 'Penilaian pelanggan yang membentuk rating setiap mobil')

@section('content')
    <form method="GET" class="mb-5">
        <select name="rating" class="admin-input w-auto" onchange="this.form.submit()">
            <option value="">Semua bintang</option>
            @for ($star = 5; $star >= 1; $star--)
                <option value="{{ $star }}" @selected((int) request('rating') === $star)>{{ $star }} bintang</option>
            @endfor
        </select>
    </form>

    <div class="space-y-3">
        @forelse ($reviews as $booking)
            <div class="card flex flex-wrap items-start gap-4 p-5">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-semibold">{{ $booking->user->name }}</p>
                        <span class="text-xs text-grey">· {{ $booking->car->name }} · {{ $booking->code }}</span>
                    </div>
                    <p class="mt-1 flex gap-0.5">
                        @for ($star = 1; $star <= 5; $star++)
                            <x-icon name="star" class="h-4 w-4 {{ $star <= $booking->rating ? 'text-amber-400' : 'text-gray-200' }}"/>
                        @endfor
                    </p>
                    <p class="mt-2 text-sm text-ink/80">{{ $booking->review ?: 'Tanpa komentar.' }}</p>
                </div>
                <form method="POST" action="{{ route('admin.reviews.destroy', $booking) }}" onsubmit="return confirm('Hapus ulasan ini?')">
                    @csrf @method('DELETE')
                    <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50">Hapus ulasan</button>
                </form>
            </div>
        @empty
            <p class="card p-10 text-center text-grey">Belum ada ulasan.</p>
        @endforelse
    </div>

    <div class="mt-5">{{ $reviews->links() }}</div>
@endsection
