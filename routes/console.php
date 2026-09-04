<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Detecta el SOS del lado del servidor, sin depender de que alguien tenga
// la pantalla de Geolocalización abierta en el navegador. Para que esto
// realmente corra hace falta programar `php artisan schedule:run` cada
// minuto (Programador de tareas de Windows, o `schedule:work` mientras se
// prueba en local) — el scheduler de Laravel no se dispara solo.
Schedule::command('sos:revisar')->everyMinute()->withoutOverlapping();
