<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioMaterial extends Model
{
    use HasFactory;

    protected $table = 'inventario_materiales';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'nombre_material',
        'cantidad',
        'cantidad_min',
        'id_unidad',
        'valor_unidad',
        'tipo',
        'descripccion',
        'activo'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->activo] ?? 'default-class').'">'
             . (self::$estado[$this->activo] ?? 'Desconocido') . '</span>';
    }

    public function getClassEstadoAttribute()
    {
        return self::$ClassEstado[$this->activo] ?? 'span-black';
    }
    public function getEstadoAttribute()
    {
        return self::$estado[$this->activo] ?? 'Desconocido';
    }

    public function getTipoMaterialAttribute()
    {
        return self::$tipo[$this->tipo] ?? 'Desconocido';
    }

    public function getUnidadesAttribute()
    {
        return self::$unidades[$this->id_unidad] ?? 'Desconocido';
    }

    public static $estado = [
        1 => 'Activo',
        2 => 'Desactivado'
    ];

    public static $tipo = [
        1 => 'Obra Blanca',
        2 => 'Carpinteria'
    ];

    public static $unidades = [
        1 => 'Unidad',
        2 => 'Metro',
        3 => 'Metro Cuadrado',
        4 => 'Metro Cubico',
        5 => 'Galon',
        6 => 'Litro'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-red'
    ];
}