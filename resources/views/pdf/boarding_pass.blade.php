<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Boarding Pass - {{ $boardingPass->boarding_pass_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #0f172a;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #059669; /* Emerald 600 */
            color: #ffffff;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .flight-info {
            display: table;
            width: 100%;
            padding: 20px;
            border-bottom: 2px dashed #cbd5e1;
            box-sizing: border-box;
        }
        .flight-col {
            display: table-cell;
            width: 33.33%;
            vertical-align: middle;
        }
        .flight-col.left { text-align: left; }
        .flight-col.center { text-align: center; }
        .flight-col.right { text-align: right; }
        
        .iata { font-size: 36px; font-weight: 900; color: #0f172a; line-height: 1; }
        .city { font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-top: 4px; }
        
        .icon { font-size: 24px; color: #94a3b8; }
        
        .details-grid {
            display: table;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            background-color: #f8fafc;
        }
        .detail-item {
            display: table-cell;
            width: 25%;
            text-align: center;
        }
        .detail-label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .detail-value { font-size: 20px; font-weight: 900; color: #0f172a; }
        
        .passenger-info {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .passenger-name { font-size: 18px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; }
        .passenger-flight { font-size: 14px; color: #475569; font-weight: 600; }
        
        .qr-section {
            padding: 30px 20px;
            text-align: center;
            background-color: #ffffff;
        }
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto;
        }
        .bp-number {
            margin-top: 15px;
            font-size: 12px;
            color: #94a3b8;
            font-family: monospace;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Boarding Pass</h1>
            <p>{{ $boardingPass->segmentPassenger->segment->schedule->flight->airline->name ?? 'AIRLINE' }}</p>
        </div>

        <!-- Route -->
        @php
            $origin = $boardingPass->segmentPassenger->segment->schedule->flight->route->origin;
            $dest = $boardingPass->segmentPassenger->segment->schedule->flight->route->destination;
        @endphp
        <div class="flight-info">
            <div class="flight-col left">
                <div class="iata">{{ $origin->iata_code }}</div>
                <div class="city">{{ $origin->city }}</div>
            </div>
            <div class="flight-col center">
                <div class="icon">✈</div>
            </div>
            <div class="flight-col right">
                <div class="iata">{{ $dest->iata_code }}</div>
                <div class="city">{{ $dest->city }}</div>
            </div>
        </div>

        <!-- Passenger -->
        <div class="passenger-info">
            <div class="passenger-name">
                {{ $boardingPass->segmentPassenger->passenger->first_name }} {{ $boardingPass->segmentPassenger->passenger->last_name }}
            </div>
            <div class="passenger-flight">
                Flight: {{ $boardingPass->segmentPassenger->segment->schedule->flight->flight_number }} • 
                {{ $boardingPass->segmentPassenger->segment->schedule->departure_datetime->format('d M Y') }}
            </div>
        </div>

        <!-- Details Grid -->
        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-label">Gate</div>
                <div class="detail-value">{{ $boardingPass->gate_snapshot ?? '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Boarding</div>
                <div class="detail-value">{{ $boardingPass->boarding_time_snapshot ? $boardingPass->boarding_time_snapshot->format('H:i') : '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Zone</div>
                <div class="detail-value">{{ $boardingPass->boarding_group_snapshot ?? '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Seat</div>
                <div class="detail-value">{{ $boardingPass->segmentPassenger->seat->seat_number ?? '-' }}</div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="qr-section">
            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(200)->generate($qrPayload)) }}" alt="QR Code" width="200" height="200">
            </div>
            <div class="bp-number">{{ $boardingPass->boarding_pass_number }}</div>
        </div>
    </div>
</body>
</html>
