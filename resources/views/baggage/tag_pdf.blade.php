<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Baggage Tag {{ $tag->tag_number }}</title>
    <style>
        @page {
            margin: 0;
            size: 576pt 108pt; /* landscape 8x1.5 inches */
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background: #fff;
            color: #000;
        }
        .container {
            width: 100%;
            height: 100%;
            display: table;
        }
        .left {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
            text-align: center;
            border-right: 2px solid #000;
            padding-right: 10px;
        }
        .middle {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            padding-left: 20px;
        }
        .right {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
            text-align: right;
        }
        .dest {
            font-size: 60px;
            font-weight: 900;
            line-height: 1;
            margin: 0;
        }
        .flight {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .passenger {
            font-size: 16px;
            margin: 5px 0;
        }
        .tag-num {
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
        }
        .weight {
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #000;
            border-radius: 50%;
            padding: 5px;
            display: inline-block;
            margin-top: 5px;
        }
        .qr-img {
            width: 80px;
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h1 class="dest">{{ $flight->route->destination->iata_code }}</h1>
            <div class="flight">{{ $flight->flight_number }}</div>
            <div class="weight">{{ (int) $tag->weight_kg }} KG</div>
        </div>
        <div class="middle">
            <div class="passenger">{{ strtoupper($passenger->last_name) }}/{{ strtoupper(substr($passenger->first_name, 0, 1)) }}</div>
            <div class="tag-num">{{ $tag->tag_number }}</div>
            <div style="font-size: 10px; margin-top: 10px;">SKYBOOK AIRLINES</div>
        </div>
        <div class="right">
            <img class="qr-img" src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
        </div>
    </div>
</body>
</html>
