<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::factory()->create();
Auth::login($user);

$request = Illuminate\Http\Request::create(route('flights.index', [
    'origin' => 'Jakarta',
    'destination' => 'Denpasar',
    'departure_date' => now()->addDay()->toDateString(),
    'airline' => 'SkyBook Air',
]), 'GET');

$response = $kernel->handle($request);
echo $response->getStatusCode(), PHP_EOL;
echo $response->getContent();
$kernel->terminate($request, $response);
