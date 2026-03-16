<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BackupMySQLDatabase extends Command
{
    protected $signature = 'backup:mysql';
    protected $description = 'Genera una copia de seguridad de la base de datos MySQL y conserva los últimos 7 días';

    public function handle()
    {
        Log::info('Comando backup:mysql iniciado.');

        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');
        $host     = Config::get('database.connections.mysql.host');

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

        // ✅ CORRECCIÓN: eliminar solo archivos con más de 7 días, no todos
        $archivos = File::files($backupDir);
        foreach ($archivos as $archivo) {
            $esBackup    = str_contains($archivo->getFilename(), 'db_backup_');
            $tieneExtension = str_ends_with($archivo->getFilename(), '.sql') ||
                              str_ends_with($archivo->getFilename(), '.sql.gz');

            if ($esBackup && $tieneExtension) {
                $diasDeVida = now()->diffInDays(
                    \Carbon\Carbon::createFromTimestamp($archivo->getMTime())
                );

                if ($diasDeVida >= 7) {
                    File::delete($archivo);
                    Log::info("Backup antiguo eliminado: {$archivo->getFilename()}");
                }
            }
        }

        // Crear el nuevo backup
        $timestamp  = now()->format('Y-m-d_H-i-s');
        $backupPath = "{$backupDir}/db_backup_{$timestamp}.sql";

        $command = [
            $mysqldumpPath,
            "--user={$username}",
            "--password={$password}",
            "--host={$host}",
            $database
        ];

        $process = new Process($command);
        $process->setTimeout(300); // 5 minutos por si la BD es grande
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
