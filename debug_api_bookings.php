<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::factory()->create();
$token = $user->createToken('debug')->plainTextToken;
\App\Models\Booking::factory()->create([
    'user_id' => $user->id,
    'booked_by' => $user->id,
    'booking_status' => 'confirmed',
    'payment_status' => 'paid',
]);

$request = Illuminate\Http\Request::create('/api/bookings', 'GET', ['search' => 'NOPE']);
$request->headers->set('Authorization', 'Bearer '.$token);
$response = $kernel->handle($request);
echo $response->getStatusCode(), PHP_EOL;
echo $response->getContent();
$kernel->terminate($request, $response);
