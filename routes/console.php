<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: Check popular people every 10 minutes
Schedule::command('people:check-popular')
    ->everyMinute()
    ->timezone('Asia/Jakarta')
    ->description('Check for popular people with more than 50 likes and send email notification');
