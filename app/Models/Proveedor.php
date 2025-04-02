<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'inventario_proveedores';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'razon_social',
        'nit',
        'direccion',
        'telefono',
        'activo'
    ];

    public static $estado = [
        1 => 'Activo',
        2 => 'Desactivado'
    ];

    public static $classEstado = [
        1 => 'span-green',
        2 => 'span-red'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$classEstado[$this->activo] ?? 'default-class').'">'
             . (self::$estado[$this->activo] ?? 'Desconocido') . '</span>';
    }

    public function pedidos()
    {
        return $this->hasMany(pedidos::class, 'id_proveedor');
    }
}