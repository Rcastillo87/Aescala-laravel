<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumos extends Model
{
    use HasFactory;

    protected $table = 'insumos';

    protected $fillable = [
        'nombre_insumo',
        'codigo',
        'estado',
        'cantidad',
        'cantidad_min',
        'descripccion'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-red'
    ];

    public static $estado = [
        1 => 'Activo',
        2 => 'Desactivado'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estado[$this->estado] ?? 'Desconocido') . '</span>';
    }

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

    // Relación con el modelo Insumo_entregado
    public function InsumoEntregado()
    {
        return $this->hasMany(InsumoEntregado::class, 'id_insumo');
    }

}
