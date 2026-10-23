@extends('layouts.admin')

@section('title', 'Chat Customer Service')
@section('subtitle', 'Balas pertanyaan pelanggan (live chat 24/7)')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="hidden lg:block">@include('admin.chats._threads')</div>

        <div class="card flex h-[75vh] flex-col overflow-hidden lg:col-span-2" x-data="chatRoom(@js([
                'messages' => $messages,
                'pollUrl' => route('admin.chats.messages', $customer),
                'sendUrl' => route('admin.chats.store', $customer),
            ]))">
            <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                <a href="{{ route('admin.chats.index') }}" class="lg:hidden"><x-icon name="chevron-left" class="h-5 w-5"/></a>
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-50 text-xs font-semibold text-primary">{{ $customer->initials() }}</span>
                <div>
                    <p class="font-semibold">{{ $customer->name }}</p>
                    <p class="text-xs text-grey">{{ $customer->email }} · {{ $customer->phone ?? '-' }}</p>
                </div>
            </div>

            <div x-ref="list" class="flex-1 space-y-3 overflow-y-auto bg-neutral px-5 py-5">
                <template x-for="m in messages" :key="m.id">
                    <div class="flex" :class="m.sender === 'admin' ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[70%] rounded-2xl px-4 py-2.5 text-sm shadow-sm"
                             :class="m.sender === 'admin' ? 'rounded-br-md bg-primary text-white' : 'rounded-bl-md bg-white'">
                            <p class="whitespace-pre-line break-words" x-text="m.body"></p>
                            <p class="mt-1 text-right text-[10px]" :class="m.sender === 'admin' ? 'text-white/70' : 'text-grey'" x-text="m.time"></p>
                        </div>
                    </div>
                </template>
            </div>

            <form @submit.prevent="send()" class="flex gap-3 border-t border-gray-100 px-5 py-4">
                <input x-model="draft" type="text" placeholder="Tulis balasan..." class="admin-input flex-1">
                <button class="btn-primary py-2" :disabled="!draft.trim() || sending"><x-icon name="send" class="h-4 w-4"/> Kirim</button>
            </form>
        </div>
    </div>
@endsection
