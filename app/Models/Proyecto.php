<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'nombre_proyecto',
        'codigo_proyecto',
        'departamento',
        'ciudad',
        'direccion',
        'nombre_cliente',
        'telefono_cliente',
        'val_obra_blanca',
        'val_obra_blanca_materiales',
        'val_obra_carpinteria',
        'val_carpinteria_materiales',
        'pres_otros',
        'observacion',
        'fec_inicio',
        'fec_fin_estimado',
        'fec_fin_real',
        'id_estado',
        'id_user'
    ];

    protected $casts = [
        'fec_inicio' => 'datetime',
        'fec_fin_estimado' => 'datetime',
        'fec_fin_real' => 'datetime'
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estado[$this->estado] ?? 'Desconocido') . '</span>';
    }

    public function getTotalProyectoAttribute ()
    {
        return $this->val_obra_blanca + $this->val_obra_blanca_materiales + $this->val_obra_carpinteria + $this->val_carpinteria_materiales;
    }

    public static $estado = [
        1 => 'En Desarrollo',
        2 => 'Cotizado',
        3 => 'Entregado',
        4 => 'Cancelado',
        5 => 'Posventas'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-yellow',
        3 => 'span-blue',
        4 => 'span-red',
        5 => 'span-black'
    ];
}