<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sp = \App\Models\BookingSegmentPassenger::first();
$sp->update(['operational_status' => 'checked_in']);

$bpService = app(\App\Services\Operations\BoardingPassService::class);
$bpPdfService = app(\App\Services\Operations\BoardingPassPdfService::class);

$bp = clone $bpService->generate($sp); // Generates record & QR signature
$bpPdfService->generatePdf($bp);

echo $bp->id . '|' . $bp->qr_signature;
