<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despachos extends Model
{
    use HasFactory;

    protected $table = 'inventario_solicituds';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'tipo',
        'id_material',
        'id_user',
        'id_proyecto',
        'cantidad',
        'valor_unidad'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$classTipo[$this->tipo] ?? 'default-class').'">'
             . (self::$estado[$this->tipo] ?? 'Desconocido') . '</span>';
    }

    public static $tipo = [
        1 => 'Despachado',
        2 => 'Devolucion'
    ];

    public static $classTipo = [
        1 => 'span-green',
        2 => 'span-red'
    ];
}