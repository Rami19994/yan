<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('competitions:close-expired-sales')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('competitions:notify-starting-soon')
    ->everyMinute()
    ->withoutOverlapping();
