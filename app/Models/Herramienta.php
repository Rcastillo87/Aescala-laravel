<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Herramienta extends Model
{
    use HasFactory;

    protected $table = 'herramientas';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'nombre_herramienta',
        'referencia',
        'observacion',
        'marca',
        'estado'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estado[$this->estado] ?? 'Desconocido') . '</span>';
    }

    public static $estado = [
        1 => 'Nuevo',
        2 => 'Bueno',
        3 => 'Regular',
        4 => 'Dado de Baja'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-red',
        3 => 'span-yellow',
        4 => 'span-black'
    ];

    public function prestamo()
    {
        return $this->hasOne(HerramientaPrestamo::class, 'id_herramienta')->latestOfMany();
    }

}