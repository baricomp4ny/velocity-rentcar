@extends('layouts.app')

@section('title', 'Chat')
@section('nav', 'chat')

@section('content')
    <div class="flex h-screen flex-col lg:mx-auto lg:h-[calc(100vh-72px)] lg:max-w-4xl lg:py-6" x-data="chatRoom(@js([
            'messages' => $messages,
            'draft' => $prefill ?? '',
            'pollUrl' => route('chat.messages'),
            'sendUrl' => route('chat.store'),
        ]))">
        <header class="flex items-center gap-3 border-b border-gray-100 bg-white px-4 py-3 lg:rounded-t-2xl lg:px-6">
            <a href="{{ route('home') }}" class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-neutral" aria-label="Kembali">
                <x-icon name="chevron-left" class="h-6 w-6"/>
            </a>
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white">CS</span>
            <div class="flex-1">
                <p class="text-sm font-semibold">Customer Service</p>
                <p class="flex items-center gap-1 text-[11px] text-emerald-600"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Live chat 24/7</p>
            </div>
            <a href="tel:+{{ setting('cs_phone') }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-50 text-primary" aria-label="Telpon CS">
                <x-icon name="phone" class="h-4 w-4"/>
            </a>
        </header>

        <div x-ref="list" class="flex-1 space-y-3 overflow-y-auto bg-neutral px-4 py-5 lg:border-x lg:border-gray-100 lg:bg-[#F9FAFB] lg:px-6">
            <p class="mx-auto w-fit rounded-full bg-white px-3 py-1 text-[10px] text-grey">Tanyakan ketersediaan mobil, harga, atau bantuan pemesanan</p>
            <template x-for="m in messages" :key="m.id">
                <div class="flex" :class="m.sender === 'user' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[78%] rounded-2xl px-4 py-2.5 text-sm shadow-sm"
                         :class="m.sender === 'user' ? 'rounded-br-md bg-primary text-white' : 'rounded-bl-md bg-white text-ink'">
                        <p class="whitespace-pre-line break-words" x-text="m.body"></p>
                        <p class="mt-1 text-right text-[10px]" :class="m.sender === 'user' ? 'text-white/70' : 'text-grey'" x-text="m.time"></p>
                    </div>
                </div>
            </template>
        </div>

        <form @submit.prevent="send()" class="flex items-center gap-2 border-t border-gray-100 bg-white px-4 py-3 lg:rounded-b-2xl lg:px-6">
            <input x-model="draft" type="text" placeholder="Tulis pesan..." maxlength="1000"
                   class="flex-1 rounded-full border-0 bg-neutral px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            <button type="submit" :disabled="!draft.trim() || sending" class="flex h-11 w-11 items-center justify-center rounded-full bg-primary text-white disabled:opacity-40" aria-label="Kirim">
                <x-icon name="send" class="h-5 w-5"/>
            </button>
        </form>
    </div>
@endsection
