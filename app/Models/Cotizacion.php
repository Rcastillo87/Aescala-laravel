<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'cantidad',
        'id_material',
        'id_solicitud'
    ];

    // Relación con el modelo InventarioMaterial
    public function material()
    {
        return $this->belongsTo(InventarioMaterial::class, 'id_inventario');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudMaterial::class, 'id_inventario');
    }

}
