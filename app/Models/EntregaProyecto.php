<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaProyecto extends Model
{
    use HasFactory;

    protected $table = 'entregable_proyecto';
    
    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_entregable',
        'id_proyecto',
        'descripccion',
        'cantidad',
        'precio_neto'
    ];

}