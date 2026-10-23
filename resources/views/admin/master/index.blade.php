@extends('layouts.admin')

@section('title', $meta['title'])
@section('subtitle', $meta['subtitle'])

@section('actions')
    <a href="{{ route("admin.{$meta['route']}.create") }}" class="btn-primary py-2 text-sm"><x-icon name="plus" class="h-4 w-4"/> Tambah</a>
@endsection

@section('content')
    @php [$usageRelation, $usageLabel] = $meta['usage']; @endphp

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-neutral">
                    <tr>
                        <th class="admin-th">Nama</th>
                        @foreach ($extraFields as [$label])
                            <th class="admin-th">{{ $label }}</th>
                        @endforeach
                        <th class="admin-th">Urutan</th>
                        <th class="admin-th">Dipakai</th>
                        <th class="admin-th">Status</th>
                        <th class="admin-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($items as $item)
                        <tr>
                            <td class="admin-td font-medium">{{ $item->name }}</td>
                            @foreach ($extraFields as $field => [, $options])
                                <td class="admin-td">{{ $options[$item->$field] }}</td>
                            @endforeach
                            <td class="admin-td">{{ $item->sort_order }}</td>
                            <td class="admin-td">{{ $item->{"{$usageRelation}_count"} }} {{ $usageLabel }}</td>
                            <td class="admin-td">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $item->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-grey' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="admin-td">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route("admin.{$meta['route']}.edit", $item) }}" class="btn-outline">Edit</a>
                                    <form method="POST" action="{{ route("admin.{$meta['route']}.destroy", $item) }}" onsubmit="return confirm('Hapus {{ $item->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ 5 + count($extraFields) }}" class="admin-td py-10 text-center text-grey">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
