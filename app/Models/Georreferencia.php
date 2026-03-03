<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class Georreferencia extends Model
{
    use HasFactory;
    use HasUlids;
    protected $table = 'georreferencias';

    protected $fillable = [
        'device_id',
        'tipo',
        'lat',
        'lng',
        'accuracy',
        'battery',
        'request_at',

        'network_type',
        'network_generation',
        'signal_dbm',
        'signal_level'

    ];

    public static $tipos = [
        0 => 'Ubicacion',
        1 => 'Reporte Inicio Jornada',
        2 => 'Reporte Fin Jornada',
        3 => 'Desconexión de Red',
        4 => 'Cierre forzado',
        5 => 'inicio de almuerzo',
        6 => 'fin de almuerzo',
    ];

    public static $signalLevels = [
        0 => 'Sin señal',
        1 => 'Mala',
        2 => 'Regular',
        3 => 'Buena',
        4 => 'Excelente',
    ];

    public static $networkTypes = [
        0  => 'WiFi',
        1  => 'Datos móviles',
        2  => 'Sin conexión',
    ];

    public static $networkGenerations = [
        0 => '2G',
        1 => '3G',
        2 => '4G',
        3 => '5G',
    ];

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }
}