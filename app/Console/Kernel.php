<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Los comandos Artisan personalizados que se deben registrar.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\BackupMySQLDatabase::class,
    ];

    /**
     * Define la programación de tareas del sistema.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Ejecuta el comando backup:mysql todos los días a las 03:00 AM
        $schedule->command('backup:mysql')->dailyAt('03:00');
    }

    /**
     * Registra los comandos del directorio Commands.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
