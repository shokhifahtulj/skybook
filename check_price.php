<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$schedule = App\Models\FlightSchedule::find('019e5ec3-4db2-7374-ab51-daec273de442');
if (! $schedule) { echo 'NO_SCHEDULE'; exit; }
echo $schedule->prices()->count();
echo PHP_EOL;
