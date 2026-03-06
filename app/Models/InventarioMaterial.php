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
        'codigo',
        'cantidad',
        'cantidad_min',
        'id_unidad',
        'valor_unidad',
        'valor_inventario',
        'tipo',
        'descripccion',
        'activo',
        'aprobar',
        'id_proveedor',
        'zona'
    ];

    protected $appends = ['spanTipo', 'unidades'];

    protected $casts = [
        'tipo' => 'integer',
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$classEstado[$this->activo] ?? 'default-class').'">'
             . (self::$estado[$this->activo] ?? 'Desconocido') . '</span>';
    }

    public function getSpanAprobarAttribute()
    {
        return $this->aprobar == 1 ? '<span class="span-green">SI</span>' : '<span class="span-red">NO</span>';
    }

    public function getSpanTipoAttribute()
    {
        return '<span class="'.(self::$classTipo[(int)$this->tipo] ?? 'default-class').'">'
        . (self::$tipo[(int)$this->tipo] ?? 'Desconocido') . '</span>';
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

    public static $zonas = [
        1 => 'Zona 1',
        2 => 'Zona 2',
        3 => 'Zona 3',
        4 => 'Zona 4',
        5 => 'Zona 5',
        6 => 'Zona 6',
        7 => 'Zona 7'
    ];

    public static $tipo = [
        1 => 'Obra Blanca',
        2 => 'Carpinteria'
    ];

    public static $unidades = [
        1 => 'Unid',
        2 => 'MT',
        3 => 'MTx2',
        4 => 'MTx3',
        5 => 'Gal',
        6 => 'Lit',
        7 => 'Par'
    ];

    public static $classEstado = [
        1 => 'span-green',
        2 => 'span-red'
    ];

    public static $classTipo = [
        1 => 'span-green',
        2 => 'span-red'
    ];

    public function getRowBgClassAttribute(): string
    {
        if ($this->cantidad == 0 && $this->cantidad_min != 0) {
            return 'bg-red-100';
        }

        if (
            ($this->cantidad <= $this->cantidad_min && $this->cantidad > 0) ||
            ($this->cantidad_min == 0 && $this->cantidad == 0)
        ) {
            return 'bg-orange-200';
        }

        return '';
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id');
    }
}
