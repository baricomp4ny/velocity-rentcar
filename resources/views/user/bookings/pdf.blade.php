<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Pembayaran {{ $booking->code }}</title>
    <style>
        @page { margin: 18px; }
        body { font-family: DejaVu Sans, sans-serif; color: #121212; font-size: 11px; }
        .ticket { border: 1px solid #B9DCFF; border-top: 8px solid #007DFC; border-radius: 12px; padding: 14px 16px; }
        .brand { font-weight: bold; font-size: 13px; }
        .muted { color: #888689; }
        .center { text-align: center; }
        .title { color: #007DFC; font-size: 16px; font-weight: bold; margin: 12px 0 4px; }
        .check { width: 54px; height: 54px; line-height: 50px; border: 5px solid #007DFC; border-radius: 50%; color: #007DFC; font-size: 28px; margin: 8px auto; }
        .cancelled .check { border-color: #EF4444; color: #EF4444; }
        .cancelled .title { color: #EF4444; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 6px 0; }
        td.value { text-align: right; color: #555; }
        .section { color: #007DFC; font-size: 13px; border-bottom: 1px solid #007DFC; padding-bottom: 4px; margin-top: 14px; }
        .total td { font-weight: bold; color: #121212; border-top: 1px dashed #ccc; padding-top: 8px; }
    </style>
</head>
<body>
    @php $cancelled = $booking->status === 'cancelled'; @endphp
    <div class="ticket {{ $cancelled ? 'cancelled' : '' }}">
        <div class="brand">{{ setting('store_name') }}</div>
        <div class="muted">{{ setting('store_tagline') }}</div>

        <div class="center">
            <div class="title">Info Bayar</div>
            <div class="check">{!! $cancelled ? '&#10005;' : '&#10003;' !!}</div>
            <div class="title">{{ $cancelled ? 'Pesanan Dibatalkan' : 'Pembayaran Sukses' }}</div>
            <div class="muted">No. Pesanan: {{ $booking->code }}</div>
        </div>

        <div class="section">Informasi Pembayaran</div>
        <table>
            <tr><td>Kendaraan</td><td class="value">{{ $booking->car->name }}</td></tr>
            <tr><td>Peminjam</td><td class="value">{{ $booking->user->name }}</td></tr>
            <tr><td>No. Telepon</td><td class="value">{{ $booking->user->phone }}</td></tr>
            <tr><td>Tanggal Pesan</td><td class="value">{{ tanggal($booking->created_at) }}</td></tr>
            <tr><td>Pengambilan</td><td class="value">{{ tanggal($booking->pickup_date) }} · {{ $booking->pickupLocation->name }}</td></tr>
            <tr><td>Pengembalian</td><td class="value">{{ tanggal($booking->return_date) }}</td></tr>
            <tr><td>Durasi Peminjaman</td><td class="value">{{ $booking->durationDays() }} Hari</td></tr>
            <tr><td>Harga/hari</td><td class="value">{{ rupiah($booking->price_per_day) }}</td></tr>
            @if ($booking->discount)
                <tr><td>Diskon{{ $booking->promo ? " ({$booking->promo->code})" : '' }}</td><td class="value">- {{ rupiah($booking->discount) }}</td></tr>
            @endif
            <tr><td>Pembayaran</td><td class="value">{{ $booking->paymentMethod->name }}</td></tr>
            <tr class="total"><td>Total Harga</td><td class="value">{{ rupiah($booking->total()) }}</td></tr>
        </table>

        <p class="muted center" style="margin-top:16px">Simpan bukti pembayaran ini dan tunjukkan saat pengambilan mobil.<br>Dicetak {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>
</html>
