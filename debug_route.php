<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$router = $app->make('router');
$request = Illuminate\Http\Request::create(route('flights.index', ['origin' => 'Jakarta', 'destination' => 'Denpasar', 'departure_date' => now()->addDay()->toDateString(), 'airline' => 'SkyBook Air']), 'GET');
$route = $router->getRoutes()->match($request);
var_export($route->gatherMiddleware());
