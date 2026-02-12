<?php



use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;  // Add this import


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register the command
Schedule::command('app:send-order-reminders')->everyMinute(); // Change to everyFiveHours() for production
