<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Ticket {{ $ticket->ticket_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #0ea5e9; padding-bottom: 10px; margin-bottom: 20px; }
        .airline-logo { font-size: 24px; font-weight: bold; color: #0ea5e9; }
        .row { width: 100%; display: table; margin-bottom: 15px; }
        .col-half { width: 50%; display: table-cell; }
        .box { border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; background-color: #f8fafc; }
        .label { font-size: 12px; color: #64748b; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        .qr-box { text-align: right; }
        .footer { margin-top: 40px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header row">
        <div class="col-half airline-logo">
            {{ $snapshot['airline_name'] ?? 'Airlines' }}
        </div>
        <div class="col-half" style="text-align: right;">
            <div class="label">Booking Reference (PNR)</div>
            <div style="font-size: 24px; font-weight: bold; letter-spacing: 2px;">{{ $snapshot['booking_reference'] ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-half">
            <div class="label">Passenger Name</div>
            <div class="value">{{ $snapshot['passenger_name'] ?? 'N/A' }}</div>
            
            <div class="label">Ticket Number</div>
            <div class="value">{{ $ticket->ticket_number }}</div>
            
            <div class="label">Flight Number</div>
            <div class="value">{{ $snapshot['flight_number'] ?? 'N/A' }}</div>
        </div>
        <div class="col-half qr-box">
            <img src="{{ $qrCode }}" alt="QR Code" width="120">
            <div style="font-size: 10px; margin-top: 5px;">Scan to Verify</div>
        </div>
    </div>

    <div class="box">
        <div class="row">
            <div class="col-half">
                <div class="label">From</div>
                <div class="value">{{ $snapshot['origin'] ?? 'N/A' }}</div>
                <div class="label">Departure</div>
                <div class="value">{{ $snapshot['departure_time'] ?? 'N/A' }}</div>
            </div>
            <div class="col-half">
                <div class="label">To</div>
                <div class="value">{{ $snapshot['destination'] ?? 'N/A' }}</div>
                <div class="label">Arrival</div>
                <div class="value">{{ $snapshot['arrival_time'] ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 0;">
            <div class="col-half">
                <div class="label">Class</div>
                <div class="value" style="margin-bottom: 0;">{{ ucfirst($snapshot['cabin_class'] ?? 'Economy') }}</div>
            </div>
            <div class="col-half">
                <div class="label">Seat</div>
                <div class="value" style="margin-bottom: 0;">{{ $snapshot['seat_number'] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        This is an electronically generated ticket. Issued at {{ $ticket->issued_at?->format('d M Y H:i:s') ?? now()->format('d M Y H:i:s') }}.
    </div>
</body>
</html>
