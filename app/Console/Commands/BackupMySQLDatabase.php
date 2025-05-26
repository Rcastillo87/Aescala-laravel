<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class BackupMySQLDatabase extends Command
{
    protected $signature = 'backup:mysql';
    protected $description = 'Genera una copia de seguridad de la base de datos MySQL';

    public function handle()
    {
        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');
        $host = Config::get('database.connections.mysql.host');

        $backupDir = storage_path('backups');

        // Asegúrate de que el directorio exista
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Eliminar el archivo anterior (si existe)
        $archivos = File::files($backupDir);
        foreach ($archivos as $archivo) {
            if (str_contains($archivo->getFilename(), 'db_backup_')) {
                File::delete($archivo);
            }
        }

        // Crear nuevo nombre de archivo
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupPath = "{$backupDir}/db_backup_{$timestamp}.sql";

        $command = "mysqldump --user={$username} --password=\"{$password}\" --host={$host} {$database} > {$backupPath}";

        $result = null;
        system($command, $result);

        if ($result === 0) {
            $this->info("Copia de seguridad creada exitosamente: {$backupPath}");
        } else {
            $this->error("Error al crear la copia de seguridad.");
        }
    }

}

