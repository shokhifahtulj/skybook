<html>
<body>
    <h2>Booking Confirmation</h2>

    <p>PNR: {{ $booking->pnr }}</p>
    <p>Status: {{ $booking->booking_status }}</p>
    <p>Jumlah tiket: {{ $booking->jumlah_tiket }}</p>

    @if($booking->schedule)
        <p>Jadwal: {{ optional($booking->schedule)->tanggal ?? 'N/A' }} {{ optional($booking->schedule)->jam_berangkat ?? '' }}</p>
    @endif

    <p>Terima kasih telah melakukan booking.</p>
</body>
</html>
