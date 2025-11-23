<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioSolicitud extends Model
{
    use HasFactory;

    protected $table = 'inventario_solicituds';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'tipo',
        'codigo',
        'cantidad',
        'valor_unidad',
        'id_material',
        'id_proyecto',
        'id_user',
        'id_solicitud'
    ];

    // Relación con el modelo InventarioMaterial
    public function material()
    {
        return $this->belongsTo(InventarioMaterial::class, 'id_material');
    }

    // Relación con el modelo Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}