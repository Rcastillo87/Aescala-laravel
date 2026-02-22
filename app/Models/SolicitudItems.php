<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudItems extends Model
{
    use HasFactory;

    protected $table = 'solicitud_items';
    public $timestamps = false;

    protected $appends = ['span_estado'];


    protected $fillable = [
        'id_solicitud',
        'id_material',
        'cantidad',
        'cantidad_solicitada',
        'estado',
        'aprobado',
        'fecha_aprobacion',
        'id_user_aprueba',
        'nota_aprobacion'
    ];

    public static $estados = [
        1 => 'Pendiente Despacho',
        2 => 'Despacho Parcial',
        3 => 'Despachado',
        4 => 'Cancelado'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-yellow',
        3 => 'span-blue',
        4 => 'span-red'
    ];

    public function getSpanEstadoAttribute()
    {
        return implode('', $this->estado());
    }

    public function getArrEstadosAttribute()
    {
        return $this->estado();
    }

    private function estado()
    {
        $span = [];

        if ($this->aprobado == 0) {
            $span[] = '<span class="span-black">Require Aprobacion</span>';
        }

        if ($this->id_user_aprueba && $this->aprobado == 1) {
            $span[] = '<span class="span-orange">Aprobado</span>';
        }

        $span[] = '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estados[$this->estado] ?? 'Desconocido') . '</span>';

        return $span;
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudMaterial::class, 'id_solicitud');
    }

    public function despachado()
    {
        return $this->hasMany(Despachos::class, 'id_solicitud', 'id_solicitud');
    }
    
    public function material()
    {
        return $this->belongsTo(InventarioMaterial::class, 'id_material');
    }

    public function usuario_aprueba()
    {
        return $this->belongsTo(User::class, 'id_user_aprueba');
    }

}