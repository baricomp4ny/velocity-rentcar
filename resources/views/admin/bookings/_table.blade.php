<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-neutral">
            <tr>
                <th class="admin-th">Kode</th>
                <th class="admin-th">Penyewa</th>
                <th class="admin-th">Mobil</th>
                <th class="admin-th">Jadwal</th>
                <th class="admin-th">Total</th>
                <th class="admin-th">Status</th>
                <th class="admin-th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse ($bookings as $booking)
                <tr class="hover:bg-neutral/60">
                    <td class="admin-td font-medium">{{ $booking->code }}</td>
                    <td class="admin-td">
                        {{ $booking->user->name }}
                        <span class="block text-xs text-grey">{{ $booking->user->phone }}</span>
                    </td>
                    <td class="admin-td">{{ $booking->car->name }}</td>
                    <td class="admin-td whitespace-nowrap">
                        {{ $booking->pickup_date->translatedFormat('d M Y') }}
                        <span class="block text-xs text-grey">{{ $booking->durationDays() }} hari · {{ $booking->pickupLocation->name }}</span>
                    </td>
                    <td class="admin-td whitespace-nowrap font-medium">{{ rupiah($booking->total(), false) }}</td>
                    <td class="admin-td"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium {{ $booking->statusColor() }}">{{ $booking->statusLabel() }}</span></td>
                    <td class="admin-td text-right"><a href="{{ route('admin.bookings.show', $booking) }}" class="btn-outline">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="admin-td py-10 text-center text-grey">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
