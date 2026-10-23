@extends('layouts.app')

@section('title', 'Notification')

@section('content')
    <x-page-header title="Notification" :back="route('home')"/>

    <div class="container-page lg:px-8">
    <div class="divide-y divide-gray-100 lg:mt-6 lg:overflow-hidden lg:rounded-2xl lg:bg-white lg:shadow-card">
        @forelse ($notifications as $notification)
            <a href="{{ $notification->url ?? '#' }}" class="flex gap-3 px-5 py-4 transition hover:bg-neutral {{ $notification->read_at ? '' : 'bg-primary-50/60' }}">
                <span class="flex h-11 w-11 flex-none items-center justify-center rounded-full bg-primary-50 text-primary">
                    <x-icon name="bell" class="h-5 w-5"/>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold">{{ $notification->title }}</p>
                        @unless ($notification->read_at)
                            <span class="mt-1.5 h-2 w-2 flex-none rounded-full bg-primary"></span>
                        @endunless
                    </div>
                    <p class="mt-0.5 text-xs text-grey">{{ $notification->body }}</p>
                    <p class="mt-1 text-[10px] text-grey">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </a>
        @empty
            <div class="px-6 py-20 text-center">
                <x-icon name="bell" class="mx-auto h-10 w-10 text-grey"/>
                <p class="mt-3 font-medium">Belum ada notifikasi</p>
            </div>
        @endforelse
    </div>
    </div>
@endsection
