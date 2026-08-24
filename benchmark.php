<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(1);

$start = microtime(true);
app('App\Http\Controllers\BookingController')->getBookingData();
echo 'Original: ' . (microtime(true) - $start) . 's' . PHP_EOL;
