<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreasEmpresa extends Model
{
    use HasFactory;

    protected $table = 'areas_empresa';
    public $timestamps = false;

    protected $fillable = [
        'nombre_area'
    ];

    // Relación con el modelo Insumo_entregado
    public function InsumoEntregado()
    {
        return $this->hasMany(InsumoEntregado::class, 'id_area_empresa');
    }
}
