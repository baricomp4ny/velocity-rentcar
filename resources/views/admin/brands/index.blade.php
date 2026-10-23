@extends('layouts.admin')

@section('title', 'Merek')
@section('subtitle', 'Merek yang tampil di menu Explore')

@section('actions')
    <a href="{{ route('admin.brands.create') }}" class="btn-primary py-2 text-sm"><x-icon name="plus" class="h-4 w-4"/> Tambah Merek</a>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @forelse ($brands as $brand)
            <div class="card p-5 text-center">
                <span class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-neutral">
                    @if ($brand->logoUrl())
                        <img src="{{ $brand->logoUrl() }}" alt="{{ $brand->name }}" class="h-full w-full object-contain p-4">
                    @else
                        <span class="text-2xl font-bold">{{ mb_substr($brand->name, 0, 1) }}</span>
                    @endif
                </span>
                <p class="mt-3 font-semibold text-primary">{{ $brand->name }}</p>
                <p class="text-xs text-grey">{{ $brand->cars_count }} mobil · urutan {{ $brand->sort_order }}</p>
                <div class="mt-4 flex justify-center gap-2">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn-outline">Edit</a>
                    <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Hapus merek {{ $brand->name }}?')">
                        @csrf @method('DELETE')
                        <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-grey">Belum ada merek.</p>
        @endforelse
    </div>
@endsection
