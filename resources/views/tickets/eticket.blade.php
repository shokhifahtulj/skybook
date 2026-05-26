<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SkyBook Air E-ticket</title>
    <style>
        :root {
            --skybook-blue: #0f68d6;
            --skybook-blue-soft: #dff2ff;
            --skybook-navy: #0f172a;
            --skybook-slate: #475569;
            --skybook-border: #cbd5e1;
            --skybook-white: #ffffff;
            --skybook-shadow: 0 24px 80px rgba(15, 23, 42, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #e2e8f0;
            color: var(--skybook-navy);
            font-family: Arial, Helvetica, sans-serif;
            padding: 24px;
        }

        .ticket-shell {
            max-width: 980px;
            margin: 0 auto;
            background: var(--skybook-white);
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid var(--skybook-border);
            box-shadow: var(--skybook-shadow);
        }

        .ticket-header {
            background: linear-gradient(90deg, #0f68d6 0%, #0f172a 100%);
            color: #fff;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .logo-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .logo-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: rgba(255,255,255,0.18);
            font-weight: 700;
            font-size: 20px;
            letter-spacing: 0.08em;
        }

        .brand-name {
            margin: 8px 0 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .brand-subtitle {
            margin: 2px 0 0;
            font-size: 13px;
            color: rgba(255,255,255,0.85);
        }

        .title-meta {
            text-align: right;
        }

        .title-meta h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
        }

        .flight-label {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
        }

        .ticket-body {
            padding: 28px;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1.1fr 1.1fr 1fr;
            gap: 18px;
        }

        .info-card {
            border: 1px solid var(--skybook-border);
            border-radius: 20px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .info-label {
            display: block;
            color: var(--skybook-slate);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--skybook-navy);
            margin: 0;
        }

        .info-value.large {
            font-size: 19px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 12px;
            border-radius: 999px;
            padding: 8px 14px;
            background: #dcfce7;
            color: #166534;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .route-block {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .route-block .info-value {
            font-size: 18px;
        }

        .reminder-box {
            margin-top: 22px;
            border-radius: 20px;
            background: var(--skybook-blue-soft);
            padding: 18px 20px;
            border: 1px solid rgba(15, 104, 214, 0.15);
        }

        .reminder-box h2 {
            margin: 0;
            font-size: 16px;
            color: var(--skybook-navy);
        }

        .reminder-list {
            margin: 12px 0 0;
            padding-left: 18px;
            color: var(--skybook-slate);
            line-height: 1.7;
        }

        .passenger-section {
            margin-top: 24px;
        }

        .section-title {
            margin: 0 0 12px;
            font-size: 18px;
            font-weight: 800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--skybook-border);
        }

        th {
            text-align: left;
            background: #0f172a;
            color: #fff;
            padding: 12px 14px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            padding: 14px;
            border-top: 1px solid var(--skybook-border);
            font-size: 14px;
            color: var(--skybook-navy);
        }

        .support-section {
            margin-top: 24px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
            align-items: center;
        }

        .support-panel {
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid var(--skybook-border);
            padding: 18px;
        }

        .support-panel h3 {
            margin: 0 0 8px;
            font-size: 16px;
        }

        .support-row {
            display: flex;
            gap: 8px;
            color: var(--skybook-slate);
            margin-top: 8px;
            font-size: 14px;
        }

        .qr-block {
            text-align: center;
            padding: 18px;
            border-radius: 20px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid var(--skybook-border);
        }

        .qr-block img {
            width: 180px;
            height: 180px;
            display: inline-block;
        }

        .qr-caption {
            margin-top: 10px;
            font-size: 12px;
            color: var(--skybook-slate);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .footer-note {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
            color: var(--skybook-slate);
            padding-bottom: 8px;
        }

        .no-print {
            margin: 0 auto 16px;
            max-width: 980px;
            display: flex;
            justify-content: flex-end;
        }

        .print-button {
            border: none;
            border-radius: 999px;
            background: #0f68d6;
            color: #fff;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .ticket-shell {
                box-shadow: none;
                border: none;
                border-radius: 0;
            }

            @page {
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" class="print-button" onclick="window.print()">Print E-ticket</button>
    </div>

    <div class="ticket-shell">
        <div class="ticket-header">
            <div class="logo-block">
                <div class="logo-mark">SB</div>
                <div>
                    <div class="brand-name">SkyBook Air</div>
                    <div class="brand-subtitle">Modern travel, made simple</div>
                </div>
            </div>

            <div class="title-meta">
                <h1>E-ticket</h1>
                <div class="flight-label">Departure Flight</div>
            </div>
        </div>

        <div class="ticket-body">
            <div class="grid-layout">
                <div class="info-card">
                    <span class="info-label">Airline</span>
                    <p class="info-value">SkyBook Air</p>
                    <span class="info-label" style="margin-top: 14px;">Flight number</span>
                    <p class="info-value">{{ $snapshot['flight_number'] ?? 'N/A' }}</p>
                    <span class="info-label" style="margin-top: 14px;">Cabin</span>
                    <p class="info-value">{{ ucfirst(str_replace('_', ' ', $snapshot['cabin'] ?? 'economy')) }}</p>
                </div>

                <div class="info-card">
                    <span class="info-label">Date</span>
                    <p class="info-value large">{{ $snapshot['departure_date'] ?? 'Tanggal belum tersedia' }}</p>
                    <div class="route-block">
                        <div>
                            <span class="info-label">Departure</span>
                            <p class="info-value">{{ $snapshot['origin_code'] ?? 'N/A' }}</p>
                            <p class="info-value" style="font-size: 14px; margin-top: 6px;">{{ $snapshot['origin_airport'] ?? 'N/A' }}</p>
                            <p class="info-value" style="font-size: 14px; margin-top: 6px;">{{ $snapshot['departure_time'] ?? 'Tidak tersedia' }}</p>
                        </div>
                        <div>
                            <span class="info-label">Arrival</span>
                            <p class="info-value">{{ $snapshot['destination_code'] ?? 'N/A' }}</p>
                            <p class="info-value" style="font-size: 14px; margin-top: 6px;">{{ $snapshot['destination_airport'] ?? 'N/A' }}</p>
                            <p class="info-value" style="font-size: 14px; margin-top: 6px;">{{ $snapshot['arrival_time'] ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <span class="info-label">Booking ID</span>
                    <p class="info-value">{{ $snapshot['booking_id'] ?? $booking->id ?? 'N/A' }}</p>
                    <span class="info-label" style="margin-top: 14px;">PNR</span>
                    <p class="info-value">{{ $snapshot['pnr'] ?? $booking->pnr ?? 'N/A' }}</p>
                    <span class="status-badge">Ticketed</span>
                </div>
            </div>

            <div class="reminder-box">
                <h2>Travel reminders</h2>
                <ul class="reminder-list">
                    <li>Show e-ticket and valid ID at the airport.</li>
                    <li>Check-in 90 minutes before departure.</li>
                    <li>All times shown are local airport time.</li>
                </ul>
            </div>

            <div class="passenger-section">
                <h2 class="section-title">Passenger details</h2>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Passenger</th>
                            <th>Route</th>
                            <th>Seat</th>
                            <th>Flight Facilities</th>
                            <th>Ticket Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($snapshot['rows'] ?? [] as $row)
                            <tr>
                                <td>{{ $row['no'] ?? '-' }}</td>
                                <td>{{ $row['passenger'] ?? 'N/A' }}</td>
                                <td>{{ $row['route'] ?? 'N/A' }}</td>
                                <td>{{ $row['seat'] ?? 'N/A' }}</td>
                                <td>{{ $row['facilities'] ?? '7kg cabin baggage' }}</td>
                                <td>{{ $row['ticket_number'] ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No passenger data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="support-section">
                <div class="support-panel">
                    <h3>Support information</h3>
                    <div class="support-row"><strong>Website</strong><span>www.skybook.example</span></div>
                    <div class="support-row"><strong>Email</strong><span>support@skybook.example</span></div>
                    <div class="support-row"><strong>Phone</strong><span>+62 21 555 0000</span></div>
                </div>

                <div class="qr-block">
                    <img src="{{ $qrCode }}" alt="QR Code" />
                    <div class="qr-caption">Scan to verify ticket</div>
                </div>
            </div>

            <div class="footer-note">Thank you for choosing SkyBook Air</div>
        </div>
    </div>
</body>
</html>
