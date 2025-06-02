<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupMySQLDatabase extends Command
{
    protected $signature = 'backup:mysql';
    protected $description = 'Genera una copia de seguridad de la base de datos MySQL';

    public function handle()
    {
        Log::info('Comando backup:mysql iniciado.');

        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');
        $host = Config::get('database.connections.mysql.host');

        // Verifica que mysqldump esté disponible
        $mysqldumpPath = trim(shell_exec('which mysqldump'));
        if (empty($mysqldumpPath)) {
            $this->error('mysqldump no está instalado o no está en el PATH.');
            Log::error('mysqldump no encontrado.');
            return 1;
        }

        $backupDir = storage_path('backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Eliminar backups anteriores
        $archivos = File::files($backupDir);
        foreach ($archivos as $archivo) {
            if (str_contains($archivo->getFilename(), 'db_backup_')) {
                File::delete($archivo);
            }
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupPath = "{$backupDir}/db_backup_{$timestamp}.sql";

        // Comando mysqldump
        $command = [
            $mysqldumpPath,
            "--user={$username}",
            "--password={$password}",
            "--host={$host}",
            $database
        ];

        $process = new Process($command);
        $process->run();

        if ($process->isSuccessful()) {
            File::put($backupPath, $process->getOutput());
            $this->info("Copia de seguridad creada exitosamente: {$backupPath}");
            Log::info("Backup creado: {$backupPath}");
            return 0;
        } else {
            $this->error('Error al crear la copia de seguridad.');
            Log::error('Error en el backup: ' . $process->getErrorOutput());
            return 1;
        }
    }
}