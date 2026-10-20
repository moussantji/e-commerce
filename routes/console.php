<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Envoi des emails/notifications en arrière-plan (mutualisé : cron
// cPanel `* * * * *` vers `php artisan schedule:run`). Sans chevauchement,
// s'arrête quand la file est vide : les pages ne wait plus le SMTP.
Schedule::command('queue:work database --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping()
    ->name('queue-worker');
