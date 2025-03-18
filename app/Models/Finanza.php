<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finanza extends Model
{
    use HasFactory;

    protected $table = 'finanzas';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_proyecto',
        'concepto',
        'tipo',
        'valor'
    ];

    public static $tipo = [
        1 => 'Ingreso - Abono',
        2 => 'Egreso - Carpinteria',
        3 => 'Egreso Obra Blanca',
        4 => 'Egreso - Otros'
    ];

    public static $ClassTipo = [
        1 => 'span-green',
        2 => 'span-red',
        3 => 'span-red',
        4 => 'span-red'
    ];

    protected $appends = ['spanTipo'];

    public function getSpanTipoAttribute()
    {
        return '<span class="'.(self::$ClassTipo[$this->tipo] ?? 'default-class').'">'
             . (self::$tipo[$this->tipo] ?? 'Desconocido') . '</span>';
    }

    // Relación con el modelo Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }
}