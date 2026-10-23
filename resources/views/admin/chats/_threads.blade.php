<div class="card overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-4 font-semibold">Percakapan</div>
    <div class="max-h-[70vh] divide-y divide-gray-100 overflow-y-auto">
        @forelse ($threads as $thread)
            @php $isActive = isset($customer) && $customer->id === $thread['user']->id; @endphp
            <a href="{{ route('admin.chats.show', $thread['user']) }}" class="flex items-center gap-3 px-5 py-3.5 transition {{ $isActive ? 'bg-primary-50' : 'hover:bg-neutral' }}">
                <span class="flex h-10 w-10 flex-none items-center justify-center rounded-full bg-primary-50 text-xs font-semibold text-primary">{{ $thread['user']->initials() }}</span>
                <span class="min-w-0 flex-1">
                    <span class="flex items-center justify-between gap-2">
                        <span class="truncate text-sm font-medium">{{ $thread['user']->name }}</span>
                        <span class="flex-none text-[10px] text-grey">{{ $thread['last']->created_at->diffForHumans(short: true) }}</span>
                    </span>
                    <span class="flex items-center justify-between gap-2">
                        <span class="truncate text-xs text-grey">{{ $thread['last']->sender === 'admin' ? 'Anda: ' : '' }}{{ $thread['last']->body }}</span>
                        @if ($thread['unread'])
                            <span class="flex h-5 min-w-[20px] flex-none items-center justify-center rounded-full bg-primary px-1.5 text-[10px] text-white">{{ $thread['unread'] }}</span>
                        @endif
                    </span>
                </span>
            </a>
        @empty
            <p class="px-5 py-10 text-center text-sm text-grey">Belum ada chat dari pelanggan.</p>
        @endforelse
    </div>
</div>
