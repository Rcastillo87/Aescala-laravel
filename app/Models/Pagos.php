<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    use HasFactory;
    protected $table = 'pagos';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_proyecto',
        'tipo_pago', // 1 = proyecto, 2 = otro sí
        'id_pago',   // id de referencia (otro sí, finanza, etc.)
        'valor_pagado',
        'fecha_pago',
        'concepto',
        'comentario',
        'fv', // factura de venta (manual)
        'rc', // consecutivo
        'id_user',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }
    public function otro_si()
    {
        return $this->belongsTo(Otrosi::class, 'id_pago', 'id');
    }

    public function getValanceAttribute()
    {
        if ($this->tipo_pago != 1) {
            return false;
        }

        $proyecto = $this->proyecto;
        if (!$proyecto) {
            return false;
        }
        $debe = $proyecto->total ?? 0;

        $pagado = self::where([
            'tipo_pago'   => 1,
            'id_proyecto' => $this->id_proyecto,
        ])->sum('valor_pagado');
        return $pagado >= $debe;
    }

    public function getValanceOtroSiAttribute()
    {
        if ($this->tipo_pago != 2) {
            return false;
        }

        $otroSi = $this->otro_si;
        if (!$otroSi) {
            return false;
        }

        $totalDebe = $otroSi->totalDeve ?? 0;
        $pagado = self::where([
            'tipo_pago' => 2,
            'id_pago'   => $this->id_pago,
        ])->sum('valor_pagado');
        return $pagado >= $totalDebe;
    }

    /**
     * Retorna el nombre legible del tipo de pago
     */
    public function getTipoPagoTextoAttribute()
    {
        return match ($this->tipo_pago) {
            1 => 'Proyecto',
            2 => 'Otro Sí',
            default => 'Desconocido',
        };
    }
}
