@extends('layouts.admin')

@section('title', 'Chat Customer Service')
@section('subtitle', 'Balas pertanyaan pelanggan (live chat 24/7)')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        @include('admin.chats._threads')
        <div class="card hidden items-center justify-center p-10 text-center text-grey lg:col-span-2 lg:flex">
            <div>
                <x-icon name="chat" class="mx-auto h-12 w-12 text-primary-100"/>
                <p class="mt-3">Pilih percakapan untuk mulai membalas.</p>
            </div>
        </div>
    </div>
@endsection
