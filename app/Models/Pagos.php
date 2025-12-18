<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_proyecto',
        'tipo_pago',//1 de proyectos y 2 de otrosi
        'valor_pagado',
        'fecha_pago',
        'concepto',
        'comentario',
        'fv',//se ingresa manualmente
        'rc',//cosecutivo
    ];

    // Relación con el modelo fecha_pago
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function getValanceAttribute()
    {
        $proyecto = $this->proyecto;
        $debe = $proyecto->total ?? 0;
        $pagado = self::where([
            'tipo_pago' => $this->tipo_pago,
            'id_proyecto' => $this->id_proyecto
        ])->sum('valor_pagado');

        return $pagado >= $debe;
    }

}