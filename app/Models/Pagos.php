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
        'c',
        'tipo',
        'campo_desc',
        'valor_pagado',
        'fecha_pago',
        'comentario'
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
            'tipo' => $this->tipo,
            'id_proyecto' => $this->id_proyecto
        ])->sum('valor_pagado');

        return $pagado >= $debe;
    }

}